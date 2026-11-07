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
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

/**
 * Module Boutique — Personnel des entrepôts (pivot `warehouse_users`).
 *
 * Le personnel est un PIVOT pur (comme `blog_post_tag`) : plus de modèle
 * WarehouseUser, on lit/écrit la table `warehouse_users` directement.
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
    // NB : la table product_warehouses ne porte plus que `code` + `status`.
    return ProductWarehouse::create([
        'code' => 'WH-'.strtoupper(uniqid()),
        'status' => 'active',
    ]);
}

it('n\'a plus de modèle WarehouseUser (pivot pur, à l\'image de blog_post_tag)', function () {
    // Garde anti-régression : c'est la suppression du modèle qui élimine définitivement
    // la LogicException « bootIfNotBooted … while it is being booted » (le modèle ne
    // pouvait plus utiliser le hook `restored()` réservé à SoftDeletes).
    expect(class_exists(\App\Models\WarehouseUser::class))->toBeFalse();
});

it('affiche la fiche entrepôt (show) sans erreur serveur', function () {
    $admin = warehouseUserAdmin();
    $warehouse = warehouseUserWarehouse();

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

it('affecte un utilisateur à un entrepôt (pivot warehouse_users)', function () {
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

    expect(DB::table('warehouse_users')->count())->toBe(1);

    $pivot = DB::table('warehouse_users')->first();
    expect($pivot->warehouse_id)->toBe($warehouse->id);
    expect($pivot->user_id)->toBe($user->id);
    expect($pivot->uuid)->not->toBeNull();
});

it('détache un utilisateur d\'un entrepôt', function () {
    $admin = warehouseUserAdmin();
    $warehouse = warehouseUserWarehouse();
    $user = warehouseUserUser();

    $this->actingAs($admin)->post(route('backend.warehouse-users.store', ['productWarehouse' => $warehouse->uuid]), [
        'user_uuid' => $user->uuid,
    ]);

    $pivot = DB::table('warehouse_users')->first();

    $this->actingAs($admin)
        ->delete(route('backend.warehouse-users.destroy', ['warehouseUser' => $pivot->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(DB::table('warehouse_users')->count())->toBe(0);
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

    expect(DB::table('warehouse_users')->count())->toBe(1);
});

it('déclare warehouseUsers comme relation bloquante pour ProductWarehouse (dependentRelations)', function () {
    $property = new ReflectionProperty(new ProductWarehouse, 'dependentRelations');
    expect($property->getValue(new ProductWarehouse))->toBe(['stocks', 'movements', 'warehouseUsers']);
});
