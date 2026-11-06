<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\Shop;
use App\Models\ShopTranslation;
use App\Models\User;
use App\Services\V1\Controllers\ShopTranslationService;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Boutique — Traductions des boutiques (`shop_translations`).
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

function shopTranslationAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-shop-translations@site.com', 'gender' => 'female',
        'phone' => '+22902222222', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

/** Boutique avec deux traductions (fr + en) — condition pour pouvoir en supprimer une. */
function shopWithTwoTranslations(): Shop
{
    $shop = Shop::create([
        'code' => 'SHOP-'.strtoupper(uniqid()),
        'status' => 'active',
        'is_default' => true,
        'sort_order' => random_int(1, 9999),
    ]);

    foreach (['fr', 'en'] as $code) {
        $lang = Lang::where('code', $code)->firstOrFail();
        ShopTranslation::create([
            'shop_id' => $shop->id,
            'lang_id' => $lang->id,
            'name' => 'Boutique '.$code.' '.uniqid(),
        ]);
    }

    return $shop;
}

it('refuse la suppression de la dernière traduction', function () {
    $admin = shopTranslationAdmin();

    $shop = Shop::create([
        'code' => 'SHOP-'.strtoupper(uniqid()),
        'status' => 'active',
        'is_default' => true,
        'sort_order' => random_int(1, 9999),
    ]);
    $fr = Lang::where('code', 'fr')->firstOrFail();
    $translation = ShopTranslation::create([
        'shop_id' => $shop->id,
        'lang_id' => $fr->id,
        'name' => 'Unique '.uniqid(),
    ]);

    $this->actingAs($admin)
        ->from(route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => 'null']))
        ->delete(route('backend.shop-translations.destroy', ['uuid' => $translation->uuid]))
        ->assertSessionHas('error');

    expect(ShopTranslation::find($translation->id))->not->toBeNull();
});

it('supprime (soft delete) une traduction quand il en reste au moins une', function () {
    $admin = shopTranslationAdmin();
    $shop = shopWithTwoTranslations();
    $translation = $shop->translations()->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.shop-translations.destroy', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ShopTranslation::find($translation->id))->toBeNull();
    expect(ShopTranslation::withTrashed()->find($translation->id))->not->toBeNull();
});

it('restaure une traduction en corbeille', function () {
    $admin = shopTranslationAdmin();
    $shop = shopWithTwoTranslations();
    $translation = $shop->translations()->firstOrFail();

    $this->actingAs($admin)->delete(route('backend.shop-translations.destroy', ['uuid' => $translation->uuid]));

    $this->actingAs($admin)
        ->from(route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => 'null']))
        ->patch(route('backend.shop-translations.restore', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ShopTranslation::find($translation->id))->not->toBeNull();
});

it('supprime définitivement une traduction en corbeille (force_destroy)', function () {
    $admin = shopTranslationAdmin();
    $shop = shopWithTwoTranslations();
    $translation = $shop->translations()->firstOrFail();

    $this->actingAs($admin)->delete(route('backend.shop-translations.destroy', ['uuid' => $translation->uuid]));

    $this->actingAs($admin)
        ->delete(route('backend.shop-translations.force_destroy', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ShopTranslation::withTrashed()->find($translation->id))->toBeNull();
});

it('refuse le force_destroy d\'une traduction non supprimée', function () {
    $admin = shopTranslationAdmin();
    $shop = shopWithTwoTranslations();
    $translation = $shop->translations()->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.shop-translations.force_destroy', ['uuid' => $translation->uuid]))
        ->assertSessionHas('error');

    expect(ShopTranslation::find($translation->id))->not->toBeNull();
});

it('lève une exception quand l\'entité parente est introuvable (garde du service)', function () {
    // Traduction non persistée dont le shop_id ne correspond à aucune boutique :
    // le service doit refuser l'opération avec une InvalidArgumentException
    // au lieu de poursuivre la suppression.
    $orphan = new ShopTranslation;
    $orphan->forceFill([
        'shop_id' => 999999,
        'lang_id' => 1,
        'name' => 'Orpheline',
    ]);

    $service = app(ShopTranslationService::class);

    expect(fn () => $service->backend_shop_translations_destroy($orphan))
        ->toThrow(InvalidArgumentException::class);
});
