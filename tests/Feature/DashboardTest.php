<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 15 — Dashboard (`backend.index`).
 *
 * NB : `backend.analytics` utilise DATE_FORMAT (MySQL pur) → il est testé
 * séparément sur la connexion MySQL de recette (AnalyticsTest.php).
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

function dashboardTestAdmin(): User
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

it('affiche le dashboard avec les statistiques globales', function () {
    $admin = dashboardTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.index'))
        ->assertOk();
});

it('protege le dashboard (non connecté → login)', function () {
    $this->get('/backend')->assertRedirect(route('login'));
});
