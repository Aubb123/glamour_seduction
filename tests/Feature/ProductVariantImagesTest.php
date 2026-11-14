<?php

declare(strict_types=1);

use App\Http\Requests\V1\Products\Variants\StoreProductVariantRequest;
use App\Models\CloudFile;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

it('uses the product image gallery when a variant has no images', function () {
    $productImage = new CloudFile(['file_type' => 'image', 'metadata' => ['page' => 1]]);
    $product = new Product;
    $product->setRelation('cloudFiles', collect([$productImage]));

    $variant = new ProductVariant;
    $variant->setRelation('cloudFiles', collect());
    $variant->setRelation('product', $product);

    expect($variant->displayImages())->toHaveCount(1)
        ->and($variant->displayImages()->first())->toBe($productImage);
});

it('prefers the variant-specific gallery over the parent product gallery', function () {
    $productImage = new CloudFile(['file_type' => 'image', 'metadata' => ['page' => 1]]);
    $variantImage = new CloudFile(['file_type' => 'image', 'metadata' => ['page' => 1]]);

    $product = new Product;
    $product->setRelation('cloudFiles', collect([$productImage]));

    $variant = new ProductVariant;
    $variant->setRelation('cloudFiles', collect([$variantImage]));
    $variant->setRelation('product', $product);

    expect($variant->displayImages())->toHaveCount(1)
        ->and($variant->displayImages()->first())->toBe($variantImage);
});

it('allows creating a variant without images and rejects more than four uploaded images', function () {
    $request = app(StoreProductVariantRequest::class);
    $rules = $request->rules();

    $withoutImages = Validator::make([
        'sku' => 'SKU-OPTIONAL-IMAGE-1',
        'status' => 'active',
    ], $rules);

    expect($withoutImages->passes())->toBeTrue();

    $tooManyImages = Validator::make([
        'sku' => 'SKU-TOO-MANY-IMAGES',
        'status' => 'active',
        'files' => [
            UploadedFile::fake()->image('one.jpg'),
            UploadedFile::fake()->image('two.jpg'),
            UploadedFile::fake()->image('three.jpg'),
            UploadedFile::fake()->image('four.jpg'),
            UploadedFile::fake()->image('five.jpg'),
        ],
    ], $rules);

    expect($tooManyImages->fails())->toBeTrue()
        ->and($tooManyImages->errors()->has('files'))->toBeTrue();
});
