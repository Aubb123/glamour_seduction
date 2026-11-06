<?php

declare(strict_types=1);

use App\Models\Currency;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Devises (`currencies` + `currency_translations`).
 *
 * NB : le module Devises n'expose pas de route create/store (les devises sont
 * amorcées puis administrées via edit/update). Les tests créent donc les devises
 * directement via le modèle, puis exercent les routes backend.
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

function currencyAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-currencies@site.com', 'gender' => 'female',
        'phone' => '+22901111111', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function makeCurrency(array $overrides = []): Currency
{
    return Currency::create(array_merge([
        'code' => strtoupper(substr(uniqid(), -3)),
        'symbol' => 'X',
        'decimal_places' => 2,
        'numeric_code' => null,
        'sort_order' => random_int(1, 999999),
        'is_default' => false,
        'exchange_rate' => 1.5,
        'status' => 'active',
    ], $overrides));
}

function currencyPayload(Currency $currency, array $overrides = []): array
{
    return array_merge([
        'code' => $currency->code,
        'symbol' => $currency->symbol,
        'decimal_places' => $currency->decimal_places,
        'numeric_code' => $currency->numeric_code,
        'sort_order' => $currency->sort_order,
        'exchange_rate' => 1.5,
        'is_default' => false,
        'status' => 'active',
        'translations' => [
            ['lang_id' => Lang::where('code', 'fr')->value('id'), 'name' => 'Devise '.uniqid(), 'description' => 'Desc.'],
        ],
    ], $overrides);
}

it('liste les devises avec DataTables', function () {
    $admin = currencyAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.currencies.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('currencies-table');
});

it('met à jour une devise et synchronise ses traductions', function () {
    $admin = currencyAdmin();
    $currency = makeCurrency();
    $fr = Lang::where('code', 'fr')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('backend.currencies.update', ['currency' => $currency->uuid]), currencyPayload($currency, [
            'translations' => [
                ['lang_id' => $fr->id, 'name' => 'Devise FR '.uniqid(), 'description' => 'Desc.'],
            ],
        ]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($currency->fresh()->translations()->count())->toBe(1);
});

it('refuse un code de devise dupliqué (unicité)', function () {
    $admin = currencyAdmin();
    $first = makeCurrency();
    $second = makeCurrency();

    $this->actingAs($admin)
        ->put(route('backend.currencies.update', ['currency' => $second->uuid]), currencyPayload($second, [
            'code' => $first->code,
        ]))
        ->assertSessionHasErrors(['code']);
});

it('gère une seule devise par défaut (is_default unique)', function () {
    $admin = currencyAdmin();

    $existingDefault = makeCurrency(['is_default' => true, 'exchange_rate' => 1.0]);
    $currency = makeCurrency(['is_default' => false]);

    $this->actingAs($admin)
        ->put(route('backend.currencies.update', ['currency' => $currency->uuid]), currencyPayload($currency, [
            'is_default' => true,
            'exchange_rate' => 1.0,
        ]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Currency::where('is_default', true)->count())->toBe(1);
    expect($currency->fresh()->is_default)->toBeTrue();
    expect($existingDefault->fresh()->is_default)->toBeFalse();
});

it('refuse la suppression de la devise par défaut puis autorise après bascule', function () {
    $admin = currencyAdmin();

    $default = makeCurrency(['is_default' => true, 'exchange_rate' => 1.0]);

    $this->actingAs($admin)
        ->from(route('backend.currencies.index'))
        ->delete(route('backend.currencies.destroy', ['currency' => $default->uuid]))
        ->assertRedirect(route('backend.currencies.index'))
        ->assertSessionHas('error');

    expect(Currency::find($default->id))->not->toBeNull();

    // Basculer le drapeau par défaut puis supprimer
    $default->update(['is_default' => false]);
    $other = makeCurrency(['is_default' => true, 'exchange_rate' => 1.0]);

    $this->actingAs($admin)
        ->delete(route('backend.currencies.destroy', ['currency' => $default->uuid]))
        ->assertRedirect();

    expect(Currency::find($default->id))->toBeNull();
});

it('passe une devise en corbeille puis la restaure', function () {
    $admin = currencyAdmin();
    $currency = makeCurrency();

    $this->actingAs($admin)
        ->delete(route('backend.currencies.destroy', ['currency' => $currency->uuid]))
        ->assertRedirect();

    expect(Currency::find($currency->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.currencies.trashed'))
        ->patch(route('backend.currencies.restore', ['uuid' => $currency->uuid]))
        ->assertRedirect();

    expect(Currency::find($currency->id))->not->toBeNull();
});

it('expose la relation countries (devise utilisée par des pays)', function () {
    $currency = makeCurrency();
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $country->update(['currency_id' => $currency->id]);

    expect($currency->countries()->pluck('id')->all())->toContain($country->id);
});

it('déclare countries comme relation bloquante pour Currency (dependentRelations)', function () {
    $property = new ReflectionProperty(new Currency, 'dependentRelations');
    expect($property->getValue(new Currency))->toBe(['countries']);
});
