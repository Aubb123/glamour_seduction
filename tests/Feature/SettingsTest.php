<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 11 — Paramètres (`settings`).
 */

beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    $this->seed(SettingSeeder::class);
    bootV1WebRoutes();
});

function settingsTestAdmin(): User
{
    $continent = \App\Models\GeoContinent::create([
        'code' => 'AFU', 'population' => 1000000, 'area_km2' => 100000,
        'file_path' => 'cu.svg', 'sort_order' => 900, 'status' => 'active',
    ]);

    $country = GeoCountry::create([
        'continent_id' => $continent->id, 'code' => 'BEN2', 'code_2' => 'B2',
        'file_path' => 'f2.svg', 'sort_order' => 900, 'status' => 'active',
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

it('affiche les paramètres groupés (index settings)', function () {
    $admin = settingsTestAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.settings.index'))
        ->assertOk()
        ->getContent();

    // La vue affiche les noms traduits (pas les clés brutes) et les form_key uuid
    expect($html)->toContain('settings[');
    expect(\App\Models\Setting::where('key', 'site_name')->exists())->toBeTrue();
});

it('met à jour une valeur de paramètre non traduisible (update)', function () {
    $admin = settingsTestAdmin();

    $setting = Setting::create([
        'key' => 'test_key_'.uniqid(),
        'value' => '42',
        'type' => 'integer',
        'is_public' => false,
        'is_translatable' => false,
        'is_editable' => true,
    ]);

    $this->actingAs($admin)
        ->put(route('backend.settings.update'), [
            'settings' => [
                $setting->uuid => [
                    'uuid' => $setting->uuid,
                    'value' => '128',
                    'is_public' => '1',
                ],
            ],
        ])
        ->assertRedirect(route('backend.settings.index'));

    expect($setting->fresh()->value)->toBe('128');
    expect($setting->fresh()->is_public)->toBeTrue();
});

it('affiche les audits et logs des paramètres', function () {
    $admin = settingsTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.settings.audits'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('backend.settings.activity-logs'))
        ->assertOk();
});

it('protege l’accès aux paramètres (non connecté → login)', function () {
    $this->get('/backend/settings/index')->assertRedirect(route('login'));
});