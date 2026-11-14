<?php

use App\Http\Controllers\V1\ProductVariantController as V1ProductVariantController;
use App\Services\V1\VersionService;
use Illuminate\Support\Facades\Route;

/* Optional, version-aware endpoint for deleting a product variant image. */
$version = app(VersionService::class)->getActive();

if ($version && $version->code === 'v1') {
    Route::middleware([
        'web',
        'v1.web.auth',
        'v1.web.check.active.user.account',
        'v1.web.check.user.email',
        'v1.web.check.user.phone',
        'role:administrator|developer|moderator|support',
        'v1.web.check.active.role',
        'v1.web.check.cloud.files.sync',
    ])->group(function () {
        Route::delete('/backend/product-variants/{productVariant}/images/{cloudFile}', [V1ProductVariantController::class, 'backend_product_variants_destroy_file'])
            ->middleware(['permission:product-variants.update'])
            ->name('backend.product-variants.destroy_file');
    });
}
