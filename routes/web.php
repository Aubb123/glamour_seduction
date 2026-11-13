<?php

use App\Http\Controllers\V1\ProductVariantController as V1ProductVariantController;
use Illuminate\Support\Facades\Route;

// Route dédiée à la suppression d'une image associée à une variante.
Route::controller(V1ProductVariantController::class)->group(function () {
    Route::delete('/backend/product-variants/{productVariant}/images/{cloudFile}', 'backend_product_variants_destroy_file')
        ->middleware(['permission:product-variants.update'])
        ->name('backend.product-variants.destroy_file');
});
