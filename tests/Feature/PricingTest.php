<?php

declare(strict_types=1);

use App\Models\Currency;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\User;
use App\Services\V1\Controllers\CurrencyService;
use App\Services\V1\PricingService;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module 10 — Prix multi-devise (Option A « conversion »).
 * PricingService : resolveDisplayCurrency / convertPrice / formatPrice.
 */
beforeEach(function () {
    // Ordre important : CurrencySeeder AVANT GeoCountrySeeder
    // (le pays associe currency_id via Currency::where('code', …))
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(CurrencySeeder::class);
    $this->seed(GeoCountrySeeder::class);
    bootV1WebRoutes();
});

function pricingCurrency(string $code): Currency
{
    return Currency::where('code', $code)->firstOrFail();
}

function pricingUser(?Currency $currency = null): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $user = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'pricing-'.uniqid().'@site.com', 'gender' => 'female',
        'phone' => '+2290'.random_int(1000000, 9999999), 'password' => 'password',
        'status' => 'active',
    ]);

    $user->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();

    if ($currency) {
        $user->currency()->associate($currency);
        $user->save();
    }

    return $user;
}

it('ajoute la colonne exchange_rate à currencies et backfill la devise par défaut à 1', function () {
    expect(Schema::hasColumn('currencies', 'exchange_rate'))->toBeTrue();

    $default = Currency::where('is_default', true)->firstOrFail();

    expect($default->code)->toBe('EUR');
    expect((float) $default->exchange_rate)->toBe(1.0);
});

it('convertit les prix EUR ↔ USD ↔ XOF selon les taux seedés', function () {
    $eur = pricingCurrency('EUR');
    $usd = pricingCurrency('USD');
    $xof = pricingCurrency('XOF');

    expect((float) $eur->exchange_rate)->toBe(1.0);
    expect((float) $usd->exchange_rate)->toBe(1.08);
    expect((float) $xof->exchange_rate)->toBe(655.957);

    // EUR (source) → XOF : 19990 × 655.957
    expect(convertPrice(19990, $eur, $xof))->toEqualWithDelta(13112580.43, 0.01);

    // USD (source) → EUR : 100 × 1 / 1.08
    expect(convertPrice(100, $usd, $eur))->toEqualWithDelta(92.5926, 0.0001);

    // XOF (source) → USD : 100 × 1.08 / 655.957
    expect(convertPrice(100, $xof, $usd))->toEqualWithDelta(0.1646, 0.0001);

    // Identité : devise par défaut → elle-même
    expect(convertPrice(50, $eur, $eur))->toEqualWithDelta(50.0, 0.000001);
});

it('formate les prix selon decimal_places (0/2/3 décimales) avec le symbole', function () {
    $eur = pricingCurrency('EUR');
    $usd = pricingCurrency('USD');
    $xof = pricingCurrency('XOF');

    // XOF : 0 décimale → arrondi entier
    expect(formatPrice(convertPrice(19990, $eur, $xof), $xof))->toBe('13 112 580 Fr');

    // USD : 2 décimales
    expect(formatPrice(convertPrice(19990, $eur, $usd), $usd))->toBe('21 589,20 $');

    // EUR : 2 décimales
    expect(formatPrice(19990.0, $eur))->toBe('19 990,00 €');

    // Devise de test : 3 décimales (style KWD)
    $test = Currency::create([
        'code' => 'TST',
        'symbol' => 'T',
        'numeric_code' => '999',
        'decimal_places' => 3,
        'sort_order' => 999,
        'exchange_rate' => 0.331,
        'status' => 'active',
    ]);

    expect(formatPrice(convertPrice(19990, $eur, $test), $test))->toBe('6 616,690 T');
});

it('résout la devise d\'affichage selon la cascade user → session → pays → défaut', function () {
    $pricing = app(PricingService::class);

    $eur = pricingCurrency('EUR');
    $usd = pricingCurrency('USD');
    $xof = pricingCurrency('XOF');

    // 4. Aucun utilisateur ni session → devise par défaut (EUR)
    expect($pricing->resolveDisplayCurrency(null)->code)->toBe('EUR');

    // 3. Utilisateur avec pays (BEN → XOF) mais sans préférence personnelle → devise du pays
    $byCountry = pricingUser();
    expect($pricing->resolveDisplayCurrency($byCountry)->code)->toBe('XOF');

    // 2. Session (devise du visiteur) prioritaire sur la devise du pays
    $withSession = pricingUser();
    session(['currency' => 'USD']);
    expect($pricing->resolveDisplayCurrency($withSession)->code)->toBe('USD');

    // 1. Devise personnelle de l'utilisateur prioritaire sur la session
    $personal = pricingUser($usd);
    session(['currency' => 'XOF']);
    expect($pricing->resolveDisplayCurrency($personal)->code)->toBe('USD');

    // Fallback : session invalide → retombe sur la devise du pays
    session(['currency' => 'ZZZ']);
    expect($pricing->resolveDisplayCurrency($byCountry)->code)->toBe('XOF');

    // Les devises créées restent cohérentes
    expect($eur->code)->toBe('EUR');
    expect($xof->code)->toBe('XOF');
});

it('refuse la conversion si un taux de change est nul ou négatif', function () {
    $eur = pricingCurrency('EUR');
    $usd = pricingCurrency('USD');

    Currency::where('code', 'USD')->update(['exchange_rate' => 0]);
    $usd->refresh();

    expect(fn () => convertPrice(100, $eur, $usd))->toThrow(InvalidArgumentException::class);
    expect(fn () => convertPrice(100, $usd, $eur))->toThrow(InvalidArgumentException::class);
});

it('permet de changer de devise depuis le frontend (session visiteur, users.currency_id connecté)', function () {
    $usd = pricingCurrency('USD');
    $xof = pricingCurrency('XOF');

    // Visiteur : la préférence va en session
    $this->post(route('frontend.currencies.switch'), ['currency' => 'XOF'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(session('currency'))->toBe('XOF');

    // Devise inexistante → refusée
    $this->post(route('frontend.currencies.switch'), ['currency' => 'ZZZ'])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(session('currency'))->toBe('XOF');

    // Utilisateur connecté : la préférence est persistée (users.currency_id)
    $admin = pricingUser();

    $this->actingAs($admin)
        ->post(route('frontend.currencies.switch'), ['currency' => 'USD'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($admin->fresh()->currency_id)->toBe($usd->id);
    expect($admin->fresh()->currency->code)->toBe('USD');

    // La page d'accueil affiche le prix converti (placeholder)
    $this->actingAs($admin)
        ->get(route('frontend.index'))
        ->assertOk();
});

it('empêche de définir un taux différent de 1 sur la devise par défaut (garde métier)', function () {
    $eur = pricingCurrency('EUR');
    $service = app(CurrencyService::class);

    $payload = [
        'code' => 'EUR',
        'symbol' => '€',
        'decimal_places' => 2,
        'numeric_code' => '978',
        'sort_order' => $eur->sort_order,
        'exchange_rate' => 655.957, // ← taux invalide pour la devise par défaut
        'is_default' => '1',
        'status' => 'active',
        'translations' => [
            ['lang_id' => Lang::where('code', 'fr')->firstOrFail()->id, 'name' => 'Euro'],
        ],
    ];

    expect(fn () => $service->backend_currencies_update($eur, $payload))->toThrow(InvalidArgumentException::class);
});
