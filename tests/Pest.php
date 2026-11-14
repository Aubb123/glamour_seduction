<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

pest()->extend(TestCase::class)
    // ->use(RefreshDatabase::class)
    ->in('Feature');

uses(TestCase::class)->in('Unit');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function something()
{
    // ..
}

/**
 * Reload the V1 routes after minimal version seeds are in place. Supplemental
 * route files are required explicitly because this helper resets the collection.
 */
function bootV1WebRoutes(): void
{
    $router = Route::getFacadeRoot();
    $router->setRoutes(new RouteCollection);

    Route::middleware('web')->group(function () {
        require base_path('routes/web.php');
    });

    require base_path('routes/product-warehouse-translations.php');
    require base_path('routes/product-stock-movements-audits-logs.php');
    require base_path('routes/product-variant-images.php');

    $router->getRoutes()->refreshNameLookups();
    $router->getRoutes()->refreshActionLookups();
}
