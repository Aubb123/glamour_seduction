<?php

use App\Http\Middleware\Web\V1\Auth;
use App\Http\Middleware\Web\V1\CheckActiveRole;
use App\Http\Middleware\Web\V1\CheckActiveUserAccount;
use App\Http\Middleware\Web\V1\CheckCloudFilesSync;
use App\Http\Middleware\Web\V1\CheckUserEmail;
use App\Http\Middleware\Web\V1\CheckUserPhone;
use App\Http\Middleware\Web\V1\Guest;
use App\Http\Middleware\Web\V1\SetLocale;
use App\Services\V1\ActivityLogger;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            require __DIR__.'/../routes/product-warehouse-translations.php';
            require __DIR__.'/../routes/product-stock-movements-audits-logs.php';
            require __DIR__.'/../routes/product-variant-images.php';
        },
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('cloud:sync')->everyFiveMinutes()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/cloud-sync.log'));
        $schedule->command('blog:publish-scheduled')->everyMinute()->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [SetLocale::class]);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'v1.web.check.active.role' => CheckActiveRole::class,
            'v1.web.check.cloud.files.sync' => CheckCloudFilesSync::class,
            'v1.web.auth' => Auth::class,
            'v1.web.guest' => Guest::class,
            'v1.web.check.user.email' => CheckUserEmail::class,
            'v1.web.check.user.phone' => CheckUserPhone::class,
            'v1.web.check.active.user.account' => CheckActiveUserAccount::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            $previous = $e->getPrevious();
            if ($previous instanceof ModelNotFoundException) {
                app(ActivityLogger::class)->reportModelNotFound($previous);
            }
            return null;
        });
        $exceptions->reportable(function (\Throwable $e): void {
            if ($e instanceof ValidationException) return;
            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) return;
            app(ActivityLogger::class)->reportServerError($e);
        });
    })->create();
