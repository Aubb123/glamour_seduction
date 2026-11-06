<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Shop;
use App\Models\ShopUser;
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
 * Module Boutique — Personnel des boutiques (`shop_users`).
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

function shopUserAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-shop-users@site.com', 'gender' => 'female',
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

function shopUserUser(string $email = 'staff@site.com'): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $user = User::create([
        'country_id' => $country->id,
        'first_name' => 'Bob', 'last_name' => 'Staff',
        'email' => $email, 'gender' => 'male',
        'phone' => '+22907777777', 'password' => 'password',
        'status' => 'active',
    ]);

    $user->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    return $user;
}

function shopUserShop(): Shop
{
    return Shop::create([
        'code' => 'SHOP-'.strtoupper(uniqid()),
        'status' => 'active',
        'is_default' => true,
        'sort_order' => random_int(1, 9999),
    ]);
}

it('affecte un utilisateur à une boutique', function () {
    $admin = shopUserAdmin();
    $shop = shopUserShop();
    $user = shopUserUser();

    $this->actingAs($admin)
        ->post(route('backend.shop-users.store', ['shop' => $shop->uuid]), [
            'user_uuid' => $user->uuid,
            'is_manager' => false,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ShopUser::count())->toBe(1);
    $shopUser = ShopUser::firstOrFail();
    expect($shopUser->shop_id)->toBe($shop->id);
    expect($shopUser->user_id)->toBe($user->id);
});

it('détache un utilisateur d\'une boutique', function () {
    $admin = shopUserAdmin();
    $shop = shopUserShop();
    $user = shopUserUser();

    $this->actingAs($admin)->post(route('backend.shop-users.store', ['shop' => $shop->uuid]), [
        'user_uuid' => $user->uuid,
    ]);

    $shopUser = ShopUser::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.shop-users.destroy', ['shopUser' => $shopUser->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ShopUser::count())->toBe(0);
});

it('refuse une affectation dupliquée (shop_id, user_id)', function () {
    $admin = shopUserAdmin();
    $shop = shopUserShop();
    $user = shopUserUser();

    $this->actingAs($admin)->post(route('backend.shop-users.store', ['shop' => $shop->uuid]), [
        'user_uuid' => $user->uuid,
    ]);

    // Seconde affectation du même couple → erreur (contrainte d'unicité)
    $this->actingAs($admin)
        ->post(route('backend.shop-users.store', ['shop' => $shop->uuid]), [
            'user_uuid' => $user->uuid,
        ])
        ->assertSessionHas('error');

    expect(ShopUser::count())->toBe(1);
});

it('déclare shopUsers comme relation bloquante pour Shop (dependentRelations)', function () {
    $property = new ReflectionProperty(new Shop, 'dependentRelations');
    expect($property->getValue(new Shop))->toBe(['shopUsers']);
});
