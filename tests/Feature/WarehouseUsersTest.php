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
    return ProductWarehouse::create([
        'code' => 'WH-'.strtoupper(uniqid()),
        'name' => 'Entrepôt '.uniqid(),
        'status' => 'active',
        'is_default' => false,
    ]);
}

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
