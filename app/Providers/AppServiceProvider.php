<?php

namespace App\Providers;

use App\Services\V1\VersionService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(VersionService::class, function () {
            return new VersionService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo formatDate($expression); ?>";
        });

    }
}
