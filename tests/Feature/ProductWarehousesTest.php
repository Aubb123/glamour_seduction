<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\ProductWarehouse;
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
 * Module Produits — Entrepôts (`product-warehouses`).
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

function warehouseAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-warehouses@site.com', 'gender' => 'female',
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

function warehousePayload(array $overrides = []): array
{
    return array_merge([
        'code' => 'WH-'.strtoupper(uniqid()),
        'name' => 'Entrepôt de test',
        'address' => 'Zone industrielle',
        'city' => 'Cotonou',
        'phone' => '+229 21 00 00 99',
        'status' => 'inactive',
        'is_default' => false,
    ], $overrides);
}

it('liste les entrepôts avec DataTables', function () {
    $admin = warehouseAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouses-table');
});

it('affiche la page de création d\'entrepôt', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouses.create'))
        ->assertOk();
});

it('crée un entrepôt (premier → is_default auto)', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-warehouses.store'), warehousePayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    $warehouse = ProductWarehouse::firstOrFail();

    expect(ProductWarehouse::count())->toBe(1);
    expect($warehouse->is_default)->toBeTrue();
    expect($warehouse->status->value)->toBe('inactive');
});

it('refuse un code dupliqué', function () {
    $admin = warehouseAdmin();

    $payload = warehousePayload(['status' => 'active']);
    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), $payload);

    $this->actingAs($admin)
        ->post(route('backend.product-warehouses.store'), warehousePayload(['code' => $payload['code']]))
        ->assertRedirect()
        ->assertSessionHasErrors(['code']);

    expect(ProductWarehouse::count())->toBe(1);
});

it('gère un seul entrepôt par défaut (la nouvelle remplace)', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $first = ProductWarehouse::firstOrFail();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload(['is_default' => true]));

    expect(ProductWarehouse::count())->toBe(2);
    expect($first->fresh()->is_default)->toBeFalse();
    expect(ProductWarehouse::where('is_default', true)->count())->toBe(1);
});

it('affiche le détail d\'un entrepôt', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]))
        ->assertOk();
});

it('met à jour un entrepôt', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $payload = warehousePayload(['name' => 'Entrepôt renommé', 'city' => 'Porto-Novo']);

    $this->actingAs($admin)
        ->put(route('backend.product-warehouses.update', ['productWarehouse' => $warehouse->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $warehouse->refresh();
    expect($warehouse->name)->toBe('Entrepôt renommé');
    expect($warehouse->city)->toBe('Porto-Novo');
});

it('active et désactive un entrepôt', function () {
    $admin = warehouseAdmin();

    // Second entrepôt (non défaut) : on garde un entrepôt par défaut distinct pour
    // pouvoir désactiver celui-ci sans déclencher la garde « dernier par défaut ».
    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload(['is_default' => true]));
    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::where('is_default', false)->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.product-warehouses.activate', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect($warehouse->fresh()->status->value)->toBe('active');

    $this->actingAs($admin)
        ->patch(route('backend.product-warehouses.deactivate', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect($warehouse->fresh()->status->value)->toBe('inactive');
});

it('refuse la suppression d\'un entrepôt actif puis autorise après désactivation', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload(['is_default' => true]));
    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload(['status' => 'active']));
    $warehouse = ProductWarehouse::where('is_default', false)->firstOrFail();

    expect($warehouse->status->value)->toBe('active');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.index'))
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect(route('backend.product-warehouses.index'))
        ->assertSessionHas('error');

    expect(ProductWarehouse::find($warehouse->id))->not->toBeNull();

    $warehouse->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect(ProductWarehouse::find($warehouse->id))->toBeNull();
});

it('passe un entrepôt en corbeille puis le restaure', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload(['is_default' => true]));
    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::where('is_default', false)->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect(ProductWarehouse::find($warehouse->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.trashed'))
        ->patch(route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]))
        ->assertRedirect();

    expect(ProductWarehouse::find($warehouse->id))->not->toBeNull();
});

it('déclare dependentRelations = [stocks, movements, warehouseUsers] pour ProductWarehouse', function () {
    $warehouse = new ProductWarehouse;

    $property = new ReflectionProperty($warehouse, 'dependentRelations');
    expect($property->getValue($warehouse))->toBe(['stocks', 'movements', 'warehouseUsers']);
});

// ─────────────────────────────────────────────
// Garde métier : dernier entrepôt par défaut
// ─────────────────────────────────────────────

it('refuse de retirer le statut « par défaut » au dernier entrepôt par défaut (update)', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    expect($warehouse->is_default)->toBeTrue();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.edit', ['productWarehouse' => $warehouse->uuid]))
        ->put(route('backend.product-warehouses.update', ['productWarehouse' => $warehouse->uuid]), warehousePayload([
            'code' => $warehouse->code,
            'name' => $warehouse->name,
            'is_default' => false,
        ]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($warehouse->fresh()->is_default)->toBeTrue();
    expect(ProductWarehouse::where('is_default', true)->count())->toBe(1);
});

it('refuse de désactiver le dernier entrepôt par défaut', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload(['status' => 'active']));
    $warehouse = ProductWarehouse::firstOrFail();

    expect($warehouse->is_default)->toBeTrue();
    expect($warehouse->status->value)->toBe('active');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.index'))
        ->patch(route('backend.product-warehouses.deactivate', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($warehouse->fresh()->status->value)->toBe('active');
    expect($warehouse->fresh()->is_default)->toBeTrue();
});
