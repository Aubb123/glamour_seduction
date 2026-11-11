<?php

declare(strict_types=1);

use App\Models\Product;
use App\Services\V1\Controllers\ProductService;
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

it('refuse un ajout qui ferait dépasser quatre images au total', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)
        ->post(route('backend.products.store'), productPayload($setup))
        ->assertRedirect()
        ->assertSessionHas('success');

    $product = Product::firstOrFail();
    expect($product->cloudFiles()->where('file_type', 'image')->count())->toBe(1);

    expect(fn () => app(ProductService::class)->backend_products_update($product, [
        'files' => [
            UploadedFile::fake()->create('extra1.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('extra2.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('extra3.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('extra4.jpg', 100, 'image/jpeg'),
        ],
    ]))->toThrow(InvalidArgumentException::class);

    expect($product->cloudFiles()->where('file_type', 'image')->count())->toBe(1);
});
