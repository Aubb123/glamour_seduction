<?php

declare(strict_types=1);

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
 * Contrat thème : les tables du back-office sont rendues AVEC DataTables
 * (comme dans base) — seul le template change, pas les fonctionnalités.
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

function datatablesTestAdmin(): User
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

it('rend DataTables sur la table des versions de plateforme', function () {
    $admin = datatablesTestAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.versions.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('versions-table');
    expect($html)->toContain('jquery.dataTables');
    expect($html)->toContain("$('#versions-table').DataTable");
});

it('rend DataTables sur la table des langues', function () {
    $admin = datatablesTestAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.langs.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('langs-table');
    expect($html)->toContain("$('#langs-table').DataTable");
});

it('rend DataTables sur la table des utilisateurs', function () {
    $admin = datatablesTestAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.users.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('users-table');
    expect($html)->toContain("$('#users-table').DataTable");
});