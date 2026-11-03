<?php

declare(strict_types=1);

use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
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
 * Étape 12 — Blog : Catégories (`blog-categories`).
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

function blogAdmin(): User
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

function categoryPayload(array $overrides = []): array
{
    $langFr = \App\Models\Lang::where('code', 'fr')->firstOrFail();
    $langEn = \App\Models\Lang::where('code', 'en')->firstOrFail();
    $platform = \App\Models\CloudPlatform::firstOrFail();
    $uid = uniqid();

    return array_merge([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
        'cloud_platform_uuid' => $platform->uuid,
        'image' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Catégorie FR '.$uid, 'slug' => 'categorie-'.$uid, 'description' => 'Description FR.'],
            ['lang_id' => $langEn->id, 'name' => 'Category '.$uid, 'slug' => 'category-'.$uid, 'description' => 'English description.'],
        ],
    ], $overrides);
}

it('liste les catégories avec DataTables', function () {
    $admin = blogAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.blog-categories.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('blog-categories-table');
    expect($html)->toContain("$('#blog-categories-table').DataTable");
});

it('affiche la page de création de catégorie', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)
        ->get(route('backend.blog-categories.create'))
        ->assertOk();
});

it('crée une catégorie avec image et traductions', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)
        ->post(route('backend.blog-categories.store'), categoryPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(BlogCategory::count())->toBe(1);
    expect(BlogCategory::first()->translations()->count())->toBe(2);
    expect(BlogCategory::first()->cloudFiles()->count())->toBe(1);
});

it('affiche le détail d’une catégorie (show avec slug)', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('backend.blog-categories.store'), categoryPayload());

    $category = BlogCategory::firstOrFail();
    $slug = $category->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.blog-categories.show', ['blogCategory' => $category->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('active et désactive une catégorie', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('backend.blog-categories.store'), categoryPayload());
    $category = BlogCategory::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.blog-categories.deactivate', ['blogCategory' => $category->uuid]))
        ->assertRedirect();

    expect($category->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.blog-categories.activate', ['blogCategory' => $category->uuid]))
        ->assertRedirect();

    expect($category->fresh()->status->value)->toBe('active');
});

it('refuse la suppression d’une catégorie active puis autorise après désactivation', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('backend.blog-categories.store'), categoryPayload());
    $category = BlogCategory::firstOrFail();

    // Active → suppression refusée (règle du BlogCategoryService::backend_blog_categories_destroy)
    $this->actingAs($admin)
        ->from(route('backend.blog-categories.index'))
        ->delete(route('backend.blog-categories.destroy', ['blogCategory' => $category->uuid]))
        ->assertRedirect(route('backend.blog-categories.index'))
        ->assertSessionHas('error');

    expect(BlogCategory::find($category->id))->not->toBeNull();

    // Désactivée → suppression autorisée
    $category->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.blog-categories.destroy', ['blogCategory' => $category->uuid]))
        ->assertRedirect();

    expect(BlogCategory::find($category->id))->toBeNull();
});

it('passe une catégorie en corbeille puis la restaure', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('backend.blog-categories.store'), categoryPayload());
    $category = BlogCategory::firstOrFail();
    $category->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.blog-categories.destroy', ['blogCategory' => $category->uuid]))
        ->assertRedirect();

    expect(BlogCategory::find($category->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.blog-categories.trashed'))
        ->patch(route('backend.blog-categories.restore', ['uuid' => $category->uuid]))
        ->assertRedirect();

    expect(BlogCategory::find($category->id))->not->toBeNull();
});

it('déclare posts comme relation bloquante (dependentRelations)', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('backend.blog-categories.store'), categoryPayload());
    $category = BlogCategory::firstOrFail();

    $property = new ReflectionProperty($category, 'dependentRelations');
    expect($property->getValue($category))->toBe(['posts']);
});

it('refuse la mise à jour d\'une traduction se trouvant en corbeille (garde syncTranslations)', function () {
    $admin = blogAdmin();

    // Création d'une catégorie avec ses traductions
    $this->actingAs($admin)->post(route('backend.blog-categories.store'), categoryPayload());
    $category = BlogCategory::firstOrFail();

    $langFr = \App\Models\Lang::where('code', 'fr')->firstOrFail();

    // Passage de la traduction FR en corbeille (soft delete direct, simulant une
    // traduction supprimée via le gestionnaire de traductions)
    $translation = BlogCategoryTranslation::where('category_id', $category->id)
        ->where('lang_id', $langFr->id)
        ->firstOrFail();
    $translation->delete();

    // Tentative de mise à jour de la catégorie en resoumettant la traduction FR :
    // le garde de syncTranslations doit lever une exception (traduction trashed)
    // au lieu de la mettre à jour silencieusement.
    $payload = categoryPayload([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
    ]);

    $this->actingAs($admin)
        ->from(route('backend.blog-categories.index'))
        ->put(route('backend.blog-categories.update', ['blogCategory' => $category->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('error');

    // La traduction reste en corbeille, non modifiée
    $trashedTranslation = BlogCategoryTranslation::withTrashed()
        ->where('category_id', $category->id)
        ->where('lang_id', $langFr->id)
        ->firstOrFail();

    expect($trashedTranslation->trashed())->toBeTrue();
});
