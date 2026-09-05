<?php

declare(strict_types=1);

use App\Models\CloudPlatform;
use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\Permission;
use App\Models\PlatformVersion;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\CloudPlatformSeeder;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 4 — Exécution des seeders de fondation.
 *
 * NB : ClearAllTablesSeeder (SHOW TABLES MySQL) et CloudFolderSeeder / CloudFileSeeder
 * (services Cloud) ne sont pas exécutés ici : ils seront couverts avec les étapes Cloud.
 */

it('seede les langues (fr par défaut + en)', function () {
    $this->seed(LangSeeder::class);

    expect(Lang::count())->toBe(2);
    expect(Lang::where('code', 'fr')->first()->is_default)->toBeTrue();
    expect(Lang::active()->count())->toBe(2);
});

it('seede les versions de plateforme avec leurs traductions', function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);

    expect(PlatformVersion::count())->toBe(2);
    expect(PlatformVersion::where('code', 'v1')->first()->is_default)->toBeTrue();
    expect(PlatformVersion::where('code', 'v2')->first()->status->value)->toBe('inactive');
});

it('seede la plateforme cloud Backblaze B2 active', function () {
    $this->seed(CloudPlatformSeeder::class);

    expect(CloudPlatform::count())->toBe(1);
    expect(CloudPlatform::first()->name)->toBe('backblaze_b2');
    expect(CloudPlatform::first()->status->value)->toBe('active');
});

it('seede les continents et pays avec leurs traductions', function () {
    $this->seed(LangSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);

    expect(GeoContinent::count())->toBe(7); // AF, AN, AS, EU, NA, OC, SA
    expect(GeoCountry::count())->toBeGreaterThan(200);
    expect(GeoCountry::where('code', 'BEN')->exists())->toBeTrue();
    expect(GeoCountry::where('code', 'FRA')->exists())->toBeTrue();
});

it('seede les rôles, permissions et utilisateurs', function () {
    $this->seed(LangSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    $this->seed(UserSeeder::class);

    expect(Role::count())->toBe(10);
    expect(Permission::count())->toBeGreaterThan(100);
    expect(User::count())->toBe(5);

    $admin = User::where('email', 'admin@site.com')->first();
    expect($admin)->not->toBeNull();
    expect($admin->hasRole('administrator'))->toBeTrue();
});

it('seede les paramètres (settings) avec leurs traductions', function () {
    $this->seed(LangSeeder::class);
    $this->seed(SettingSeeder::class);

    expect(Setting::count())->toBeGreaterThanOrEqual(5);

    $siteName = Setting::where('key', 'site_name')->first();
    expect($siteName)->not->toBeNull();

    $maxUnsynced = Setting::where('key', 'max_unsynced_files_before_redirect')->first();
    expect($maxUnsynced)->not->toBeNull();
    expect($maxUnsynced->getValue())->toBe(10);
});