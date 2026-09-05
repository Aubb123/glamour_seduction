<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    // ->use(RefreshDatabase::class)
    ->in('Feature');

// Les tests Unit du projet ont aussi besoin de l'application Laravel bootée
// (enums avec __(), helpers qui font app()->getLocale(), chemins app(), ...).
uses(TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/*
|--------------------------------------------------------------------------
| Routes V1 pour les tests Feature
|--------------------------------------------------------------------------
|
| routes/web.php n'enregistre les routes que si VersionService::getActive()
| renvoie une version active 'v1' — or au boot de l'application (avant les
| migrations RefreshDatabase), la table `platform_versions` n'existe pas.
|
| Après avoir seedé les données minimales (langs + platform_versions), on
| vide la collection de routes puis on recharge routes/web.php pour que
| toutes les routes V1 (auth, backend, proxy…) soient disponibles en test.
|
*/
function bootV1WebRoutes(): void
{
    $router = \Illuminate\Support\Facades\Route::getFacadeRoot();
    $router->setRoutes(new \Illuminate\Routing\RouteCollection);

    // routes/web.php est normalement chargé par le framework DANS le groupe de
    // middleware 'web' (session, partage de $errors, CSRF…). On reproduit ce
    // comportement pour que les routes ré-enregistrées fonctionnent correctement.
    \Illuminate\Support\Facades\Route::middleware('web')->group(function () {
        require base_path('routes/web.php');
    });

    // Les noms de routes sont résolus via la table de lookups (`nameList`),
    // qui n'est rafraîchie qu'à la compilation. On force le refresh pour que
    // route('login') & co. fonctionnent immédiatement dans les tests.
    $router->getRoutes()->refreshNameLookups();
    $router->getRoutes()->refreshActionLookups();
}
