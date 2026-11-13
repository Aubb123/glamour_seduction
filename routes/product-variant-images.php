<?php

use App\Http\Controllers\V1\ProductVariantController;
use Illuminate\Support\Facades\Route;

// Route dédiée à la suppression d'une image attachée à une variante précise.
Route::delete('/backend/product-variants/{productVariant}/images/{cloudFile}', [ProductVariantController::class, 'backend_product_variants_destroy_file'])
    ->middleware(['permission:product-variants.update'])
    ->name('backend.product-variants.destroy_file');
