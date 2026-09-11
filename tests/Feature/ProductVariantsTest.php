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

function variantPayload(array $setup, array $overrides = []): array
{
    return array_merge([
        'product_id' => $setup['product']->id,
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

it('affiche la page de création de variante', function () {
    $admin = variantAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-variants.create'))
        ->assertOk();
});

it('crée une variante (première → is_default auto) avec ses valeurs d\'attributs', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store'), variantPayload($setup))
        ->assertRedirect()
        ->assertSessionHas('success');

    $variant = ProductVariant::firstOrFail();

    expect(ProductVariant::count())->toBe(1);
    expect($variant->is_default)->toBeTrue(); // Première variante → par défaut automatiquement
    expect($variant->attributeValues()->count())->toBe(1);
});

it('gère un seul is_default par produit (la nouvelle remplace)', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup));
    $first = ProductVariant::firstOrFail();

    // Deuxième variante marquée par défaut
    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup, ['sku' => 'SKU-2-'.uniqid(), 'is_default' => true]));

    expect(ProductVariant::count())->toBe(2);
    expect($first->fresh()->is_default)->toBeFalse();
    expect(ProductVariant::where('product_id', $setup['product']->id)->where('is_default', true)->count())->toBe(1);
});

it('refuse un SKU dupliqué', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $payload = variantPayload($setup);
    $this->actingAs($admin)->post(route('backend.product-variants.store'), $payload);

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store'), variantPayload($setup, ['sku' => $payload['sku']]))
        ->assertRedirect()
        ->assertSessionHasErrors(['sku']);

    expect(ProductVariant::count())->toBe(1);
});

it('refuse un prix non positif', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)
        ->post(route('backend.product-variants.store'), variantPayload($setup, ['price' => 0]))
        ->assertRedirect()
        ->assertSessionHasErrors(['price']);

    expect(ProductVariant::count())->toBe(0);
});
it('affiche le détail d\'une variante', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.product-variants.show', ['productVariant' => $variant->uuid]))
        ->assertOk();
});

it('met à jour une variante et synchronise ses valeurs d\'attributs', function () {
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

    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup));
    $variant = ProductVariant::firstOrFail();

    $payload = variantPayload($setup, [
        'sku' => 'SKU-MAJ-'.uniqid(),
        'attribute_value_ids' => [$secondValue->id],
    ]);

    $this->actingAs($admin)
        ->put(route('backend.product-variants.update', ['productVariant' => $variant->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $variant->refresh();
    expect($variant->sku)->toBe($payload['sku']);
    expect($variant->attributeValues()->count())->toBe(1);
    expect($variant->attributeValues()->first()->id)->toBe($secondValue->id);
});

it('active et désactive une variante', function () {
    $admin = variantAdmin();
    $setup = variantSetup();

    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup));
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

    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup));
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

    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup));
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

    $this->actingAs($admin)->post(route('backend.product-variants.store'), variantPayload($setup));

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