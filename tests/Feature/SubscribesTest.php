<?php

declare(strict_types=1);

use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\Subscribe;
use App\Models\User;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 11 — Subscribes (`subscribes` + `subscribe_histories`).
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function subscribeTestAdmin(): User
{
    $continent = GeoContinent::create([
        'code' => 'AFS', 'population' => 1000000, 'area_km2' => 100000,
        'file_path' => 'cs.svg', 'sort_order' => 904, 'status' => 'active',
    ]);

    $country = GeoCountry::create([
        'continent_id' => $continent->id, 'code' => 'BEN4', 'code_2' => 'B4',
        'file_path' => 'f4.svg', 'sort_order' => 902, 'status' => 'active',
    ]);

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

it('liste les abonnés', function () {
    $admin = subscribeTestAdmin();

    Subscribe::create(['email' => 'abo1@mail.com', 'status' => 'active', 'verified_at' => now()]);

    $this->actingAs($admin)
        ->get(route('backend.subscribes.index'))
        ->assertOk()
        ->assertSee('abo1@mail.com');
});

it('affiche le détail d’un abonné', function () {
    $admin = subscribeTestAdmin();

    $subscribe = Subscribe::create(['email' => 'abo2@mail.com', 'status' => 'active', 'verified_at' => now()]);

    $this->actingAs($admin)
        ->get(route('backend.subscribes.show', ['subscribe' => $subscribe->uuid]))
        ->assertOk()
        ->assertSee('abo2@mail.com');
});

it('active et désactive un abonné', function () {
    $admin = subscribeTestAdmin();

    $subscribe = Subscribe::create(['email' => 'abo3@mail.com', 'status' => 'inactive']);

    $this->actingAs($admin)
        ->patch(route('backend.subscribes.activate', ['subscribe' => $subscribe->uuid]))
        ->assertRedirect();

    expect($subscribe->fresh()->status->value)->toBe('active');

    $this->actingAs($admin)
        ->patch(route('backend.subscribes.deactivate', ['subscribe' => $subscribe->uuid]))
        ->assertRedirect();

    expect($subscribe->fresh()->status->value)->toBe('inactive');
});

it('refuse la suppression d’un abonné actif puis autorise après désactivation', function () {
    $admin = subscribeTestAdmin();

    $subscribe = Subscribe::create(['email' => 'abo4@mail.com', 'status' => 'active']);

    // Actif → suppression refusée (règle du SubscribeService::destroy)
    $this->actingAs($admin)
        ->from(route('backend.subscribes.index'))
        ->delete(route('backend.subscribes.destroy', ['subscribe' => $subscribe->uuid]))
        ->assertRedirect(route('backend.subscribes.index'))
        ->assertSessionHas('error');

    expect(Subscribe::find($subscribe->id))->not->toBeNull();

    // Inactif → suppression autorisée
    $subscribe->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.subscribes.destroy', ['subscribe' => $subscribe->uuid]))
        ->assertRedirect();

    expect(Subscribe::find($subscribe->id))->toBeNull();
});

it('passe un abonné en corbeille puis le restaure', function () {
    $admin = subscribeTestAdmin();

    $subscribe = Subscribe::create(['email' => 'abo5@mail.com', 'status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.subscribes.destroy', ['subscribe' => $subscribe->uuid]))
        ->assertRedirect();

    $this->actingAs($admin)
        ->from(route('backend.subscribes.trashed'))
        ->patch(route('backend.subscribes.restore', ['uuid' => $subscribe->uuid]))
        ->assertRedirect();

    expect(Subscribe::find($subscribe->id))->not->toBeNull();
});

it('protege l’accès aux abonnés (non connecté → login)', function () {
    $this->get('/backend/subscribes/index')->assertRedirect(route('login'));
});
