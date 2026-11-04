<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseStock;
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
 * Module Produits — Stocks par entrepôt / variante (`product-warehouse-stocks`).
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

function stockAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-stocks@site.com', 'gender' => 'female',
        'phone' => '+22904444445', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

/** Crée un entrepôt + une variante liée à un produit minimal, pour les tests de stocks. */
function stockFixtures(): array
{
    $warehouse = ProductWarehouse::create([
        'code' => 'WH-'.strtoupper(uniqid()),
        'name' => 'Entrepôt de test',
        'city' => 'Cotonou',
        'status' => 'active',
        'is_default' => true,
    ]);

    $category = ProductCategory::create(['sort_order' => random_int(1, 999), 'status' => 'active']);

    $product = Product::create([
        'category_id' => $category->id,
        'base_price' => 5000,
        'status' => 'published',
    ]);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'SKU-'.strtoupper(uniqid()),
        'status' => 'active',
        'is_default' => true,
    ]);

    return ['warehouse' => $warehouse, 'variant' => $variant, 'product' => $product];
}

function stockPayload(array $fixtures, array $overrides = []): array
{
    return array_merge([
        'warehouse_id' => $fixtures['warehouse']->id,
        'product_variant_id' => $fixtures['variant']->id,
        'quantity' => 100,
        'reserved_quantity' => 0,
        'low_stock_threshold' => 5,
    ], $overrides);
}

it('liste les stocks avec DataTables', function () {
    $admin = stockAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouse-stocks.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouse-stocks-table');
});

it('affiche la page de création d\'un stock', function () {
    $admin = stockAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouse-stocks.create'))
        ->assertOk();
});

it('crée un stock (entrepôt × variante)', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures))
        ->assertRedirect()
        ->assertSessionHas('success');

    $stock = ProductWarehouseStock::firstOrFail();

    expect(ProductWarehouseStock::count())->toBe(1);
    expect($stock->warehouse_id)->toBe($fixtures['warehouse']->id);
    expect($stock->variant->id)->toBe($fixtures['variant']->id);
    expect($stock->quantity)->toBe(100);
    expect($stock->reserved_quantity)->toBe(0);
    expect($stock->availableQuantity())->toBe(100);
});

it('refuse un couple (entrepôt, variante) dupliqué', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures));

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures))
        ->assertRedirect()
        ->assertSessionHas('error'); // Garde métier : un stock existe déjà pour ce couple

    expect(ProductWarehouseStock::count())->toBe(1);
});

it('refuse une quantité réservée supérieure à la quantité en stock', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures, ['quantity' => 10, 'reserved_quantity' => 25]))
        ->assertRedirect()
        ->assertSessionHasErrors(['reserved_quantity']);

    expect(ProductWarehouseStock::count())->toBe(0);
});

it('met à jour un stock (seuil d\'alerte et quantité)', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures));
    $stock = ProductWarehouseStock::firstOrFail();

    $this->actingAs($admin)
        ->put(route('backend.product-warehouse-stocks.update', ['productWarehouseStock' => $stock->uuid]), stockPayload($fixtures, ['quantity' => 50, 'low_stock_threshold' => 10]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $stock->refresh();
    expect($stock->quantity)->toBe(50);
    expect($stock->low_stock_threshold)->toBe(10);
});

it('refuse la mise à jour vers un couple (entrepôt, variante) déjà pris', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures));
    $stock = ProductWarehouseStock::firstOrFail();

    // Second entrepôt pour créer un conflit de couple
    $otherWarehouse = ProductWarehouse::create([
        'code' => 'WH-'.strtoupper(uniqid()),
        'name' => 'Autre entrepôt',
        'status' => 'active',
        'is_default' => false,
    ]);

    ProductWarehouseStock::create([
        'warehouse_id' => $otherWarehouse->id,
        'product_variant_id' => $fixtures['variant']->id,
        'quantity' => 20,
    ]);

    // Tente de faire pointer le premier stock sur le couple déjà utilisé par le second
    $this->actingAs($admin)
        ->put(route('backend.product-warehouse-stocks.update', ['productWarehouseStock' => $stock->uuid]), [
            'warehouse_id' => $otherWarehouse->id,
            'product_variant_id' => $fixtures['variant']->id,
            'quantity' => 30,
            'reserved_quantity' => 0,
            'low_stock_threshold' => 5,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($stock->fresh()->warehouse_id)->toBe($fixtures['warehouse']->id);
});

it('passe un stock en corbeille puis le restaure et le supprime définitivement', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures));
    $stock = ProductWarehouseStock::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-stocks.destroy', ['productWarehouseStock' => $stock->uuid]))
        ->assertRedirect();

    expect(ProductWarehouseStock::find($stock->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-stocks.trashed'))
        ->patch(route('backend.product-warehouse-stocks.restore', ['uuid' => $stock->uuid]))
        ->assertRedirect();

    expect(ProductWarehouseStock::find($stock->id))->not->toBeNull();

    // Suppression définitive : corbeille d'abord puis force destroy
    $stock->delete();

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-stocks.force-destroy', ['uuid' => $stock->uuid]))
        ->assertRedirect();

    expect(ProductWarehouseStock::withTrashed()->find($stock->id))->toBeNull();
});

it('affiche la corbeille des stocks', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures));
    $stock = ProductWarehouseStock::firstOrFail();
    $stock->delete();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouse-stocks.trashed'))
        ->assertOk();
});

it('affiche le détail, les audits et les journaux d\'activité d\'un stock', function () {
    $admin = stockAdmin();
    $fixtures = stockFixtures();

    $this->actingAs($admin)->post(route('backend.product-warehouse-stocks.store'), stockPayload($fixtures));
    $stock = ProductWarehouseStock::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $stock->uuid]))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouse-stocks.audits', ['productWarehouseStock' => $stock->uuid]))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouse-stocks.activity-logs', ['productWarehouseStock' => $stock->uuid]))
        ->assertOk();
});

it('refuse l\'accès aux stocks sans permission', function () {
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $user = User::create([
        'country_id' => $country->id,
        'first_name' => 'Bob', 'last_name' => 'User',
        'email' => 'user-stocks@site.com', 'gender' => 'male',
        'phone' => '+22904444446', 'password' => 'password',
        'status' => 'active',
    ]);

    $user->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();

    $this->actingAs($user)
        ->get(route('backend.product-warehouse-stocks.index'))
        ->assertForbidden();
});

it('déclare dependentRelations = [stocks, movements] pour ProductVariant (modules 8-9)', function () {
    $variant = new ProductVariant;

    $property = new ReflectionProperty($variant, 'dependentRelations');
    expect($property->getValue($variant))->toBe(['stocks', 'movements']);
});
