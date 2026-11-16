<?php

declare(strict_types=1);

use App\Http\Requests\V1\Products\Variants\StoreProductVariantRequest;
use App\Http\Requests\V1\Products\Variants\UpdateProductVariantRequest;
use App\Models\CloudFile;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

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

it('prefers the variant gallery when variant images exist', function () {
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

it('allows zero images and rejects a fifth image on variant creation', function () {
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

it('validates the remove_cloud_file_uuids list on update', function () {
    $rules = app(UpdateProductVariantRequest::class)->rules();

    $uuid = fn (): string => (string) Str::uuid();

    // Liste valide : quatre UUID distincts.
    expect(Validator::make([
        'remove_cloud_file_uuids' => [$uuid(), $uuid(), $uuid(), $uuid()],
    ], $rules)->passes())->toBeTrue();

    // Plus de quatre UUID → refusé.
    $tooMany = Validator::make([
        'remove_cloud_file_uuids' => [$uuid(), $uuid(), $uuid(), $uuid(), $uuid()],
    ], $rules);

    expect($tooMany->fails())->toBeTrue()
        ->and($tooMany->errors()->has('remove_cloud_file_uuids'))->toBeTrue();

    // Doublon → règle distinct.
    $duplicate = $uuid();
    $duplicates = Validator::make([
        'remove_cloud_file_uuids' => [$duplicate, $duplicate],
    ], $rules);

    expect($duplicates->fails())->toBeTrue()
        ->and($duplicates->errors()->has('remove_cloud_file_uuids.0'))->toBeTrue();
});

it('inherits the parent product base price when the variant has no price', function () {
    $product = new Product(['base_price' => 19990]);

    $inherited = new ProductVariant(['price' => null]);
    $inherited->setRelation('product', $product);

    $priced = new ProductVariant(['price' => 24990]);
    $priced->setRelation('product', $product);

    expect($inherited->effectivePrice())->toBe('19990.00')
        ->and($priced->effectivePrice())->toBe('24990.00');
});
