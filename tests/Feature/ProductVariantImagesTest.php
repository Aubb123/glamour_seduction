<?php

declare(strict_types=1);

use App\Enums\Products\Products\ProductStatus;
use App\Http\Requests\V1\Products\Variants\StoreProductVariantRequest;
use App\Models\CloudFile;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

it('uses the parent gallery when a variant has no own images', function () {
    $parentImage = new CloudFile(['file_type' => 'image', 'metadata' => ['page' => 1]]);
    $product = new Product;
    $product->setRelation('cloudFiles', collect([$parentImage]));
    $variant = new ProductVariant;
    $variant->setRelation('cloudFiles', collect());
    $variant->setRelation('product', $product);

    expect($variant->displayImages())->toHaveCount(1)
        ->and($variant->displayImages()->first())->toBe($parentImage);
});

it('prefers variant-owned gallery when images exist', function () {
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

it('allows zero images but rejects more than four files on variant creation', function () {
    $rules = app(StoreProductVariantRequest::class)->rules();
    expect(Validator::make(['sku' => 'SKU-NO-IMAGE', 'status' => 'active'], $rules)->passes())->toBeTrue();

    $validator = Validator::make([
        'sku' => 'SKU-FIVE-IMAGES',
        'status' => 'active',
        'files' => [
            UploadedFile::fake()->create('one.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('two.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('three.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('four.jpg', 20, 'image/jpeg'),
            UploadedFile::fake()->create('five.jpg', 20, 'image/jpeg'),
        ],
    ], $rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('files'))->toBeTrue();
});
