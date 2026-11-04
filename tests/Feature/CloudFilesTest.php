<?php

declare(strict_types=1);

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\CloudFile;
use App\Models\CloudFolder;
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
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/**
 * Étape 13 — Module Cloud : Fichiers (`cloud-files`).
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

function cloudFileTestAdmin(): User
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

function cloudTestFolder(): CloudFolder
{
    return CloudFolder::whereNotNull('entity_table')->firstOrFail();
}

function cloudFileFixture(User $owner): CloudFile
{
    $folder = CloudFolder::whereNotNull('entity_table')->firstOrFail();

    return CloudFile::create([
        'cloud_folder_id' => $folder->id,
        'fileable_type' => User::class,
        'fileable_id' => $owner->id,
        'file_name' => 'test-image.jpg',
        'file_type' => 'image',
        'mime_type' => 'image/jpeg',
        'file_size' => 2048,
        'sync_status' => 'pending',
    ]);
}

it('liste les fichiers cloud avec DataTables', function () {
    $admin = cloudFileTestAdmin();
    cloudFileFixture($admin);

    $html = $this->actingAs($admin)
        ->get(route('backend.cloud-files.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('cloud-files-table');
    expect($html)->toContain("$('#cloud-files-table').DataTable");
});

it('affiche le détail d’un fichier cloud (show)', function () {
    $admin = cloudFileTestAdmin();

    $file = cloudFileFixture($admin, cloudTestFolder());

    $this->actingAs($admin)
        ->get(route('backend.cloud-files.show', ['cloudFile' => $file->uuid]))
        ->assertOk()
        ->assertSee('test-image.jpg');
});

it('affiche la page de synchronisation des fichiers', function () {
    $admin = cloudFileTestAdmin();

    cloudFileFixture($admin);

    $html = $this->actingAs($admin)
        ->get(route('backend.cloud-files.sync'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('sync');
});

it('passe un fichier en corbeille puis le restaure (via fileable existant)', function () {
    $admin = cloudFileTestAdmin();
    $category = BlogCategory::create(['sort_order' => 501, 'status' => 'active']);
    $post = BlogPost::create([
        'category_id' => $category->id, 'user_id' => $admin->id,
        'status' => 'published', 'sort_order' => 2, 'published_at' => now()->subDay(),
    ]);

    $folder = cloudTestFolder();
    $file = CloudFile::create([
        'cloud_folder_id' => $folder->id,
        'fileable_type' => BlogPost::class,
        'fileable_id' => $post->id,
        'file_name' => 'attached.jpg',
        'file_type' => 'image',
        'mime_type' => 'image/jpeg',
        'file_size' => 1024,
        'sync_status' => 'pending',
    ]);

    // Règle : un fichier attaché à une entité ne peut pas être supprimé via la route
    $this->actingAs($admin)
        ->from(route('backend.cloud-files.trashed'))
        ->delete(route('backend.cloud-files.destroy', ['cloudFile' => $file->uuid]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(CloudFile::find($file->id))->not->toBeNull();

    // Le fichier est soft-deleté directement (flux réel : cascade destroy_function)
    $file->delete();

    // Le restore fonctionne car le fileable existe et n'est pas trashed
    $this->actingAs($admin)
        ->from(route('backend.cloud-files.trashed'))
        ->patch(route('backend.cloud-files.restore', ['uuid' => $file->uuid]))
        ->assertRedirect();

    expect(CloudFile::find($file->id))->not->toBeNull();
});

it('expose le proxy public des fichiers (cloud-file.show)', function () {
    $admin = cloudFileTestAdmin();
    $folder = cloudTestFolder();

    // Fichier 'pending' servi depuis le stockage LOCAL (local_uploads)
    $localPath = 'galleries/images/proxy-test.jpg';
    Storage::disk('local_uploads')->put($localPath, 'contenu-test');

    $file = CloudFile::create([
        'cloud_folder_id' => $folder->id,
        'fileable_type' => User::class,
        'fileable_id' => $admin->id,
        'file_name' => 'proxy-test.jpg',
        'file_type' => 'image',
        'mime_type' => 'image/jpeg',
        'file_size' => strlen('contenu-test'),
        'sync_status' => 'pending',
        'local_path' => $localPath,
    ]);

    $this->get(route('cloud-file.show', ['uuid' => $file->uuid]))
        ->assertOk();
});

it('protege l’accès aux fichiers cloud (non connecté → login)', function () {
    $this->get('/backend/cloud-files/index')->assertRedirect(route('login'));
    $this->get('/backend/cloud-files/sync')->assertRedirect(route('login'));
});
