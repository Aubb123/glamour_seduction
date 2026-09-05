<?php

declare(strict_types=1);

use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\CloudFolderSeeder;
use Database\Seeders\CloudPlatformSeeder;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 13 — Modules Cloud : Dossiers (`cloud-folders`) & Fichiers (`cloud-files`).
 */

beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    $this->seed(CloudPlatformSeeder::class);
    $this->seed(CloudFolderSeeder::class);
    bootV1WebRoutes();
});

function cloudFilesTestAdmin(): User
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

function cloudFoldersTestFolder(): CloudFolder
{
    return CloudFolder::whereNotNull('entity_table')->firstOrFail();
}

function cloudTestFile(CloudFolder $folder): CloudFile
{
    return CloudFile::create([
        'cloud_folder_id' => $folder->id,
        'file_name' => 'test-image.jpg',
        'file_type' => 'image',
        'mime_type' => 'image/jpeg',
        'file_size' => 2048,
        'sync_status' => 'pending',
        'local_path' => null,
        'remote_path' => null,
    ]);
}

// ─────────────────────────────────────────────
// DOSSIERS
// ─────────────────────────────────────────────

it('liste les dossiers cloud', function () {
    $admin = cloudFilesTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.cloud-folders.index'))
        ->assertOk();
});

it('affiche le détail d’un dossier cloud', function () {
    $admin = cloudFilesTestAdmin();

    $folder = cloudFoldersTestFolder();

    $this->actingAs($admin)
        ->get(route('backend.cloud-folders.show', ['cloudFolder' => $folder->uuid]))
        ->assertOk();
});

it('affiche les fichiers d’un dossier', function () {
    $admin = cloudFilesTestAdmin();

    $folder = cloudFoldersTestFolder();

    $this->actingAs($admin)
        ->get(route('backend.cloud-folders.files', ['cloudFolder' => $folder->uuid]))
        ->assertOk();
});

it('affiche les sous-dossiers d’un dossier', function () {
    $admin = cloudFilesTestAdmin();

    $folder = cloudFoldersTestFolder();

    $this->actingAs($admin)
        ->get(route('backend.cloud-folders.subfolders', ['cloudFolder' => $folder->uuid]))
        ->assertOk();
});

it('affiche la page d’édition d’un dossier', function () {
    $admin = cloudFilesTestAdmin();

    $folder = cloudFoldersTestFolder();

    $this->actingAs($admin)
        ->get(route('backend.cloud-folders.edit', ['cloudFolder' => $folder->uuid]))
        ->assertOk();
});

it('bascule l’état d’un dossier (toggle-active)', function () {
    $admin = cloudFilesTestAdmin();

    $folder = cloudFoldersTestFolder();
    $initial = $folder->status->value;

    $this->actingAs($admin)
        ->patch(route('backend.cloud-folders.toggle_active', ['cloudFolder' => $folder->uuid]))
        ->assertRedirect();

    expect($folder->fresh()->status->value)->not->toBe($initial);
});

it('masque les secrets via la navigation dossiers (aucune fuite dans le HTML)', function () {
    $admin = cloudFilesTestAdmin();

    $platform = CloudPlatform::firstOrFail();
    $folder = cloudFoldersTestFolder();

    $html = $this->actingAs($admin)
        ->get(route('backend.cloud-folders.show', ['cloudFolder' => $folder->uuid]))
        ->assertOk()
        ->getContent();

    if ($platform->api_secret !== null && $platform->api_secret !== '') {
        expect($html)->not->toContain((string) $platform->api_secret);
    }
});