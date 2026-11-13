<?php

namespace App\Providers;

use App\Http\Controllers\V1\ProductVariantController as V1ProductVariantController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class ProductVariantImageRouteProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/product-variant-image-removal.php'));
        });
    }
}
