<?php

/**
 * Routes : Audits & Journal d'activité des mouvements de stock (module Boutique).
 *
 * Ce fichier est volontairement isolé du monolithe `routes/web.php` :
 *  - chargé en production via `bootstrap/app.php` (withRouting -> then) ;
 *  - rechargé en test via `tests/Pest.php::bootV1WebRoutes()`.
 *
 * Il n'est actif que lorsque la version active est `v1` (même garde que web.php).
 *
 * NB : les mouvements n'exposent pas de permissions dédiées `audits`/`activity_logs`
 * (voir PermissionSeeder : `'product-stock-movements' => []`). Le droit d'ouverture
 * de ces vues de LECTURE est donc adossé à `product-stock-movements.show`.
 */

use App\Http\Controllers\V1\ProductStockMovementController as V1ProductStockMovementController;
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

        Route::controller(V1ProductStockMovementController::class)->group(function () {
            Route::get('/backend/product-stock-movements/audits/{productStockMovement}', 'backend_product_stock_movements_audits')->middleware(['permission:product-stock-movements.show'])->name('backend.product-stock-movements.audits');
            Route::get('/backend/product-stock-movements/activity-logs/{productStockMovement}', 'backend_product_stock_movements_activity_logs')->middleware(['permission:product-stock-movements.show'])->name('backend.product-stock-movements.activity-logs');
        });
    });
}
