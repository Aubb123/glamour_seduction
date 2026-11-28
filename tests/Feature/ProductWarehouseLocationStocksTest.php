<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\GeoDivision;
use App\Models\Lang;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationStock;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\GeoDivisionSeeder;
use Database\Seeders\GeoDivisionTypeSeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Boutique — Stocks par emplacement (`product_warehouse_location_stocks`).
 *
 * Un solde courant est enregistré pour une variante dans un emplacement dont
 * l'entrepôt parent est en mode « location ». Un entrepôt en mode « warehouse »
 * refuse tout stock par emplacement.
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(SettingSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(GeoDivisionTypeSeeder::class);
    $this->seed(GeoDivisionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function locationStockAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'division_id' => $division->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-loc-stocks@site.com', 'gender' => 'female',
        'phone' => '+22906666666', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

/**
 * Crée un entrepôt (mode « location » par défaut), un emplacement et une variante.
 *
 * @return array{warehouse: ProductWarehouse, location: ProductWarehouseLocation, variant: ProductVariant}
 */
function locationStockSetup(string $trackingMode = 'location'): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $warehouse = ProductWarehouse::create([
        'sort_order' => random_int(1, 1000000000),
        'code' => 'LST-'.strtoupper(substr(uniqid(), -6)),
        'type' => 'storage',
        'stock_tracking_mode' => $trackingMode,
        'division_id' => $division->id,
        'status' => 'active',
        'is_default' => false,
    ]);
    $warehouse->translations()->create(['lang_id' => $fr->id, 'name' => 'Entrepôt '.uniqid(), 'description' => 'Desc.']);

    $location = ProductWarehouseLocation::create([
        'warehouse_id' => $warehouse->id,
        'type' => 'shelf',
        'code' => 'LOC-'.strtoupper(substr(uniqid(), -6)),
        'sort_order' => 1,
        'status' => 'active',
    ]);
    $location->translations()->create(['lang_id' => $fr->id, 'name' => 'Emplacement '.uniqid(), 'description' => 'Desc.']);

    $category = ProductCategory::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $category->translations()->create(['lang_id' => $fr->id, 'name' => 'Cat '.uniqid(), 'description' => 'Desc.']);

    $brand = ProductBrand::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $brand->translations()->create(['lang_id' => $fr->id, 'name' => 'Marque '.uniqid(), 'description' => 'Desc.']);

    $product = Product::create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'status' => 'draft',
        'base_price' => 19990,
    ]);
    $product->translations()->create(['lang_id' => $fr->id, 'name' => 'Produit '.uniqid(), 'short_description' => 'Short.', 'description' => 'Desc.']);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'LSKU-'.strtoupper(substr(uniqid(), -6)),
        'status' => 'active',
        'is_default' => false,
    ]);

    return ['warehouse' => $warehouse, 'location' => $location, 'variant' => $variant];
}
it('crée un stock dans un emplacement d\'un entrepôt en mode location', function () {
    $admin = locationStockAdmin();
    $setup = locationStockSetup('location');

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-location-stocks.store'), [
            'location_id' => $setup['location']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => '8',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $stock = ProductWarehouseLocationStock::firstOrFail();
    expect((int) $stock->quantity)->toBe(8);
    expect($stock->location_id)->toBe($setup['location']->id);
    expect($stock->product_variant_id)->toBe($setup['variant']->id);
});

it('refuse de créer un stock par emplacement pour un entrepôt en mode warehouse', function () {
    $admin = locationStockAdmin();
    $setup = locationStockSetup('warehouse');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-location-stocks.create'))
        ->post(route('backend.product-warehouse-location-stocks.store'), [
            'location_id' => $setup['location']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => 10,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(ProductWarehouseLocationStock::count())->toBe(0);
});

it('refuse un doublon pour la même combinaison emplacement + variante', function () {
    $admin = locationStockAdmin();
    $setup = locationStockSetup('location');

    ProductWarehouseLocationStock::create([
        'location_id' => $setup['location']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 5,
    ]);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-location-stocks.create'))
        ->post(route('backend.product-warehouse-location-stocks.store'), [
            'location_id' => $setup['location']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => 10,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(ProductWarehouseLocationStock::count())->toBe(1);
});

it('rejette une quantité négative', function () {
    $admin = locationStockAdmin();
    $setup = locationStockSetup('location');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-location-stocks.create'))
        ->post(route('backend.product-warehouse-location-stocks.store'), [
            'location_id' => $setup['location']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => '-3',
        ])
        ->assertSessionHasErrors(['quantity']);

    expect(ProductWarehouseLocationStock::count())->toBe(0);
});

it('refuse de modifier un stock quand l\'entrepôt passe en mode warehouse', function () {
    $admin = locationStockAdmin();
    $setup = locationStockSetup('location');

    $stock = ProductWarehouseLocationStock::create([
        'location_id' => $setup['location']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 5,
    ]);

    // Passe l'entrepôt en mode warehouse
    $setup['warehouse']->update(['stock_tracking_mode' => 'warehouse']);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-location-stocks.edit', ['productWarehouseLocationStock' => $stock->uuid]))
        ->put(route('backend.product-warehouse-location-stocks.update', ['productWarehouseLocationStock' => $stock->uuid]), [
            'quantity' => 8,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect((int) $stock->fresh()->quantity)->toBe(5);
});

it('liste les stocks par emplacement avec la variante et l\'emplacement', function () {
    $admin = locationStockAdmin();
    $setup = locationStockSetup('location');

    ProductWarehouseLocationStock::create([
        'location_id' => $setup['location']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 7,
    ]);

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouse-location-stocks.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouse-location-stocks-table');
    expect($html)->toContain($setup['variant']->sku);
    expect($html)->toContain($setup['location']->code);
});

it('respecte les droits par rôle sur les actions sensibles', function () {
    $admin = locationStockAdmin();
    $setup = locationStockSetup('location');

    $stock = ProductWarehouseLocationStock::create([
        'location_id' => $setup['location']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 5,
    ]);

    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $moderator = User::create([
        'country_id' => $country->id,
        'division_id' => $division->id,
        'first_name' => 'Marc', 'last_name' => 'Dupont',
        'email' => 'mod-loc-stocks@site.com', 'gender' => 'male',
        'phone' => '+22907777777', 'password' => 'password',
        'status' => 'active',
    ]);
    $moderator->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();
    $moderator->assignRole('moderator');

    // Lecture autorisée
    $this->actingAs($moderator)->get(route('backend.product-warehouse-location-stocks.index'))->assertOk();

    // Actions sensibles interdites
    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouse-location-stocks.destroy', ['productWarehouseLocationStock' => $stock->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->get(route('backend.product-warehouse-location-stocks.create'))
        ->assertForbidden();
});
