<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Shop;
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
 * Module Boutique — Personnel des boutiques (pivot `shop_users`).
 *
 * Le personnel est un PIVOT pur (comme `blog_post_tag`) : plus de modèle
 * ShopUser, on lit/écrit la table `shop_users` directement.
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

it('n\'a plus de modèle ShopUser (pivot pur, à l\'image de blog_post_tag)', function () {
    expect(class_exists(\App\Models\ShopUser::class))->toBeFalse();
});

it('affecte un utilisateur à une boutique (pivot shop_users)', function () {
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

    expect(DB::table('shop_users')->count())->toBe(1);

    $pivot = DB::table('shop_users')->first();
    expect($pivot->shop_id)->toBe($shop->id);
    expect($pivot->user_id)->toBe($user->id);
    expect($pivot->uuid)->not->toBeNull();
});

it('détache un utilisateur d\'une boutique', function () {
    $admin = shopUserAdmin();
    $shop = shopUserShop();
    $user = shopUserUser();

    $this->actingAs($admin)->post(route('backend.shop-users.store', ['shop' => $shop->uuid]), [
        'user_uuid' => $user->uuid,
    ]);

    $pivot = DB::table('shop_users')->first();

    $this->actingAs($admin)
        ->delete(route('backend.shop-users.destroy', ['shopUser' => $pivot->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(DB::table('shop_users')->count())->toBe(0);
});

it('refuse une affectation dupliquée (shop_id, user_id)', function () {
    $admin = shopUserAdmin();
    $shop = shopUserShop();
    $user = shopUserUser();

    $this->actingAs($admin)->post(route('backend.shop-users.store', ['shop' => $shop->uuid]), [
        'user_uuid' => $user->uuid,
    ]);

    // Seconde affectation du même couple → erreur (règle métier explicite)
    $this->actingAs($admin)
        ->post(route('backend.shop-users.store', ['shop' => $shop->uuid]), [
            'user_uuid' => $user->uuid,
        ])
        ->assertSessionHas('error');

    expect(DB::table('shop_users')->count())->toBe(1);
});

it('déclare shopUsers comme relation bloquante pour Shop (dependentRelations)', function () {
    $property = new ReflectionProperty(new Shop, 'dependentRelations');
    expect($property->getValue(new Shop))->toBe(['shopUsers']);
});
