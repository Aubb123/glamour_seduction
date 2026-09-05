<?php

declare(strict_types=1);

use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\LangSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Préparation commune : langues + version v1 + routes V1
|--------------------------------------------------------------------------
*/
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    bootV1WebRoutes();
});

/**
 * Petite aide : crée un pays (FK users.country_id) + un utilisateur.
 */
function authTestUser(array $overrides = []): User
{
    $continent = GeoContinent::create([
        'code' => 'AF', 'population' => 1000000, 'area_km2' => 100000,
        'file_path' => 'c.svg', 'sort_order' => 1, 'status' => 'active',
    ]);

    $country = GeoCountry::create([
        'continent_id' => $continent->id, 'code' => 'BEN', 'code_2' => 'BJ',
        'file_path' => 'f.svg', 'sort_order' => 1, 'status' => 'active',
    ]);

    return User::create(array_merge([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'email_verified_at' => now(), 'phone_verified_at' => now(),
        'status' => 'active',
    ], $overrides));
}

it('expose les pages de connexion et de statut du compte', function () {
    $this->get(route('login'))->assertOk()->assertSee('authentication-form');

    $this->get(route('account.disabled'))->assertOk()->assertSee('Compte désactivé');
    $this->get(route('email.verifie.use-otp'))->assertOk();
    $this->get(route('phone.verifie.use-otp'))->assertOk();
});

it('connecte un utilisateur valide et redirige vers le frontend', function () {
    authTestUser();

    $response = $this->post(route('login.store'), [
        'email' => 'admin@site.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('frontend.index'));
    $this->assertAuthenticated();
});

it('refuse les identifiants invalides avec un message flash', function () {
    authTestUser();

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'admin@site.com',
            'password' => 'mauvais-mot-de-passe',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('error');

    $this->assertGuest();
});

it('valide les champs du formulaire de connexion', function () {
    $this->from(route('login'))
        ->post(route('login.store'), ['email' => '', 'password' => ''])
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

it('redirige un compte suspendu ou banni vers la page « compte désactivé »', function () {
    authTestUser(['status' => 'suspended']);

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'admin@site.com',
            'password' => 'password',
        ])
        ->assertRedirect(route('account.disabled'))
        ->assertSessionHas('error');

    $this->assertGuest();
});

it('empêche un utilisateur déjà connecté d’accéder au login (middleware guest)', function () {
    $user = authTestUser();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('frontend.index'));
});

it('redirige un utilisateur non connecté vers le login sur le backend (middleware auth)', function () {
    $this->get('/backend')
        ->assertRedirect(route('login'));
});

it('déconnecte proprement un utilisateur authentifié', function () {
    $user = authTestUser();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('enregistre les alias middleware V1 dans bootstrap/app.php', function () {
    // La synchronisation alias → routeur se fait à la construction du kernel HTTP.
    $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

    $aliases = Route::getMiddleware();

    $expected = [
        'v1.web.auth', 'v1.web.guest',
        'v1.web.check.active.user.account',
        'v1.web.check.user.email', 'v1.web.check.user.phone',
        'v1.web.check.active.role', 'v1.web.check.cloud.files.sync',
        'role', 'permission', 'role_or_permission',
    ];

    foreach ($expected as $alias) {
        $this->assertArrayHasKey($alias, $aliases, "Alias middleware « {$alias} » non enregistré");
    }
});

it('applique la locale de session via SetLocale', function () {
    $this->withSession(['locale' => 'fr'])
        ->get(route('login'))
        ->assertOk();

    expect(app()->getLocale())->toBe('fr');
});