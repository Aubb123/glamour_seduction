<?php

/**
 * Routes : Traductions des entrepôts (module Boutique).
 *
 * Ce fichier est volontairement isolé du monolithe `routes/web.php` (117 Ko) :
 *  - chargé en production via `bootstrap/app.php` (withRouting -> then) ;
 *  - rechargé en test via `tests/Pest.php::bootV1WebRoutes()`.
 *
 * Il n'est actif que lorsque la version active est `v1` (même garde que web.php).
 */

use App\Http\Controllers\V1\ProductWarehouseTranslationController as V1ProductWarehouseTranslationController;
use App\Services\V1\VersionService;
use Illuminate\Support\Facades\Route;

$version = app(VersionService::class)->getActive();

if ($version && $version->code === 'v1') {

    // Même pile de middlewares que le reste du backend v1 (voir routes/web.php).
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

        Route::controller(V1ProductWarehouseTranslationController::class)->group(function () {
            Route::delete('/backend/product-warehouse-translations/destroy/{uuid}', 'backend_product_warehouse_translations_destroy')->middleware(['permission:product-warehouse-translations.destroy'])->name('backend.product-warehouse-translations.destroy');
            Route::get('/backend/product-warehouse-translations/trashed/{warehouse_uuid}', 'backend_product_warehouse_translations_trashed')->middleware(['permission:product-warehouse-translations.trashed'])->name('backend.product-warehouse-translations.trashed');
            Route::patch('/backend/product-warehouse-translations/restore/{uuid}', 'backend_product_warehouse_translations_restore')->middleware(['permission:product-warehouse-translations.restore'])->name('backend.product-warehouse-translations.restore');
            Route::delete('/backend/product-warehouse-translations/force-destroy/{uuid}', 'backend_product_warehouse_translations_force_destroy')->middleware(['permission:product-warehouse-translations.force_destroy'])->name('backend.product-warehouse-translations.force_destroy');
        });
    });
}
