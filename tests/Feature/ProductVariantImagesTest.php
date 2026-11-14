<?php

declare(strict_types=1);

use App\Http\Requests\V1\Products\Variants\StoreProductVariantRequest;
use App\Models\CloudFile;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses the parent product gallery when a variant has no own images', function () {
    $parentImage = new CloudFile(['file_type' => 'image', 'metadata' => ['page' => 1]]);
    $product = new Product;
    $product->setRelation('cloudFiles', collect([$parentImage]));
    $variant = new ProductVariant;
    $variant->setRelation('cloudFiles', collect());
    $variant->setRelation('product', $product);

    expect($variant->displayImages())->toHaveCount(1)
        ->and($variant->displayImages()->first())->toBe($parentImage);
});

it('uses the variant gallery when variant images exist', function () {
    $parentImage = new CloudFile(['file_type' => 'image', 'metadata' => ['page' => 1]]);
    $variantImage = new CloudFile(['file_type' => 'image', 'metadata' => ['page' => 1]]);
    $product = new Product;
    $product->setRelation('cloudFiles', collect([$parentImage]));
    $variant = new ProductVariant;
    $variant->setRelation('cloudFiles', collect([$variantImage]));
    $variant->setRelation('product', $product);

    expect($variant->displayImages())->toHaveCount(1)
        ->and($variant->displayImages()->first())->toBe($variantImage);
});

it('allows a variant without images and rejects a fifth image', function () {
    $rules = app(StoreProductVariantRequest::class)->rules();
    expect(Validator::make(['sku' => 'SKU-OPTIONAL', 'status' => 'active'], $rules)->passes())->toBeTrue();

    $validator = Validator::make([
        'sku' => 'SKU-FIVE',
        'status' => 'active',
        'files' => [
            UploadedFile::fake()->create('a.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('b.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('c.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('d.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('e.jpg', 20, 'image/jpeg'),
        ],
    ], $rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('files'))->toBeTrue();
});
