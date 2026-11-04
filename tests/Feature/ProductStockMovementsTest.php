<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStockMovement;
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
 * Module Produits — Mouvements de stock (`product_stock_movements`, audit & traçabilité).
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

function movementAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-movements@site.com', 'gender' => 'female',
        'phone' => '+22904444447', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

/** Crée un entrepôt + une variante liée à un produit minimal, pour les tests de mouvements. */
function movementFixtures(): array
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

function movementPayload(array $fixtures, array $overrides = []): array
{
    return array_merge([
        'warehouse_id' => $fixtures['warehouse']->id,
        'product_variant_id' => $fixtures['variant']->id,
        'type' => 'in',
        'quantity' => 50,
        'reference_type' => 'order',
        'reference_id' => 123,
        'note' => 'Réassort de test',
    ], $overrides);
}

it('liste les mouvements de stock avec DataTables et filtres', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    // Deux mouvements pour vérifier l'index
    $this->actingAs($admin)->post(route('backend.product-stock-movements.store'), movementPayload($fixtures));
    $this->actingAs($admin)->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['type' => 'out', 'quantity' => 10]));

    $html = $this->actingAs($admin)
        ->get(route('backend.product-stock-movements.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-stock-movements-table');

    // Filtre par type
    $this->actingAs($admin)
        ->get(route('backend.product-stock-movements.index', ['type' => 'out']))
        ->assertOk();
});

it('affiche la page de création d\'un mouvement de stock', function () {
    $admin = movementAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-stock-movements.create'))
        ->assertOk();
});

it('enregistre une entrée (in) : le stock augmente et le mouvement est audité avec created_by', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)
        ->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['type' => 'in', 'quantity' => 50]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $movement = ProductStockMovement::firstOrFail();
    $stock = ProductWarehouseStock::firstOrFail();

    expect(ProductStockMovement::count())->toBe(1);
    expect($movement->type->value)->toBe('in');
    expect($movement->quantity)->toBe(50);
    expect($movement->created_by)->toBe($admin->id); // created_by = auth()->id()
    expect($stock->quantity)->toBe(50); // 0 + 50
});

it('enregistre une sortie (out) : le stock diminue', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['type' => 'in', 'quantity' => 100]));

    $this->actingAs($admin)
        ->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['type' => 'out', 'quantity' => 30]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $stock = ProductWarehouseStock::firstOrFail();

    expect($stock->quantity)->toBe(70); // 100 - 30
    expect(ProductStockMovement::count())->toBe(2);
});

it('refuse une sortie qui rendrait le stock négatif', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['type' => 'in', 'quantity' => 10]));

    $this->actingAs($admin)
        ->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['type' => 'out', 'quantity' => 25]))
        ->assertRedirect()
        ->assertSessionHas('error'); // Garde métier : stock négatif interdit

    $stock = ProductWarehouseStock::firstOrFail();

    expect($stock->quantity)->toBe(10); // Inchangé
    expect(ProductStockMovement::count())->toBe(1); // Aucun mouvement d'audit créé (transactionnel)
});

it('refuse une quantité nulle ou négative (validation)', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)
        ->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['quantity' => 0]))
        ->assertRedirect()
        ->assertSessionHasErrors(['quantity']);

    expect(ProductStockMovement::count())->toBe(0);
});

it('refuse une référence polymorphique incomplète', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)
        ->post(route('backend.product-stock-movements.store'), movementPayload($fixtures, ['reference_type' => null, 'reference_id' => 123]))
        ->assertRedirect()
        ->assertSessionHasErrors(['reference_type']);

    expect(ProductStockMovement::count())->toBe(0);
});

it('affiche le détail d\'un mouvement', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)->post(route('backend.product-stock-movements.store'), movementPayload($fixtures));
    $movement = ProductStockMovement::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.product-stock-movements.show', ['productStockMovement' => $movement->uuid]))
        ->assertOk();
});

it('passe un mouvement en corbeille puis le restaure et le supprime définitivement', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)->post(route('backend.product-stock-movements.store'), movementPayload($fixtures));
    $movement = ProductStockMovement::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.product-stock-movements.destroy', ['productStockMovement' => $movement->uuid]))
        ->assertRedirect();

    expect(ProductStockMovement::find($movement->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-stock-movements.trashed'))
        ->patch(route('backend.product-stock-movements.restore', ['uuid' => $movement->uuid]))
        ->assertRedirect();

    expect(ProductStockMovement::find($movement->id))->not->toBeNull();

    // Suppression définitive : corbeille d'abord puis force destroy
    $movement->delete();

    $this->actingAs($admin)
        ->delete(route('backend.product-stock-movements.force-destroy', ['uuid' => $movement->uuid]))
        ->assertRedirect();

    expect(ProductStockMovement::withTrashed()->find($movement->id))->toBeNull();
});

it('affiche la corbeille des mouvements', function () {
    $admin = movementAdmin();
    $fixtures = movementFixtures();

    $this->actingAs($admin)->post(route('backend.product-stock-movements.store'), movementPayload($fixtures));
    $movement = ProductStockMovement::firstOrFail();
    $movement->delete();

    $this->actingAs($admin)
        ->get(route('backend.product-stock-movements.trashed'))
        ->assertOk();
});

it('refuse l\'accès aux mouvements sans permission', function () {
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $user = User::create([
        'country_id' => $country->id,
        'first_name' => 'Bob', 'last_name' => 'User',
        'email' => 'user-movements@site.com', 'gender' => 'male',
        'phone' => '+22904444448', 'password' => 'password',
        'status' => 'active',
    ]);

    $user->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();

    $this->actingAs($user)
        ->get(route('backend.product-stock-movements.index'))
        ->assertForbidden();
});

it('déclare stocks/movements comme relations bloquantes pour ProductVariant et ProductWarehouse (modules 8-9)', function () {
    $variant = new ProductVariant;
    $propertyV = new ReflectionProperty($variant, 'dependentRelations');
    expect($propertyV->getValue($variant))->toBe(['stocks', 'movements']);

    $warehouse = new ProductWarehouse;
    $propertyW = new ReflectionProperty($warehouse, 'dependentRelations');
    expect($propertyW->getValue($warehouse))->toBe(['stocks', 'movements']);
});
