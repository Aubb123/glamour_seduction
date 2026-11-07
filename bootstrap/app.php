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
        // Routes additionnelles (hors monolithe routes/web.php) : traductions d'entrepôts.
        // NB : tests/Pest.php::bootV1WebRoutes() recharge aussi ce fichier après avoir
        // vidé la collection de routes (le chargement initial n'a lieu qu'une fois au boot).
        then: function (): void {
            require __DIR__.'/../routes/product-warehouse-translations.php';
        },
    )
    ->withSchedule(function (Schedule $schedule): void {
        // Migration des fichiers cloud vers Backblaze B2 toutes les 5 minutes.
        // sansOverlapping() : bloque l'exécution si une migration est encore en cours
        // (évite le double traitement des mêmes lignes / l'upload concurrent sur B2).
        // appendOutputTo() : consigne la sortie (succès/échecs) dans storage/logs/cloud-sync.log.
        $schedule->command('cloud:sync')->everyFiveMinutes()
            ->withoutOverlapping() // évite le double traitement des mêmes lignes / l'upload concurrent sur B2
            ->appendOutputTo(storage_path('logs/cloud-sync.log')); // appendOutputTo() : consigne la sortie (succès/échecs) dans storage/logs/cloud-sync.log
        // Publication automatique des articles programmés (scheduled → published)
        $schedule->command('blog:publish-scheduled')->everyMinute()->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {

        // Middleware global web
        $middleware->web(append: [
            SetLocale::class,
            // $middleware->append(\App\Http\Middleware\Web\V1\SetLocale::class);
        ]);

        // Alias des middlewares Spatie
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            //
            'v1.web.check.active.role' => CheckActiveRole::class,
            'v1.web.check.cloud.files.sync' => CheckCloudFilesSync::class,
            //
            'v1.web.auth' => Auth::class,
            'v1.web.guest' => Guest::class,
            'v1.web.check.user.email' => CheckUserEmail::class,
            'v1.web.check.user.phone' => CheckUserPhone::class,
            'v1.web.check.active.user.account' => CheckActiveUserAccount::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Journalise les tentatives d'accès à une ressource introuvable lors du
        // binding implicite des routes (ModelNotFoundException), pour tous les
        // modules migrés.
        //
        // NB : ModelNotFoundException figure dans internalDontReport du Handler
        // (report() jamais appelé) ET est convertie en NotFoundHttpException par
        // prepareException() avant renderViaCallbacks(). On intercepte donc la
        // NotFoundHttpException et on passe par ->getPrevious() pour retrouver
        // la MNF d'origine. La closure ne renvoie rien (null) pour conserver le
        // rendu 404 standard de Laravel.
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                app(ActivityLogger::class)->reportModelNotFound($previous);
            }

            return null;
        });

        // Journalise TOUTE exception serveur non gérée (5xx : erreurs de rendu Blade
        // hors du try/catch des contrôleurs, erreurs PHP, etc.) dans activity_logs,
        // afin qu'elle soit consultable depuis le dashboard (backend/activity-logs/index).
        //
        // Les exceptions « attendues » sont ignorées pour éviter le bruit :
        //  - ValidationException (422) : erreurs de validation de formulaire ;
        //  - HTTP < 500 (404/403/405/419...) : erreurs client, pas des bugs serveur.
        $exceptions->reportable(function (\Throwable $e): void {
            if ($e instanceof ValidationException) {
                return;
            }

            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
                return;
            }

            app(ActivityLogger::class)->reportServerError($e);
        });

    })->create();
