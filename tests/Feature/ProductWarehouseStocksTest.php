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
use App\Models\ProductWarehouseStock;
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
 * Module Boutique — Stocks par entrepôt (`product_warehouse_stocks`).
 *
 * Un solde courant est enregistré pour une variante dans un entrepôt en mode
 * « warehouse ». Un entrepôt en mode « location » refuse tout stock global.
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

function stockAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'division_id' => $division->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-stocks@site.com', 'gender' => 'female',
        'phone' => '+22904444444', 'password' => 'password',
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
 * Crée un entrepôt (mode « warehouse » par défaut) et une variante de produit.
 *
 * @return array{warehouse: ProductWarehouse, variant: ProductVariant}
 */
function stockSetup(string $trackingMode = 'warehouse'): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $warehouse = ProductWarehouse::create([
        'sort_order' => random_int(1, 1000000000),
        'code' => 'STK-'.strtoupper(substr(uniqid(), -6)),
        'type' => 'storage',
        'stock_tracking_mode' => $trackingMode,
        'division_id' => $division->id,
        'status' => 'active',
        'is_default' => false,
    ]);
    $warehouse->translations()->create(['lang_id' => $fr->id, 'name' => 'Entrepôt '.uniqid(), 'description' => 'Desc.']);

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
        'sku' => 'SKU-'.strtoupper(substr(uniqid(), -6)),
        'status' => 'active',
        'is_default' => false,
    ]);

    return ['warehouse' => $warehouse, 'variant' => $variant];
}

it('crée un stock pour une variante dans un entrepôt en mode warehouse', function () {
    $admin = stockAdmin();
    $setup = stockSetup('warehouse');

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-stocks.store'), [
            'warehouse_id' => $setup['warehouse']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => '12',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $stock = ProductWarehouseStock::firstOrFail();
    expect((int) $stock->quantity)->toBe(12);
    expect($stock->warehouse_id)->toBe($setup['warehouse']->id);
    expect($stock->product_variant_id)->toBe($setup['variant']->id);
});

it('refuse un doublon pour la même combinaison entrepôt + variante', function () {
    $admin = stockAdmin();
    $setup = stockSetup('warehouse');

    ProductWarehouseStock::create([
        'warehouse_id' => $setup['warehouse']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 5,
    ]);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-stocks.create'))
        ->post(route('backend.product-warehouse-stocks.store'), [
            'warehouse_id' => $setup['warehouse']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => 10,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(ProductWarehouseStock::count())->toBe(1);
});

it('rejette une quantité négative', function () {
    $admin = stockAdmin();
    $setup = stockSetup('warehouse');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-stocks.create'))
        ->post(route('backend.product-warehouse-stocks.store'), [
            'warehouse_id' => $setup['warehouse']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => '-5',
        ])
        ->assertSessionHasErrors(['quantity']);

    expect(ProductWarehouseStock::count())->toBe(0);
});

it('refuse de créer un stock global dans un entrepôt en mode location', function () {
    $admin = stockAdmin();
    $setup = stockSetup('location');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-stocks.create'))
        ->post(route('backend.product-warehouse-stocks.store'), [
            'warehouse_id' => $setup['warehouse']->id,
            'product_variant_id' => $setup['variant']->id,
            'quantity' => 10,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(ProductWarehouseStock::count())->toBe(0);
});
it('refuse de modifier un stock dans un entrepôt passé en mode location', function () {
    $admin = stockAdmin();
    $setup = stockSetup('warehouse');

    $stock = ProductWarehouseStock::create([
        'warehouse_id' => $setup['warehouse']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 5,
    ]);

    // Passe l'entrepôt en mode location
    $setup['warehouse']->update(['stock_tracking_mode' => 'location']);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-stocks.edit', ['productWarehouseStock' => $stock->uuid]))
        ->put(route('backend.product-warehouse-stocks.update', ['productWarehouseStock' => $stock->uuid]), [
            'quantity' => 8,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect((int) $stock->fresh()->quantity)->toBe(5);
});

it('liste les stocks avec la variante et l\'entrepôt', function () {
    $admin = stockAdmin();
    $setup = stockSetup('warehouse');

    ProductWarehouseStock::create([
        'warehouse_id' => $setup['warehouse']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 7,
    ]);

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouse-stocks.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouse-stocks-table');
    expect($html)->toContain($setup['variant']->sku);
});

it('respecte les droits par rôle sur les actions sensibles', function () {
    $admin = stockAdmin();
    $setup = stockSetup('warehouse');

    $stock = ProductWarehouseStock::create([
        'warehouse_id' => $setup['warehouse']->id,
        'product_variant_id' => $setup['variant']->id,
        'quantity' => 5,
    ]);

    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $moderator = User::create([
        'country_id' => $country->id,
        'division_id' => $division->id,
        'first_name' => 'Marc', 'last_name' => 'Dupont',
        'email' => 'mod-stocks@site.com', 'gender' => 'male',
        'phone' => '+22905555555', 'password' => 'password',
        'status' => 'active',
    ]);
    $moderator->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();
    $moderator->assignRole('moderator');

    // Lecture autorisée
    $this->actingAs($moderator)->get(route('backend.product-warehouse-stocks.index'))->assertOk();

    // Actions sensibles interdites
    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouse-stocks.destroy', ['productWarehouseStock' => $stock->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->get(route('backend.product-warehouse-stocks.create'))
        ->assertForbidden();
});
