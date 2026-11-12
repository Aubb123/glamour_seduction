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

it('does not return untranslated product validation keys for uploaded files', function () {
    foreach (['fr', 'en'] as $locale) {
        app()->setLocale($locale);

        foreach ([new StoreProductRequest, new UpdateProductRequest] as $request) {
            foreach ($request->messages() as $message) {
                expect($message)->not->toContain('products-messages-validation');
            }
        }
    }
});
