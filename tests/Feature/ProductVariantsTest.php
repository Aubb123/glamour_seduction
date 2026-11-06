<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Produits — Variantes (`product_variants` + pivot `product_variant_attribute_value`).
 *
 * Workflow : le CRUD des variantes vit dans le contexte de la fiche produit.
 * Le produit parent est transporté dans l'URL (?product=uuid) sur create/store
 * et injecté côté serveur — il n'est jamais choisi ni modifié depuis le formulaire.
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function variantAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-variants@site.com', 'gender' => 'female',
        'phone' => '+22903333333', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function variantSetup(): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();

    // Produit parent
    $category = ProductCategory::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $category->translations()->create(['lang_id' => $fr->id, 'name' => 'Cat V '.uniqid(), 'description' => 'Desc.']);

    $brand = ProductBrand::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $brand->translations()->create(['lang_id' => $fr->id, 'name' => 'Marque V '.uniqid(), 'description' => 'Desc.']);

    $product = Product::create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'status' => 'draft',
        'base_price' => 19990,
    ]);
    $product->translations()->create(['lang_id' => $fr->id, 'name' => 'Produit V '.uniqid(), 'short_description' => 'Short.', 'description' => 'Desc.']);

    // Attribut + valeur
    $attribute = ProductAttribute::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $attribute->translations()->create(['lang_id' => $fr->id, 'name' => 'Couleur V '.uniqid(), 'description' => 'Desc.']);

    $value = ProductAttributeValue::create([
        'attribute_id' => $attribute->id,
        'value' => 'rouge-'.uniqid(),
        'sort_order' => 1,
        'status' => 'active',
    ]);
    $value->translations()->create(['lang_id' => $fr->id, 'label' => 'Rouge V '.uniqid()]);

    return ['fr' => $fr, 'product' => $product, 'attribute' => $attribute, 'value' => $value];
}

/** Contexte produit transporté dans l'URL des routes create/store (?product=uuid). */
function variantProductContext(array $setup): array
{
    return ['product' => $setup['product']->uuid];
}

/** URL de la fiche du produit parent — cible des redirections après store/update. */
function variantProductShowUrl(array $setup): string
{
    return route('backend.products.show', [
        'product' => $setup['product']->uuid,
        'slug' => $setup['product']->active_translation?->slug ?? 'null',
    ]);
}

/** Payload du formulaire — SANS product_id : le parent vient de l'URL, jamais du POST. */
function variantPayload(array $setup, array $overrides = []): array
{
    return array_merge([
        'sku' => 'SKU-'.strtoupper(uniqid()),
        'barcode' => 'EAN-'.random_int(100000, 999999),
        'price' => 24990,
        'status' => 'active',
        'is_default' => false,
        'attribute_value_ids' => [$setup['value']->id],
    ], $overrides);
}

it('liste les variantes avec DataTables', function () {
    $admin = variantAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-variants.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-variants-table');
});

it('affiche la page de création de variante depuis la fiche produit', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->get(route('backend.product-variants.create', variantProductContext($setup)))
        ->assertOk();
});

it('refuse la création de variante hors contexte produit (404)', function () {
    $admin = variantAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-variants.create'))
        ->assertNotFound();
});

it('crée une variante (première → is_default auto) et redirige vers la fiche produit / onglet Variantes', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $response = $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));

    $response->assertRedirect()->assertSessionHas('success');

    $location = $response->headers->get('Location');
    expect($location)->toContain(variantProductShowUrl($setup));
    expect($location)->toContain('#tab_variants');

    $variant = ProductVariant::firstOrFail();

    expect(ProductVariant::count())->toBe(1);
    expect($variant->product_id)->toBe($setup['product']->id);
    expect($variant->is_default)->toBeTrue(); // Première variante → par défaut automatiquement
    expect($variant->attributeValues()->count())->toBe(1);
});

it('ignore un product_id posté par le formulaire — le parent vient de l\'URL', function () {
    $admin = variantAdmin();
    $setup = variantSetup();
    $other = variantSetup();

    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['product_id' => $other['product']->id]),
        )
        ->assertRedirect()
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();

    expect($variant->product_id)->toBe($setup['product']->id);
    expect($variant->product_id)->not->toBe($other['product']->id);
});

it('gère un seul is_default par produit (la nouvelle remplace)', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $first = ProductVariant::firstOrFail();

    // Deuxième variante marquée par défaut
    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['sku' => 'SKU-2-'.uniqid(), 'is_default' => true]),
        );

    expect(ProductVariant::count())->toBe(2);
    expect($first->fresh()->is_default)->toBeFalse();
    expect(ProductVariant::where('product_id', $setup['product']->id)->where('is_default', true)->count())->toBe(1);
});

it('refuse un SKU dupliqué', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $payload = variantPayload($setup);
    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), $payload);

    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['sku' => $payload['sku']]),
        )
        ->assertRedirect()
        ->assertSessionHasErrors(['sku']);

    expect(ProductVariant::count())->toBe(1);
});

it('refuse un prix non positif', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['price' => 0]),
        )
        ->assertRedirect()
        ->assertSessionHasErrors(['price']);

    expect(ProductVariant::count())->toBe(0);
});

it('affiche le détail d\'une variante', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.product-variants.show', ['productVariant' => $variant->uuid]))
        ->assertOk();
});

it('met à jour une variante, synchronise ses valeurs d\'attributs et redirige vers l\'onglet Variantes', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    // Deuxième valeur d'attribut pour la synchronisation
    $secondValue = ProductAttributeValue::create([
        'attribute_id' => $setup['attribute']->id,
        'value' => 'noir-'.uniqid(),
        'sort_order' => 2,
        'status' => 'active',
    ]);
    $secondValue->translations()->create(['lang_id' => $setup['fr']->id, 'label' => 'Noir V '.uniqid()]);

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    $payload = variantPayload($setup, [
        'sku' => 'SKU-MAJ-'.uniqid(),
        'attribute_value_ids' => [$secondValue->id],
    ]);

    $response = $this->actingAs($admin)
        ->put(route('backend.product-variants.update', ['productVariant' => $variant->uuid]), $payload);

    $response->assertRedirect()->assertSessionHas('success');

    $location = $response->headers->get('Location');
    expect($location)->toContain(variantProductShowUrl($setup));
    expect($location)->toContain('#tab_variants');

    $variant->refresh();
    expect($variant->sku)->toBe($payload['sku']);
    expect($variant->attributeValues()->count())->toBe(1);
    expect($variant->attributeValues()->first()->id)->toBe($secondValue->id);
});

it('rend le produit parent immuable à l\'édition', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    $other = variantSetup();

    // Même en falsifiant le POST avec un product_id d'un autre produit,
    // la variante ne doit jamais changer de parent.
    $this->actingAs($admin)
        ->put(
            route('backend.product-variants.update', ['productVariant' => $variant->uuid]),
            variantPayload($setup, ['sku' => 'SKU-IMMUTABLE-'.uniqid(), 'product_id' => $other['product']->id]),
        )
        ->assertRedirect()
        ->assertSessionHas('success');

    $variant->refresh();

    expect($variant->product_id)->toBe($setup['product']->id);
    expect($variant->product_id)->not->toBe($other['product']->id);
});

it('active et désactive une variante', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.product-variants.deactivate', ['productVariant' => $variant->uuid]))
        ->assertRedirect();

    expect($variant->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.product-variants.activate', ['productVariant' => $variant->uuid]))
        ->assertRedirect();

    expect($variant->fresh()->status->value)->toBe('active');
});

it('refuse la suppression d\'une variante active puis autorise après désactivation', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    // Active → suppression refusée
    $this->actingAs($admin)
        ->from(route('backend.product-variants.index'))
        ->delete(route('backend.product-variants.destroy', ['productVariant' => $variant->uuid]))
        ->assertRedirect(route('backend.product-variants.index'))
        ->assertSessionHas('error');

    expect(ProductVariant::find($variant->id))->not->toBeNull();

    // Désactivée → suppression autorisée
    $variant->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-variants.destroy', ['productVariant' => $variant->uuid]))
        ->assertRedirect();

    expect(ProductVariant::find($variant->id))->toBeNull();
});

it('passe une variante en corbeille puis la restaure', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();
    $variant->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-variants.destroy', ['productVariant' => $variant->uuid]))
        ->assertRedirect();

    expect(ProductVariant::find($variant->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-variants.trashed'))
        ->patch(route('backend.product-variants.restore', ['uuid' => $variant->uuid]))
        ->assertRedirect();

    expect(ProductVariant::find($variant->id))->not->toBeNull();
});

it('refuse la suppression d\'un produit possédant des variantes (dependentRelations)', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));

    $this->actingAs($admin)
        ->from(route('backend.products.index'))
        ->delete(route('backend.products.destroy', ['product' => $setup['product']->uuid]))
        ->assertRedirect(route('backend.products.index'))
        ->assertSessionHas('error');

    expect(Product::find($setup['product']->id))->not->toBeNull();
});

it('déclare variants comme relation bloquante pour Product (dependentRelations)', function () {
    $product = new Product;

    $property = new ReflectionProperty($product, 'dependentRelations');
    expect($property->getValue($product))->toBe(['variants']);
});

it('déclare stocks/movements comme relations bloquantes pour ProductVariant (modules 8-9)', function () {
    $variant = new ProductVariant;

    $property = new ReflectionProperty($variant, 'dependentRelations');
    expect($property->getValue($variant))->toBe(['stocks', 'movements']);
});

it('affiche, sur la fiche d\'une variante, un lien vers le produit parent', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-variants.show', ['productVariant' => $variant->uuid]))
        ->assertOk()
        ->getContent();

    // Le lien « Produit » de la fiche variante pointe vers la fiche du produit parent
    expect($html)->toContain(route('backend.products.show', [
        'product' => $setup['product']->uuid,
        'slug' => $setup['product']->active_translation?->slug ?? 'null',
    ]));
});

it('isole les variantes du produit courant dans l\'onglet #tab_variants de la fiche produit', function () {
    $admin = variantAdmin();

    // Deux produits distincts, chacun avec sa variante
    $setupA = variantSetup();
    $setupB = variantSetup();

    $skuA = 'SKU-A-'.uniqid();
    $skuB = 'SKU-B-'.uniqid();

    $this->actingAs($admin)->post(
        route('backend.product-variants.store', variantProductContext($setupA)),
        variantPayload($setupA, ['sku' => $skuA]),
    );
    $this->actingAs($admin)->post(
        route('backend.product-variants.store', variantProductContext($setupB)),
        variantPayload($setupB, ['sku' => $skuB]),
    );

    $html = $this->actingAs($admin)
        ->get(variantProductShowUrl($setupA))
        ->assertOk()
        ->getContent();

    // L'onglet Variantes est rendu par le partial partagé…
    expect($html)->toContain('product-variants-table');
    // …et n'affiche QUE les variantes du produit courant
    expect($html)->toContain($skuA);
    expect($html)->not->toContain($skuB);
});
