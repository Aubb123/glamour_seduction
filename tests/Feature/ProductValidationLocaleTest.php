<?php

declare(strict_types=1);

use App\Http\Requests\V1\Products\Products\StoreProductRequest;
use App\Http\Requests\V1\Products\Products\UpdateProductRequest;

it('returns localized product validation messages in French and English', function () {
    $storeRequest = new StoreProductRequest;
    $updateRequest = new UpdateProductRequest;

    app()->setLocale('fr');
    expect($storeRequest->messages()['files.max'])->toBe('Vous pouvez joindre au maximum 4 images.');
    expect($updateRequest->messages()['files.max'])->toBe('Vous pouvez joindre au maximum 4 images.');

    app()->setLocale('en');
    expect($storeRequest->messages()['files.max'])->toBe('You can attach up to 4 images.');
    expect($updateRequest->messages()['files.max'])->toBe('You can attach up to 4 images.');
});

it('maps uploaded-file errors to localized product messages', function () {
    $store = (new StoreProductRequest)->messages();
    $update = (new UpdateProductRequest)->messages();

    expect($store['files.*.uploaded'])->toBe(__('v1/backend/views/pages/products/products/messages/products-messages-validation.products.files.uploaded'));
    expect($update['files.*.uploaded'])->toBe(__('v1/backend/views/pages/products/products/messages/products-messages-validation.products.files.uploaded'));
});
