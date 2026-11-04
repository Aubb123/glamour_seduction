<?php

declare(strict_types=1);

use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\PlatformVersion;
use App\Models\User;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 7 — Module Versions de plateforme (`platform-versions`).
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function versionTestAdmin(): User
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

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function versionTestData(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();

    return array_merge([
        'code' => 'v3',
        'status' => 'inactive',
        'is_published' => false,
        'is_default' => false,
        'released_at' => null,
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Version 3', 'description' => 'Troisième version.'],
            ['lang_id' => $langEn->id, 'name' => 'Version 3', 'description' => 'Third version.'],
        ],
    ], $overrides);
}

it('liste les versions avec leurs statistiques', function () {
    $admin = versionTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.versions.index'))
        ->assertOk()
        ->assertSee('v1')
        ->assertSee('v2');
});

it('affiche la page de création avec les langues actives', function () {
    $admin = versionTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.versions.create'))
        ->assertOk()
        ->assertSee('Version');
});

it('crée une nouvelle version avec ses traductions (redirige vers le show)', function () {
    $admin = versionTestAdmin();

    $this->actingAs($admin)
        ->post(route('backend.versions.store'), versionTestData())
        ->assertRedirect()
        ->assertSessionHas('success');

    $version = PlatformVersion::where('code', 'v3')->first();
    expect($version)->not->toBeNull();
    expect($version->translations()->count())->toBe(2);
});

it('affiche le détail d’une version (show avec slug)', function () {
    $admin = versionTestAdmin();

    $version = PlatformVersion::where('code', 'v1')->first();
    $slug = $version->active_translation?->slug ?? $version->code;

    $this->actingAs($admin)
        ->get(route('backend.versions.show', ['platformVersion' => $version->uuid, 'slug' => $slug]))
        ->assertOk()
        ->assertSee('v1');
});

it('active et désactive une version publiée', function () {
    $admin = versionTestAdmin();

    // La v2 du seeder est inactive et NON publiée. Une version doit d'abord
    // être publiée pour pouvoir être activée (règle du service).
    $version = PlatformVersion::where('code', 'v2')->first();
    $version->update(['is_published' => true, 'released_at' => now()]);

    $this->actingAs($admin)
        ->patch(route('backend.versions.activate', ['platformVersion' => $version->uuid]))
        ->assertRedirect();

    expect($version->fresh()->status->value)->toBe('active');

    $this->actingAs($admin)
        ->patch(route('backend.versions.deactivate', ['platformVersion' => $version->uuid]))
        ->assertRedirect();

    expect($version->fresh()->status->value)->toBe('inactive');
});

it('refuse d’activer une version non publiée', function () {
    $admin = versionTestAdmin();

    $version = PlatformVersion::where('code', 'v2')->first(); // non publiée

    $this->actingAs($admin)
        ->from(route('backend.versions.index'))
        ->patch(route('backend.versions.activate', ['platformVersion' => $version->uuid]))
        ->assertRedirect(route('backend.versions.index'))
        ->assertSessionHas('error');

    expect($version->fresh()->status->value)->toBe('inactive');
});

it('publie une version et interdit ensuite sa dépublication (règle métier)', function () {
    $admin = versionTestAdmin();

    $version = PlatformVersion::where('code', 'v2')->first(); // non publiée

    $this->actingAs($admin)
        ->patch(route('backend.versions.publish', ['platformVersion' => $version->uuid]))
        ->assertRedirect();

    $fresh = $version->fresh();
    expect($fresh->is_published)->toBeTrue();
    expect($fresh->released_at)->not->toBeNull();

    // Une version publiée ne peut jamais être dépubliée (règles du service).
    $this->actingAs($admin)
        ->from(route('backend.versions.index'))
        ->patch(route('backend.versions.unpublish', ['platformVersion' => $version->uuid]))
        ->assertRedirect(route('backend.versions.index'))
        ->assertSessionHas('error');

    expect($version->fresh()->is_published)->toBeTrue();
});

it('passe une version à la corbeille puis la restaure', function () {
    $admin = versionTestAdmin();

    $version = PlatformVersion::where('code', 'v2')->first();

    $this->actingAs($admin)
        ->from(route('backend.versions.index'))
        ->delete(route('backend.versions.destroy', ['platformVersion' => $version->uuid]))
        ->assertRedirect(route('backend.versions.index'));

    expect(PlatformVersion::find($version->id))->toBeNull();
    expect(PlatformVersion::onlyTrashed()->find($version->id))->not->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.versions.trashed'))
        ->patch(route('backend.versions.restore', ['uuid' => $version->uuid]))
        ->assertRedirect(route('backend.versions.trashed'));

    expect(PlatformVersion::find($version->id))->not->toBeNull();
});

it('liste la corbeille des versions', function () {
    $admin = versionTestAdmin();

    $version = PlatformVersion::where('code', 'v2')->first();
    $version->delete();

    $this->actingAs($admin)
        ->get(route('backend.versions.trashed'))
        ->assertOk()
        ->assertSee('v2');
});

it('protege l’accès au module (non connecté → login)', function () {
    $this->get('/backend/versions/index')->assertRedirect(route('login'));
});
it('affiche la page d’édition d’une version', function () {
    $admin = versionTestAdmin();

    $version = PlatformVersion::where('code', 'v1')->first();

    $this->actingAs($admin)
        ->get(route('backend.versions.edit', ['platformVersion' => $version->uuid]))
        ->assertOk();
});
