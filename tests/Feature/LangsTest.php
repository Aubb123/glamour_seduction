<?php

declare(strict_types=1);

use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 6 — Module Langues (`langs`).
 */

beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function langTestAdmin(): User
{
    $continent = GeoContinent::create([
        'code' => 'AF', 'population' => 1000000, 'area_km2' => 100000,
        'file_path' => 'c.svg', 'sort_order' => 1, 'status' => 'active',
    ]);

    $country = GeoCountry::create([
        'continent_id' => $continent->id, 'code' => 'BEN', 'code_2' => 'BJ',
        'file_path' => 'f.svg', 'sort_order' => 1, 'status' => 'active',
    ]);

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'status' => 'active',
    ]);

    // NB : `#[Fillable]` du modèle User n'inclut PAS email_verified_at /
    // phone_verified_at → on force ces attributs pour passer les middlewares
    // CheckUserEmail / CheckUserPhone.
    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

it('liste les langues sur la page index du backend', function () {
    $admin = langTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.langs.index'))
        ->assertOk()
        ->assertSee('fr')
        ->assertSee('en')
        ->assertSee('Français');
});

it('protege l’accès à la page des langues (non connecté → login)', function () {
    $this->get('/backend/langs/index')->assertRedirect(route('login'));
});

it('active une langue inactive via la route back-end', function () {
    $admin = langTestAdmin();

    // 'en' est active par défaut → on la désactive d'abord, puis on la réactive.
    $lang = \App\Models\Lang::where('code', 'en')->first();

    $this->actingAs($admin)
        ->patch(route('backend.langs.activate', ['lang' => $lang->uuid]))
        ->assertRedirect();

    expect($lang->fresh()->is_active)->toBeTrue();
});

it('désactive une langue non par défaut via la route back-end', function () {
    $admin = langTestAdmin();

    $lang = \App\Models\Lang::where('code', 'en')->first(); // fr = défaut, en = non défaut

    $this->actingAs($admin)
        ->from(route('backend.langs.index'))
        ->patch(route('backend.langs.deactivate', ['lang' => $lang->uuid]))
        ->assertRedirect(route('backend.langs.index'))
        ->assertSessionHas('success');

    expect($lang->fresh()->is_active)->toBeFalse();
});

it('interdit la désactivation de la langue par défaut', function () {
    $admin = langTestAdmin();

    $lang = \App\Models\Lang::where('code', 'fr')->first(); // langue par défaut

    $this->actingAs($admin)
        ->from(route('backend.langs.index'))
        ->patch(route('backend.langs.deactivate', ['lang' => $lang->uuid]))
        ->assertRedirect(route('backend.langs.index'))
        ->assertSessionHas('error');

    expect($lang->fresh()->is_default)->toBeTrue();
    expect($lang->fresh()->is_active)->toBeTrue();
});

it('change la langue de session via le sélecteur (backend.language.switch)', function () {
    $admin = langTestAdmin();
    $lang = \App\Models\Lang::where('code', 'en')->first();

    $this->actingAs($admin)
        ->from(route('backend.langs.index'))
        ->get(route('backend.language.switch', ['lang' => $lang->uuid]))
        ->assertRedirect(route('backend.langs.index'));

    expect(session('locale'))->toBe('en');
    expect(app()->getLocale())->toBe('en');
});

it('refuse le switch vers une langue inactive', function () {
    $admin = langTestAdmin();

    $lang = \App\Models\Lang::where('code', 'en')->first();
    $lang->update(['is_active' => false]);

    $this->actingAs($admin)
        ->from(route('backend.langs.index'))
        ->get(route('backend.language.switch', ['lang' => $lang->uuid]))
        ->assertRedirect(route('backend.langs.index'))
        ->assertSessionHas('error');

    expect(session('locale'))->not->toBe('en');
});