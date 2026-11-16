<?php

declare(strict_types=1);

use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\V1\Controllers\ProductVariantService;
use Database\Seeders\CloudFolderSeeder;
use Database\Seeders\CloudPlatformSeeder;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

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

/** Dossier cloud cible des images de test (nécessite CloudPlatformSeeder + CloudFolderSeeder). */
function variantCloudFolder(): CloudFolder
{
    return CloudFolder::whereNotNull('entity_table')->firstOrFail();
}

/** Attache une image factice à une variante (le dossier doit avoir été semé au préalable). */
function variantImageFixture(ProductVariant $variant, CloudFolder $folder, int $page = 1): CloudFile
{
    return CloudFile::create([
        'cloud_folder_id' => $folder->id,
        'fileable_type' => $variant->getMorphClass(),
        'fileable_id' => $variant->id,
        'file_name' => 'variant-'.$page.'.jpg',
        'file_type' => 'image',
        'mime_type' => 'image/jpeg',
        'file_size' => 1024,
        'sync_status' => 'pending',
        'metadata' => ['page' => $page],
    ]);
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

    // Désigne une autre variante par défaut : la variante ciblée ne sera plus le dernier défaut.
    $this->actingAs($admin)->post(
        route('backend.product-variants.store', variantProductContext($setup)),
        variantPayload($setup, ['sku' => 'SKU-SECOND-'.uniqid(), 'is_default' => true, 'attribute_value_ids' => []]),
    );

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

    // Désigne une autre variante par défaut : une variante par défaut ne peut plus être supprimée.
    $this->actingAs($admin)->post(
        route('backend.product-variants.store', variantProductContext($setup)),
        variantPayload($setup, ['sku' => 'SKU-SECOND-'.uniqid(), 'is_default' => true, 'attribute_value_ids' => []]),
    );

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

it('refuse un code-barres dupliqué au store et ignore la variante courante à l\'update', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $barcode = 'EAN-'.random_int(100000, 999999);

    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['sku' => 'SKU-BAR-1-'.uniqid(), 'barcode' => $barcode]),
        )
        ->assertSessionHas('success');

    $first = ProductVariant::firstOrFail();

    // Deuxième variante avec le même code-barres → refusée.
    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['sku' => 'SKU-BAR-2-'.uniqid(), 'barcode' => $barcode, 'attribute_value_ids' => []]),
        )
        ->assertSessionHasErrors(['barcode']);

    expect(ProductVariant::count())->toBe(1);

    // Une variante peut conserver son propre code-barres lors de la mise à jour (règle ignore).
    $this->actingAs($admin)
        ->put(
            route('backend.product-variants.update', ['productVariant' => $first->uuid]),
            ['sku' => $first->sku, 'barcode' => $barcode, 'status' => 'active'],
        )
        ->assertSessionHas('success');

    expect($first->fresh()->barcode)->toBe($barcode);
});

it('refuse une combinaison de valeurs d\'attributs déjà utilisée par une variante du produit', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['sku' => 'SKU-COMBO-1-'.uniqid()]),
        )
        ->assertSessionHas('success');

    // Même combinaison → refusée au store.
    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['sku' => 'SKU-COMBO-2-'.uniqid()]),
        )
        ->assertSessionHasErrors(['attribute_value_ids']);

    expect(ProductVariant::count())->toBe(1);

    // Une variante sans options reste autorisée (ensemble vide = pas un doublon).
    $this->actingAs($admin)
        ->post(
            route('backend.product-variants.store', variantProductContext($setup)),
            variantPayload($setup, ['sku' => 'SKU-COMBO-3-'.uniqid(), 'attribute_value_ids' => []]),
        )
        ->assertSessionHas('success');

    $second = ProductVariant::where('sku', 'like', 'SKU-COMBO-3-%')->firstOrFail();

    // À l'update, reprendre la combinaison de la première variante → refusé.
    $this->actingAs($admin)
        ->put(
            route('backend.product-variants.update', ['productVariant' => $second->uuid]),
            ['attribute_value_ids' => [$setup['value']->id]],
        )
        ->assertSessionHasErrors(['attribute_value_ids']);

    // Conserver sa propre combinaison (vide) reste permis grâce à l'exclusion de la variante courante.
    $this->actingAs($admin)
        ->put(
            route('backend.product-variants.update', ['productVariant' => $second->uuid]),
            ['attribute_value_ids' => []],
        )
        ->assertSessionHas('success');
});

it('garantit qu\'un produit conserve toujours au moins une variante par défaut', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup))
        ->assertSessionHas('success');

    $defaultVariant = ProductVariant::firstOrFail();
    expect($defaultVariant->is_default)->toBeTrue();

    // Retirer le statut « par défaut » à la dernière variante par défaut → refusé.
    $this->actingAs($admin)
        ->from(route('backend.product-variants.show', ['productVariant' => $defaultVariant->uuid]))
        ->put(route('backend.product-variants.update', ['productVariant' => $defaultVariant->uuid]), ['is_default' => false])
        ->assertSessionHas('error');

    expect($defaultVariant->fresh()->is_default)->toBeTrue();

    // Une variante par défaut ne peut pas être mise en corbeille, même inactive.
    $defaultVariant->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->from(route('backend.product-variants.index'))
        ->delete(route('backend.product-variants.destroy', ['productVariant' => $defaultVariant->uuid]))
        ->assertSessionHas('error');

    expect(ProductVariant::find($defaultVariant->id))->not->toBeNull();

    // Dès qu'une autre variante devient par défaut, la suppression redevient possible.
    $this->actingAs($admin)->post(
        route('backend.product-variants.store', variantProductContext($setup)),
        variantPayload($setup, ['sku' => 'SKU-NEW-DEFAULT-'.uniqid(), 'is_default' => true, 'attribute_value_ids' => []]),
    )->assertSessionHas('success');

    $this->actingAs($admin)
        ->delete(route('backend.product-variants.destroy', ['productVariant' => $defaultVariant->uuid]))
        ->assertSessionHas('success');

    expect(ProductVariant::find($defaultVariant->id))->toBeNull();
});

it('applique les règles force_destroy (non corbeille, active, puis suppression définitive)', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup))
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();
    $variant->update(['status' => 'inactive', 'is_default' => false]);

    // Variante non mise en corbeille → refus au niveau du service.
    $service = app(ProductVariantService::class);
    expect(fn () => $service->backend_product_variants_force_destroy($variant))
        ->toThrow(InvalidArgumentException::class);

    // En corbeille mais encore active → refus.
    $variant->update(['status' => 'active']);
    $variant->delete();

    $this->actingAs($admin)
        ->from(route('backend.product-variants.trashed'))
        ->delete(route('backend.product-variants.force-destroy', ['uuid' => $variant->uuid]))
        ->assertSessionHas('error');

    expect(ProductVariant::onlyTrashed()->find($variant->id))->not->toBeNull();

    // En corbeille et inactive → suppression définitive.
    $variant->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-variants.force-destroy', ['uuid' => $variant->uuid]))
        ->assertSessionHas('success');

    expect(ProductVariant::withTrashed()->find($variant->id))->toBeNull();
});

it('supprime une image via remove_cloud_file_uuids et refuse un fichier étranger', function () {
    $admin = variantAdmin();
    $setup = variantSetup();
    $this->seed(CloudPlatformSeeder::class);
    $this->seed(CloudFolderSeeder::class);

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup))
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();
    $folder = variantCloudFolder();

    $ownImage = variantImageFixture($variant, $folder, 1);

    // Image rattachée à une autre variante du même produit.
    $otherVariant = ProductVariant::create([
        'product_id' => $setup['product']->id,
        'sku' => 'SKU-OTHER-'.uniqid(),
        'status' => 'inactive',
        'is_default' => false,
    ]);
    $foreignImage = variantImageFixture($otherVariant, $folder, 1);

    // UUID étranger → refusé, aucune image supprimée.
    $this->actingAs($admin)
        ->put(
            route('backend.product-variants.update', ['productVariant' => $variant->uuid]),
            ['remove_cloud_file_uuids' => [$foreignImage->uuid]],
        )
        ->assertSessionHas('error');

    expect(CloudFile::find($ownImage->id))->not->toBeNull();

    // Image appartenant à la variante → supprimée.
    $this->actingAs($admin)
        ->put(
            route('backend.product-variants.update', ['productVariant' => $variant->uuid]),
            ['remove_cloud_file_uuids' => [$ownImage->uuid]],
        )
        ->assertSessionHas('success');

    expect(CloudFile::find($ownImage->id))->toBeNull();
    expect(CloudFile::find($foreignImage->id))->not->toBeNull();
});

it('refuse de supprimer l\'image d\'une autre variante via destroy_file et supprime la sienne', function () {
    $admin = variantAdmin();
    $setup = variantSetup();
    $this->seed(CloudPlatformSeeder::class);
    $this->seed(CloudFolderSeeder::class);

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup))
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();
    $folder = variantCloudFolder();

    $ownImage = variantImageFixture($variant, $folder, 1);

    $otherVariant = ProductVariant::create([
        'product_id' => $setup['product']->id,
        'sku' => 'SKU-FOREIGN-'.uniqid(),
        'status' => 'inactive',
        'is_default' => false,
    ]);
    $foreignImage = variantImageFixture($otherVariant, $folder, 1);

    // Image d'une autre variante → refus.
    $this->actingAs($admin)
        ->from(route('backend.product-variants.show', ['productVariant' => $variant->uuid]))
        ->delete(route('backend.product-variants.destroy_file', [
            'productVariant' => $variant->uuid,
            'cloudFile' => $foreignImage->uuid,
        ]))
        ->assertSessionHas('error');

    expect(CloudFile::find($foreignImage->id))->not->toBeNull();

    // Image de la variante → supprimée.
    $this->actingAs($admin)
        ->delete(route('backend.product-variants.destroy_file', [
            'productVariant' => $variant->uuid,
            'cloudFile' => $ownImage->uuid,
        ]))
        ->assertSessionHas('success');

    expect(CloudFile::find($ownImage->id))->toBeNull();
});

it('refuse d\'ajouter une image au-delà de quatre à l\'update', function () {
    $admin = variantAdmin();
    $setup = variantSetup();
    $this->seed(CloudPlatformSeeder::class);
    $this->seed(CloudFolderSeeder::class);

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup))
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();
    $folder = variantCloudFolder();

    foreach ([1, 2, 3, 4] as $page) {
        variantImageFixture($variant, $folder, $page);
    }

    // 4 images existantes + 3 nouvelles − 2 retirées = 5 → dépassement.
    $service = app(ProductVariantService::class);

    $images = $variant->cloudFiles()->where('file_type', 'image')->orderBy('id')->get();

    expect(fn () => $service->backend_product_variants_update($variant, [
        'cloud_platform_uuid' => CloudPlatform::firstOrFail()->uuid,
        'remove_cloud_file_uuids' => [$images[0]->uuid, $images[1]->uuid],
        'files' => [
            UploadedFile::fake()->create('extra-1.jpg', 10, 'image/jpeg'),
            UploadedFile::fake()->create('extra-2.jpg', 10, 'image/jpeg'),
            UploadedFile::fake()->create('extra-3.jpg', 10, 'image/jpeg'),
        ],
    ]))->toThrow(InvalidArgumentException::class);

    expect($variant->cloudFiles()->where('file_type', 'image')->count())->toBe(4);
});

it('génère automatiquement le SKU et le code-barres EAN-13 quand ils sont laissés vides', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $payload = variantPayload($setup);
    unset($payload['sku'], $payload['barcode']);

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), $payload)
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();

    // SKU lisible : produit + valeurs d'attributs, en majuscules ASCII.
    expect($variant->sku)->not->toBe('')
        ->and($variant->sku)->toMatch('/^[A-Z0-9]+(-[A-Z0-9]+)*$/');

    // Code-barres EAN-13 (13 chiffres) avec clé de contrôle valide.
    expect($variant->barcode)->toMatch('/^\d{13}$/');

    $digits = substr($variant->barcode, 0, 12);
    $sum = 0;
    foreach (str_split($digits) as $index => $digit) {
        $sum += (int) $digit * ($index % 2 === 0 ? 1 : 3);
    }

    expect((string) ((10 - ($sum % 10)) % 10))->toBe(substr($variant->barcode, -1));
});

it('conserve la référence SKU existante quand le champ est vidé à l\'update', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $payload = variantPayload($setup);
    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), $payload)
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();
    $originalSku = $variant->sku;

    $this->actingAs($admin)
        ->put(
            route('backend.product-variants.update', ['productVariant' => $variant->uuid]),
            ['sku' => '', 'status' => 'active'],
        )
        ->assertSessionHas('success');

    expect($variant->fresh()->sku)->toBe($originalSku);
});

it('affiche le nombre de variantes sur la liste des produits avec un lien vers la page variantes du produit', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup))
        ->assertSessionHas('success');

    $html = $this->actingAs($admin)
        ->get(route('backend.products.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain(route('backend.products.variants', [
        'product' => $setup['product']->uuid,
        'slug' => $setup['product']->active_translation?->slug ?? 'null',
    ]));
});

it('affiche la page variantes d\'un produit avec le tableau des variantes', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $sku = 'SKU-PRODUCT-VARIANTS-'.uniqid();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store', variantProductContext($setup)), variantPayload($setup, ['sku' => $sku]))
        ->assertSessionHas('success');

    $html = $this->actingAs($admin)
        ->get(route('backend.products.variants', [
            'product' => $setup['product']->uuid,
            'slug' => $setup['product']->active_translation?->slug ?? 'null',
        ]))
        ->assertOk()
        ->getContent();

    expect($html)->toContain($sku)
        ->and($html)->toContain('product-variants-table');
});
