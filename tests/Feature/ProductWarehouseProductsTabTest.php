<?php

declare(strict_types=1);

use App\Models\GeoCountry;
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
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Produits — Onglet « Produits » de la fiche entrepôt.
 *
 * Vérifie que la fiche d'un entrepôt expose la liste des produits stockés
 * (produit + variante/SKU + quantité) via la relation
 * ProductWarehouse::stocks() → ProductWarehouseStock::variant() → product.
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

function warehouseProductsAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-wh-products@site.com', 'gender' => 'female',
        'phone' => '+22907777777', 'password' => 'password',
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
 * Crée un entrepôt + un produit + une variante + une ligne de stock,
 * et renvoie les entités créées.
 */
function warehouseProductsSetup(int $quantity = 42, int $reserved = 2): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();

    // Entrepôt
    $warehouse = ProductWarehouse::create([
        'code' => 'WH-PROD-'.strtoupper(uniqid()),
        'status' => 'active',
    ]);

    // Produit + catégorie + marque
    $category = ProductCategory::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $category->translations()->create(['lang_id' => $fr->id, 'name' => 'Cat WH '.uniqid(), 'description' => 'Desc.']);

    $brand = ProductBrand::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $brand->translations()->create(['lang_id' => $fr->id, 'name' => 'Marque WH '.uniqid(), 'description' => 'Desc.']);

    $product = Product::create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'status' => 'draft',
        'base_price' => 19990,
    ]);
    $productName = 'Produit WH '.uniqid();
    $product->translations()->create(['lang_id' => $fr->id, 'name' => $productName, 'short_description' => 'Short.', 'description' => 'Desc.']);

    // Variante
    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'SKU-WH-'.strtoupper(uniqid()),
        'status' => 'active',
        'is_default' => true,
    ]);

    // Ligne de stock
    $stock = ProductWarehouseStock::create([
        'warehouse_id' => $warehouse->id,
        'product_variant_id' => $variant->id,
        'quantity' => $quantity,
        'reserved_quantity' => $reserved,
        'low_stock_threshold' => 5,
    ]);

    return [
        'warehouse' => $warehouse,
        'product' => $product,
        'product_name' => $productName,
        'variant' => $variant,
        'stock' => $stock,
    ];
}

it('affiche un onglet Produits sur la fiche entrepôt', function () {
    $admin = warehouseProductsAdmin();
    $setup = warehouseProductsSetup();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $setup['warehouse']->uuid]))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('tab_products');
});

it('liste les produits stockés avec leur SKU et leur quantité', function () {
    $admin = warehouseProductsAdmin();
    $setup = warehouseProductsSetup(quantity: 42, reserved: 2);

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $setup['warehouse']->uuid]))
        ->assertOk()
        ->getContent();

    // Nom du produit + SKU de la variante + quantité physique présents
    expect($html)->toContain($setup['product_name']);
    expect($html)->toContain($setup['variant']->sku);
    expect($html)->toContain('42');
    // Quantité disponible = 42 - 2 = 40
    expect($html)->toContain('40');
});

it('n\'affiche le produit que dans l\'entrepôt où il est stocké', function () {
    $admin = warehouseProductsAdmin();
    $setup = warehouseProductsSetup();

    // Un second entrepôt sans stock de ce produit
    $otherWarehouse = ProductWarehouse::create([
        'code' => 'WH-EMPTY-'.strtoupper(uniqid()),
        'status' => 'active',
    ]);

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $otherWarehouse->uuid]))
        ->assertOk()
        ->getContent();

    expect($html)->not->toContain($setup['product_name']);
});
