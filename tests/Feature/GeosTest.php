<?php

declare(strict_types=1);

use App\Models\GeoContinent;
use App\Models\GeoCountry;
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
 * Étape 8 — Modules Géographie (`geo-continents`, `geo-countries`).
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

function geoTestAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

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

it('liste les continents avec leurs pays et traductions', function () {
    $admin = geoTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geocontinents.index'))
        ->assertOk()
        ->assertSee('AF');
});

it('liste les pays avec leur continent', function () {
    $admin = geoTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geocountries.index'))
        ->assertOk()
        ->assertSee('BEN')
        ->assertSee('FRA');
});

it('affiche le détail d’un continent (show avec slug)', function () {
    $admin = geoTestAdmin();

    $continent = GeoContinent::where('code', 'AF')->firstOrFail();
    $slug = $continent->active_translation?->slug ?? $continent->code;

    $this->actingAs($admin)
        ->get(route('backend.geocontinents.show', ['geoContinent' => $continent->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('affiche le détail d’un pays (show avec slug)', function () {
    $admin = geoTestAdmin();

    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $slug = $country->active_translation?->slug ?? $country->code;

    $this->actingAs($admin)
        ->get(route('backend.geocountries.show', ['geoCountry' => $country->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('active et désactive un continent', function () {
    $admin = geoTestAdmin();

    // 'AN' (Antarctique) est seedé actif → on le désactive puis réactive.
    $continent = GeoContinent::where('code', 'AN')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.geocontinents.deactivate', ['geoContinent' => $continent->uuid]))
        ->assertRedirect();

    expect($continent->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.geocontinents.activate', ['geoContinent' => $continent->uuid]))
        ->assertRedirect();

    expect($continent->fresh()->status->value)->toBe('active');
});

it('active et désactive un pays', function () {
    $admin = geoTestAdmin();

    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.geocountries.deactivate', ['geoCountry' => $country->uuid]))
        ->assertRedirect();

    expect($country->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.geocountries.activate', ['geoCountry' => $country->uuid]))
        ->assertRedirect();

    expect($country->fresh()->status->value)->toBe('active');
});

it('protege l’accès aux pages géo (non connecté → login)', function () {
    $this->get('/backend/geo-continents/index')->assertRedirect(route('login'));
    $this->get('/backend/geo-countries/index')->assertRedirect(route('login'));
});
