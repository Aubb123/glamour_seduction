<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\ProductWarehouse;
use App\Models\User;
use App\Models\WarehouseUser;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Boutique — Personnel des entrepôts (`warehouse_users`).
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

function warehouseUserAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-warehouse-users@site.com', 'gender' => 'female',
        'phone' => '+22908888888', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function warehouseUserUser(string $email = 'wh-staff@site.com'): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $user = User::create([
        'country_id' => $country->id,
        'first_name' => 'Bob', 'last_name' => 'Staff',
        'email' => $email, 'gender' => 'male',
        'phone' => '+22909999999', 'password' => 'password',
        'status' => 'active',
    ]);

    $user->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    return $user;
}

function warehouseUserWarehouse(): ProductWarehouse
{
    // NB : la table product_warehouses ne porte plus que `code` + `status`
    // (name/address/city/phone vivent dans product_warehouse_translations, et
    // is_default est remplacé par le pivot shop_warehouse.is_primary).
    return ProductWarehouse::create([
        'code' => 'WH-'.strtoupper(uniqid()),
        'status' => 'active',
    ]);
}

it('n\'utilise pas SoftDeletes (la table warehouse_users n\'a pas de deleted_at)', function () {
    // Garde anti-régression : si le modèle repassait par HasModelBehaviors
    // (qui embarque SoftDeletes), toute requête planterait avec
    // « Unknown column 'warehouse_users.deleted_at' in 'where clause' ».
    expect(in_array(SoftDeletes::class, class_uses_recipes_safe(WarehouseUser::class), true))->toBeFalse();
    expect((new WarehouseUser)->getDeletedAtColumn())->toBe('deleted_at');
});

it('affiche la fiche entrepôt (show) sans erreur serveur', function () {
    $admin = warehouseUserAdmin();
    $warehouse = warehouseUserWarehouse();

    // La relation ProductWarehouse::warehouseUsers est chargée par le show :
    // c'est ce chargement qui levait un 500 avant la correction.
    $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]))
        ->assertOk();
});

it('affiche le formulaire d\'édition entrepôt (edit) sans erreur serveur', function () {
    $admin = warehouseUserAdmin();
    $warehouse = warehouseUserWarehouse();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouses.edit', ['productWarehouse' => $warehouse->uuid]))
        ->assertOk();
});

it('affecte un utilisateur à un entrepôt', function () {
    $admin = warehouseUserAdmin();
    $warehouse = warehouseUserWarehouse();
    $user = warehouseUserUser();

    $this->actingAs($admin)
        ->post(route('backend.warehouse-users.store', ['productWarehouse' => $warehouse->uuid]), [
            'user_uuid' => $user->uuid,
            'is_manager' => false,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(WarehouseUser::count())->toBe(1);
    $warehouseUser = WarehouseUser::firstOrFail();
    expect($warehouseUser->warehouse_id)->toBe($warehouse->id);
    expect($warehouseUser->user_id)->toBe($user->id);
});

it('détache un utilisateur d\'un entrepôt', function () {
    $admin = warehouseUserAdmin();
    $warehouse = warehouseUserWarehouse();
    $user = warehouseUserUser();

    $this->actingAs($admin)->post(route('backend.warehouse-users.store', ['productWarehouse' => $warehouse->uuid]), [
        'user_uuid' => $user->uuid,
    ]);

    $warehouseUser = WarehouseUser::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.warehouse-users.destroy', ['warehouseUser' => $warehouseUser->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(WarehouseUser::count())->toBe(0);
});

it('refuse une affectation dupliquée (warehouse_id, user_id)', function () {
    $admin = warehouseUserAdmin();
    $warehouse = warehouseUserWarehouse();
    $user = warehouseUserUser();

    $this->actingAs($admin)->post(route('backend.warehouse-users.store', ['productWarehouse' => $warehouse->uuid]), [
        'user_uuid' => $user->uuid,
    ]);

    $this->actingAs($admin)
        ->post(route('backend.warehouse-users.store', ['productWarehouse' => $warehouse->uuid]), [
            'user_uuid' => $user->uuid,
        ])
        ->assertSessionHas('error');

    expect(WarehouseUser::count())->toBe(1);
});

it('déclare warehouseUsers comme relation bloquante pour ProductWarehouse (dependentRelations)', function () {
    $property = new ReflectionProperty(new ProductWarehouse, 'dependentRelations');
    expect($property->getValue(new ProductWarehouse))->toBe(['stocks', 'movements', 'warehouseUsers']);
});
