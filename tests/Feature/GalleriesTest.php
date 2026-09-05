<?php

declare(strict_types=1);

use App\Models\Gallery;
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
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

/**
 * Étape 10 — Module Galeries (`galleries`).
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

function galleriesTestAdmin(): User
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

function galleryTestPayload(array $overrides = []): array
{
    $langFr = \App\Models\Lang::where('code', 'fr')->firstOrFail();
    $langEn = \App\Models\Lang::where('code', 'en')->firstOrFail();
    $platform = \App\Models\CloudPlatform::firstOrFail();

    return array_merge([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
        'cloud_platform_uuid' => $platform->uuid,
        'image' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
        'translations' => [
            ['lang_id' => $langFr->id, 'title' => 'Galerie '.uniqid(), 'description' => 'Description FR.'],
            ['lang_id' => $langEn->id, 'title' => 'Gallery '.uniqid(), 'description' => 'English description.'],
        ],
    ], $overrides);
}

it('liste les galeries avec DataTables', function () {
    $admin = galleriesTestAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.galleries.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('galleries-table');
    expect($html)->toContain("$('#galleries-table').DataTable");
});

it('affiche la page de création d’une galerie', function () {
    $admin = galleriesTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.galleries.create'))
        ->assertOk();
});

it('crée une galerie avec son image et ses traductions', function () {
    $admin = galleriesTestAdmin();

    $this->actingAs($admin)
        ->post(route('backend.galleries.store'), galleryTestPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Gallery::count())->toBe(1);
    $gallery = Gallery::first();
    expect($gallery->translations()->count())->toBe(2);
    expect($gallery->cloudFiles()->count())->toBe(1);
});

it('affiche le détail d’une galerie (show avec slug)', function () {
    $admin = galleriesTestAdmin();

    $this->actingAs($admin)->post(route('backend.galleries.store'), galleryTestPayload());

    $gallery = Gallery::firstOrFail();
    $slug = $gallery->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.galleries.show', ['gallery' => $gallery->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('bascule l’état actif/inactif d’une galerie (toggle-active)', function () {
    $admin = galleriesTestAdmin();

    $gallery = Gallery::create(['user_id' => $admin->id, 'sort_order' => random_int(1000, 99999), 'status' => 'active']);

    $this->actingAs($admin)
        ->patch(route('backend.galleries.toggle_active', ['gallery' => $gallery->uuid]))
        ->assertRedirect();

    expect($gallery->fresh()->status->value)->toBe('inactive');
});

it('passe une galerie en corbeille puis la restaure', function () {
    $admin = galleriesTestAdmin();

    $gallery = Gallery::create(['user_id' => $admin->id, 'sort_order' => random_int(1000, 99999), 'status' => 'inactive']);

    $this->actingAs($admin)
        ->from(route('backend.galleries.index'))
        ->delete(route('backend.galleries.destroy', ['gallery' => $gallery->uuid]))
        ->assertRedirect();

    expect(Gallery::find($gallery->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.galleries.trashed'))
        ->patch(route('backend.galleries.restore', ['uuid' => $gallery->uuid]))
        ->assertRedirect();

    expect(Gallery::find($gallery->id))->not->toBeNull();
});

it('protege l’accès au module galeries (non connecté → login)', function () {
    $this->get('/backend/galleries/index')->assertRedirect(route('login'));
});