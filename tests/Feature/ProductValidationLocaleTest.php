<?php

declare(strict_types=1);

use App\Http\Requests\V1\Products\Products\StoreProductRequest;
use App\Http\Requests\V1\Products\Products\UpdateProductRequest;

it('returns product validation messages in the active French or English locale', function () {
    $storeRequest = new StoreProductRequest;
    $updateRequest = new UpdateProductRequest;

    app()->setLocale('fr');
    expect($storeRequest->messages()['files.max'])->toBe('Vous pouvez joindre au maximum 4 images.')
        ->and($updateRequest->messages()['files.max'])->toBe('Vous pouvez joindre au maximum 4 images.')
        ->and($storeRequest->messages()['files.*.uploaded'])->toBe('Le téléversement de l’image a échoué. Veuillez réessayer.')
        ->and($updateRequest->messages()['files.*.uploaded'])->toBe('Le téléversement de l’image a échoué. Veuillez réessayer.');

    app()->setLocale('en');
    expect($storeRequest->messages()['files.max'])->toBe('You can attach up to 4 images.')
        ->and($updateRequest->messages()['files.max'])->toBe('You can attach up to 4 images.')
        ->and($storeRequest->messages()['files.*.uploaded'])->toBe('The image upload failed. Please try again.')
        ->and($updateRequest->messages()['files.*.uploaded'])->toBe('The image upload failed. Please try again.');
});

it('does not expose missing translation keys for product validation messages', function () {
    foreach (['fr', 'en'] as $locale) {
        app()->setLocale($locale);

        foreach ([new StoreProductRequest, new UpdateProductRequest] as $request) {
            foreach ($request->messages() as $message) {
                expect($message)->not->toContain('products-messages-validation');
            }
        }
    }
});
