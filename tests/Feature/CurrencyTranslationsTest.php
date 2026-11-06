<?php

declare(strict_types=1);

use App\Models\Currency;
use App\Models\CurrencyTranslation;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\User;
use App\Services\V1\Controllers\CurrencyTranslationService;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Devises — Traductions des devises (`currency_translations`).
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function currencyTranslationAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-currency-translations@site.com', 'gender' => 'female',
        'phone' => '+22903333333', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

/** Devise neuve : code alpha-3 unique et sort_order unique (contraintes DB). */
function newCurrency(): Currency
{
    static $counter = 0;
    $counter++;

    $code = chr(65 + intdiv($counter, 676) % 26)
        .chr(65 + intdiv($counter, 26) % 26)
        .chr(65 + $counter % 26);

    return Currency::create([
        'code' => $code,
        'symbol' => 'X',
        'decimal_places' => 2,
        'numeric_code' => null,
        'sort_order' => 200000 + $counter,
        'is_default' => false,
        'exchange_rate' => 1.5,
        'status' => 'active',
    ]);
}

/** Devise avec deux traductions (fr + en) — condition pour pouvoir en supprimer une. */
function currencyWithTwoTranslations(): Currency
{
    $currency = newCurrency();

    foreach (['fr', 'en'] as $code) {
        $lang = Lang::where('code', $code)->firstOrFail();
        CurrencyTranslation::create([
            'currency_id' => $currency->id,
            'lang_id' => $lang->id,
            'name' => 'Devise '.$code.' '.uniqid(),
        ]);
    }

    return $currency;
}

it('refuse la suppression de la dernière traduction', function () {
    $admin = currencyTranslationAdmin();

    $currency = newCurrency();
    $fr = Lang::where('code', 'fr')->firstOrFail();
    $translation = CurrencyTranslation::create([
        'currency_id' => $currency->id,
        'lang_id' => $fr->id,
        'name' => 'Unique '.uniqid(),
    ]);

    $this->actingAs($admin)
        ->from(route('backend.currencies.index'))
        ->delete(route('backend.currencies.translation.destroy', ['currencyTranslation' => $translation->uuid]))
        ->assertSessionHas('error');

    expect(CurrencyTranslation::find($translation->id))->not->toBeNull();
});

it('supprime (soft delete) une traduction quand il en reste au moins une', function () {
    $admin = currencyTranslationAdmin();
    $currency = currencyWithTwoTranslations();
    $translation = $currency->translations()->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.currencies.translation.destroy', ['currencyTranslation' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(CurrencyTranslation::find($translation->id))->toBeNull();
    expect(CurrencyTranslation::withTrashed()->find($translation->id))->not->toBeNull();
});

it('restaure une traduction en corbeille', function () {
    $admin = currencyTranslationAdmin();
    $currency = currencyWithTwoTranslations();
    $translation = $currency->translations()->firstOrFail();

    $this->actingAs($admin)->delete(route('backend.currencies.translation.destroy', ['currencyTranslation' => $translation->uuid]));

    $this->actingAs($admin)
        ->from(route('backend.currencies.index'))
        ->patch(route('backend.currencies.translation.restore', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(CurrencyTranslation::find($translation->id))->not->toBeNull();
});

it('supprime définitivement une traduction en corbeille (force_destroy)', function () {
    $admin = currencyTranslationAdmin();
    $currency = currencyWithTwoTranslations();
    $translation = $currency->translations()->firstOrFail();

    $this->actingAs($admin)->delete(route('backend.currencies.translation.destroy', ['currencyTranslation' => $translation->uuid]));

    $this->actingAs($admin)
        ->delete(route('backend.currencies.translation.force_destroy', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(CurrencyTranslation::withTrashed()->find($translation->id))->toBeNull();
});

it('lève une exception quand l\'entité parente est introuvable (garde du service)', function () {
    $orphan = new CurrencyTranslation;
    $orphan->forceFill([
        'currency_id' => 999999,
        'lang_id' => 1,
        'name' => 'Orpheline',
    ]);

    $service = app(CurrencyTranslationService::class);

    expect(fn () => $service->backend_currencies_translations_destroy($orphan))
        ->toThrow(InvalidArgumentException::class);
});
