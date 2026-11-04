<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 16 — Frontend placeholder (`frontend.index`).
 *
 * Dans `base`, le frontend est un squelette minimal : la page welcome
 * affiche « v1 », un lien vers le backend et, si connecté, le bouton
 * de déconnexion. Ce test fige ce contrat.
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    bootV1WebRoutes();
});

it('affiche la page d’accueil avec le placeholder v1', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('v1');
});

it('affiche le lien vers le backend', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('backend.index'));
});

it('affiche le bouton de déconnexion pour un utilisateur connecté', function () {
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);

    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $user = User::create([
        'country_id' => $country->id,
        'first_name' => 'Jean', 'last_name' => 'Dupont',
        'email' => 'jean.dupont@example.com', 'gender' => 'male',
        'phone' => '+22900000001', 'password' => 'password',
        'status' => 'active',
    ]);

    $user->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee(__('Déconnexion'));
});
