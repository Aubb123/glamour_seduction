<?php

declare(strict_types=1);

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
 * Étape 13 — Module Cloud : Plateformes (`cloud-platforms`).
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

function cloudTestAdmin(): User
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

function cloudPlatformPayload(array $overrides = []): array
{
    $uid = uniqid();

    return array_merge([
        'name' => 'backblaze_b2',
        'label' => 'Backblaze B2 '.$uid,
        'disk_name' => 'b2',
        'api_endpoint' => 'https://s3.us-west-004.backblazeb2.com',
        'api_key' => 'key-'.$uid,
        'api_secret' => 'secret-'.$uid,
        'description' => 'Plateforme de test '.$uid,
        'metadata' => json_encode(['bucket' => 'test-bucket']),
        'status' => 'inactive',
    ], $overrides);
}

it('liste les plateformes cloud avec DataTables', function () {
    $admin = cloudTestAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.cloud-platforms.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('cloud-platforms-table');
    expect($html)->toContain("$('#cloud-platforms-table').DataTable");
});

it('affiche la page de création de plateforme', function () {
    $admin = cloudTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.cloud-platforms.create'))
        ->assertOk();
});

it('crée une plateforme cloud inactive', function () {
    $admin = cloudTestAdmin();

    $this->actingAs($admin)
        ->post(route('backend.cloud-platforms.store'), cloudPlatformPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(CloudPlatform::count())->toBe(2);
});

it('refuse une plateforme avec un nom non supporté', function () {
    $admin = cloudTestAdmin();

    $payload = cloudPlatformPayload(['name' => 'mega']);
    $payload['label'] = 'Mega '.$payload['label'];

    $this->actingAs($admin)
        ->post(route('backend.cloud-platforms.store'), $payload)
        ->assertSessionHasErrors(['name']);

    expect(CloudPlatform::where('label', 'like', 'Mega %')->exists())->toBeFalse();
});

it('affiche le détail, les dossiers et les fichiers d’une plateforme (slug)', function () {
    $admin = cloudTestAdmin();

    $platform = CloudPlatform::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.cloud-platforms.show', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('backend.cloud-platforms.folders', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('backend.cloud-platforms.files', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]))
        ->assertOk();
});

it('bascule l’état actif/inactif d’une plateforme (toggle-active)', function () {
    $admin = cloudTestAdmin();

    $platform = CloudPlatform::where('status', 'active')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.cloud-platforms.toggle_active', ['cloudPlatform' => $platform->uuid]))
        ->assertRedirect();

    expect($platform->fresh()->status->value)->toBe('inactive');
});

it('crée l’architecture de dossiers (architecture-store)', function () {
    $admin = cloudTestAdmin();

    $platform = CloudPlatform::firstOrFail();
    $foldersCount = $platform->folders()->count();

    $this->actingAs($admin)
        ->post(route('backend.cloud-platforms.architecture_store', ['cloudPlatform' => $platform->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    // L'architecture a créé/confirmé les dossiers users/blogs/galleries (images + vidéos)
    expect($platform->folders()->count())->toBeGreaterThanOrEqual($foldersCount);
    expect($platform->folders()->whereNotNull('entity_table')->count())->toBeGreaterThan(0);
});

it('refuse la suppression d’une plateforme active puis autorise après désactivation et purge des dossiers', function () {
    $admin = cloudTestAdmin();

    $platform = CloudPlatform::where('status', 'active')->firstOrFail();

    // Active → suppression refusée (règle du CloudPlatformService::destroy)
    $this->actingAs($admin)
        ->from(route('backend.cloud-platforms.index'))
        ->delete(route('backend.cloud-platforms.destroy', ['cloudPlatform' => $platform->uuid]))
        ->assertRedirect(route('backend.cloud-platforms.index'))
        ->assertSessionHas('error');

    expect(CloudPlatform::find($platform->id))->not->toBeNull();

    // Désactivée + dossiers purgés (dependentRelations = ['folders']) → suppression autorisée
    $platform->update(['status' => 'inactive']);
    $platform->folders()->delete();

    $this->actingAs($admin)
        ->delete(route('backend.cloud-platforms.destroy', ['cloudPlatform' => $platform->uuid]))
        ->assertRedirect();

    expect(CloudPlatform::find($platform->id))->toBeNull();
});

it('passe une plateforme en corbeille puis la restaure', function () {
    $admin = cloudTestAdmin();

    // Le seeder crée une plateforme active avec son architecture de dossiers.
    // Destroy exige : status inactive + aucun dossier dépendant.
    $platform = CloudPlatform::firstOrFail();
    $platform->update(['status' => 'inactive']);
    $platform->folders()->delete();

    $this->actingAs($admin)
        ->delete(route('backend.cloud-platforms.destroy', ['cloudPlatform' => $platform->uuid]))
        ->assertRedirect();

    $this->actingAs($admin)
        ->from(route('backend.cloud-platforms.trashed'))
        ->patch(route('backend.cloud-platforms.restore', ['uuid' => $platform->uuid]))
        ->assertRedirect();

    expect(CloudPlatform::find($platform->id))->not->toBeNull();
});

it('masque api_key et api_secret sur le rendu des plateformes', function () {
    $admin = cloudTestAdmin();

    // Plateforme avec secrets NON nuls (le seeder en crée une sans secrets)
    $payload = cloudPlatformPayload();
    $this->actingAs($admin)
        ->post(route('backend.cloud-platforms.store'), $payload)
        ->assertRedirect();

    $platform = CloudPlatform::where('label', $payload['label'])->firstOrFail();
    expect($platform->api_key)->not->toBeNull();

    $html = $this->actingAs($admin)
        ->get(route('backend.cloud-platforms.show', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]))
        ->assertOk()
        ->getContent();

    // Les secrets ne doivent pas apparaître en clair dans le HTML
    expect($html)->not->toContain((string) $platform->api_key);
    expect($html)->not->toContain((string) $platform->api_secret);
});

it('protege l’accès au module plateformes (non connecté → login)', function () {
    $this->get('/backend/cloud-platforms/index')->assertRedirect(route('login'));
});
