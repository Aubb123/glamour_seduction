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
use Database\Seeders\SettingSeeder;
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
    $this->seed(SettingSeeder::class);
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

    // XOF est désormais la devise par défaut (base de saisie des prix)
    expect($default->code)->toBe('XOF');
    expect((float) $default->exchange_rate)->toBe(1.0);
});

it('convertit les prix EUR ↔ USD ↔ XOF selon les taux seedés', function () {
    $service = app(\App\Services\V1\PricingService::class);
    $eur = pricingCurrency('EUR');
    $usd = pricingCurrency('USD');
    $xof = pricingCurrency('XOF');

    // XOF = 1 (devise par défaut), EUR = 655.957, USD = 606.835
    expect((float) $xof->exchange_rate)->toBe(1.0);
    expect((float) $eur->exchange_rate)->toBe(655.957);
    expect((float) $usd->exchange_rate)->toBe(606.835);

    // EUR (source) → XOF : 100 × 1 / 655.957
    expect($service->convertPrice(100, $eur, $xof))->toEqualWithDelta(0.1524, 0.001);

    // XOF (source) → EUR : 100 × 655.957 / 1
    expect($service->convertPrice(100, $xof, $eur))->toEqualWithDelta(65595.7, 0.1);

    // USD (source) → XOF : 100 × 1 / 606.835
    expect($service->convertPrice(100, $usd, $xof))->toEqualWithDelta(0.1648, 0.001);

    // Identité : devise par défaut → elle-même
    expect($service->convertPrice(50, $xof, $xof))->toEqualWithDelta(50.0, 0.000001);
});

it('formate les prix selon decimal_places (0/2/3 décimales) avec le symbole', function () {
    $service = app(\App\Services\V1\PricingService::class);
    $eur = pricingCurrency('EUR');
    $usd = pricingCurrency('USD');
    $xof = pricingCurrency('XOF');

    // XOF : 0 décimale → arrondi entier (100 EUR → XOF)
    expect($service->formatPrice($service->convertPrice(100, $eur, $xof), $xof))->toBe('66 Fr');

    // USD : 2 décimales (100 EUR → USD)
    expect($service->formatPrice($service->convertPrice(100, $eur, $usd), $usd))->toBe('0,92 $');

    // EUR : 2 décimales
    expect($service->formatPrice(19990.0, $eur))->toBe('19 990,00 €');

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

    expect($service->formatPrice($service->convertPrice(100, $eur, $test), $test))->toBe('0,033 T');
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

it('retourne le montant original si un taux de change est nul ou négatif', function () {
    $service = app(\App\Services\V1\PricingService::class);
    $eur = pricingCurrency('EUR');
    $usd = pricingCurrency('USD');

    Currency::where('code', 'USD')->update(['exchange_rate' => 0]);
    $usd->refresh();

    // Avec un taux à 0, la conversion retourne le montant original (pas d'exception)
    expect($service->convertPrice(100, $eur, $usd))->toEqualWithDelta(100.0, 0.0001);
    expect($service->convertPrice(100, $usd, $eur))->toEqualWithDelta(100.0, 0.0001);
});

it('permet de changer de devise depuis le frontend (session)', function () {
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

    // Utilisateur connecté : la préférence va aussi en session (plus de persistance en base)
    $admin = pricingUser();

    $this->actingAs($admin)
        ->post(route('frontend.currencies.switch'), ['currency' => 'USD'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(session('currency'))->toBe('USD');
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

it('ne garde que XOF, USD et EUR actives après le seed (les autres sont désactivées)', function () {
    $active = Currency::active()->pluck('code')->all();

    sort($active);
    expect($active)->toBe(['EUR', 'USD', 'XOF']);
});

it('permet de changer de devise depuis le dashboard (backend.currency.switch)', function () {
    $admin = pricingUser();
    $admin->assignRole('administrator');

    $usd = pricingCurrency('USD');

    $this->actingAs($admin)
        ->get(route('backend.currency.switch', ['currency' => $usd->uuid]))
        ->assertRedirect(route('backend.index'))
        ->assertSessionHas('success');

    // La préférence est en session (plus de persistance en base)
    expect(session('currency'))->toBe('USD');
});
