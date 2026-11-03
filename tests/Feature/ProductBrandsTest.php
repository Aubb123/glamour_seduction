<?php

declare(strict_types=1);

use App\Models\CloudPlatform;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\ProductBrand;
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
 * Module Produits — Marques (`product-brands`).
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

function productBrandAdmin(): User
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

function productBrandPayload(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();
    $platform = CloudPlatform::firstOrFail();
    $uid = uniqid();

    return array_merge([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
        'cloud_platform_uuid' => $platform->uuid,
        'image' => UploadedFile::fake()->create('logo.jpg', 100, 'image/jpeg'),
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Marque FR '.$uid, 'slug' => 'marque-'.$uid, 'description' => 'Description FR.'],
            ['lang_id' => $langEn->id, 'name' => 'Brand '.$uid, 'slug' => 'brand-'.$uid, 'description' => 'English description.'],
        ],
    ], $overrides);
}

it('liste les marques de produits avec DataTables', function () {
    $admin = productBrandAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-brands.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-brands-table');
    expect($html)->toContain("$('#product-brands-table').DataTable");
});

it('affiche la page de création de marque', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-brands.create'))
        ->assertOk();
});

it('crée une marque avec image et traductions', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-brands.store'), productBrandPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductBrand::count())->toBe(1);
    expect(ProductBrand::first()->translations()->count())->toBe(2);
    expect(ProductBrand::first()->cloudFiles()->count())->toBe(1);
});

it('affiche le détail d’une marque (show avec slug)', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)->post(route('backend.product-brands.store'), productBrandPayload());

    $brand = ProductBrand::firstOrFail();
    $slug = $brand->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.product-brands.show', ['productBrand' => $brand->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('met à jour une marque et ses traductions', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)->post(route('backend.product-brands.store'), productBrandPayload());
    $brand = ProductBrand::firstOrFail();

    $payload = productBrandPayload([
        'sort_order' => random_int(1000, 99999),
        'status' => 'inactive',
    ]);

    $this->actingAs($admin)
        ->put(route('backend.product-brands.update', ['productBrand' => $brand->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($brand->fresh()->status->value)->toBe('inactive');
});

it('active et désactive une marque', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)->post(route('backend.product-brands.store'), productBrandPayload());
    $brand = ProductBrand::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.product-brands.deactivate', ['productBrand' => $brand->uuid]))
        ->assertRedirect();

    expect($brand->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.product-brands.activate', ['productBrand' => $brand->uuid]))
        ->assertRedirect();

    expect($brand->fresh()->status->value)->toBe('active');
});

it('refuse la suppression d’une marque active puis autorise après désactivation', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)->post(route('backend.product-brands.store'), productBrandPayload());
    $brand = ProductBrand::firstOrFail();

    // Active → suppression refusée (règle du ProductBrandService::backend_product_brands_destroy)
    $this->actingAs($admin)
        ->from(route('backend.product-brands.index'))
        ->delete(route('backend.product-brands.destroy', ['productBrand' => $brand->uuid]))
        ->assertRedirect(route('backend.product-brands.index'))
        ->assertSessionHas('error');

    expect(ProductBrand::find($brand->id))->not->toBeNull();

    // Désactivée → suppression autorisée
    $brand->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-brands.destroy', ['productBrand' => $brand->uuid]))
        ->assertRedirect();

    expect(ProductBrand::find($brand->id))->toBeNull();
});

it('passe une marque en corbeille puis la restaure', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)->post(route('backend.product-brands.store'), productBrandPayload());
    $brand = ProductBrand::firstOrFail();
    $brand->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-brands.destroy', ['productBrand' => $brand->uuid]))
        ->assertRedirect();

    expect(ProductBrand::find($brand->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-brands.trashed'))
        ->patch(route('backend.product-brands.restore', ['uuid' => $brand->uuid]))
        ->assertRedirect();

    expect(ProductBrand::find($brand->id))->not->toBeNull();
});

it('déclare products comme relation bloquante (dependentRelations, module Produits)', function () {
    $admin = productBrandAdmin();

    $this->actingAs($admin)->post(route('backend.product-brands.store'), productBrandPayload());
    $brand = ProductBrand::firstOrFail();

    $property = new ReflectionProperty($brand, 'dependentRelations');
    expect($property->getValue($brand))->toBe(['products']);
});
