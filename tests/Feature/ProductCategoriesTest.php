<?php

declare(strict_types=1);

use App\Models\CloudPlatform;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\ProductCategory;
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
 * Module Produits — Catégories (`product-categories`).
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

function productCategoryAdmin(): User
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

function productCategoryPayload(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();
    $platform = CloudPlatform::firstOrFail();
    $uid = uniqid();

    return array_merge([
        'parent_id' => null,
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

it('liste les catégories de produits avec DataTables', function () {
    $admin = productCategoryAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-categories.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-categories-table');
    expect($html)->toContain("$('#product-categories-table').DataTable");
});

it('affiche la page de création de catégorie', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-categories.create'))
        ->assertOk();
});

it('crée une catégorie racine avec image et traductions', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-categories.store'), productCategoryPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductCategory::count())->toBe(1);
    expect(ProductCategory::first()->parent_id)->toBeNull();
    expect(ProductCategory::first()->translations()->count())->toBe(2);
    expect(ProductCategory::first()->cloudFiles()->count())->toBe(1);
});

it('crée une sous-catégorie rattachée à sa catégorie parente', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $parent = ProductCategory::firstOrFail();

    $payload = productCategoryPayload(['parent_id' => $parent->id, 'status' => 'inactive']);

    $this->actingAs($admin)
        ->post(route('backend.product-categories.store'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $child = ProductCategory::query()->where('parent_id', $parent->id)->firstOrFail();

    expect($child->parent->id)->toBe($parent->id);
});

it('affiche le détail d’une catégorie (show avec slug)', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());

    $category = ProductCategory::firstOrFail();
    $slug = $category->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.product-categories.show', ['productCategory' => $category->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('met à jour une catégorie et ses traductions', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $category = ProductCategory::firstOrFail();

    $payload = productCategoryPayload([
        'sort_order' => random_int(1000, 99999),
        'status' => 'inactive',
    ]);

    $this->actingAs($admin)
        ->put(route('backend.product-categories.update', ['productCategory' => $category->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($category->fresh()->status->value)->toBe('inactive');
});

it('refuse de rattacher une catégorie à elle-même (cycle)', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $category = ProductCategory::firstOrFail();

    $payload = productCategoryPayload([
        'parent_id' => $category->id,
        'sort_order' => random_int(1000, 99999),
    ]);

    $this->actingAs($admin)
        ->from(route('backend.product-categories.index'))
        ->put(route('backend.product-categories.update', ['productCategory' => $category->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($category->fresh()->parent_id)->toBeNull();
});

it('active et désactive une catégorie', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $category = ProductCategory::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.product-categories.deactivate', ['productCategory' => $category->uuid]))
        ->assertRedirect();

    expect($category->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.product-categories.activate', ['productCategory' => $category->uuid]))
        ->assertRedirect();

    expect($category->fresh()->status->value)->toBe('active');
});

it('refuse la suppression d’une catégorie active puis autorise après désactivation', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $category = ProductCategory::firstOrFail();

    // Active → suppression refusée (règle du ProductCategoryService::backend_product_categories_destroy)
    $this->actingAs($admin)
        ->from(route('backend.product-categories.index'))
        ->delete(route('backend.product-categories.destroy', ['productCategory' => $category->uuid]))
        ->assertRedirect(route('backend.product-categories.index'))
        ->assertSessionHas('error');

    expect(ProductCategory::find($category->id))->not->toBeNull();

    // Désactivée → suppression autorisée
    $category->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-categories.destroy', ['productCategory' => $category->uuid]))
        ->assertRedirect();

    expect(ProductCategory::find($category->id))->toBeNull();
});

it('refuse la suppression d’une catégorie parente ayant des sous-catégories', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $parent = ProductCategory::firstOrFail();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload([
        'parent_id' => $parent->id,
        'status' => 'inactive',
    ]));

    $parent->update(['status' => 'inactive']);

    // La catégorie parente a une sous-catégorie → suppression refusée
    $this->actingAs($admin)
        ->from(route('backend.product-categories.index'))
        ->delete(route('backend.product-categories.destroy', ['productCategory' => $parent->uuid]))
        ->assertRedirect(route('backend.product-categories.index'))
        ->assertSessionHas('error');

    expect(ProductCategory::find($parent->id))->not->toBeNull();
});

it('passe une catégorie en corbeille puis la restaure', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $category = ProductCategory::firstOrFail();
    $category->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-categories.destroy', ['productCategory' => $category->uuid]))
        ->assertRedirect();

    expect(ProductCategory::find($category->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-categories.trashed'))
        ->patch(route('backend.product-categories.restore', ['uuid' => $category->uuid]))
        ->assertRedirect();

    expect(ProductCategory::find($category->id))->not->toBeNull();
});

it('déclare products et children comme relations bloquantes (dependentRelations)', function () {
    $admin = productCategoryAdmin();

    $this->actingAs($admin)->post(route('backend.product-categories.store'), productCategoryPayload());
    $category = ProductCategory::firstOrFail();

    $property = new ReflectionProperty($category, 'dependentRelations');
    expect($property->getValue($category))->toBe(['products', 'children']);
});
