<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\Shop;
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
 * Module Boutique — Boutiques (`shops` + `shop_translations` + pivot `shop_warehouse`).
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

function shopAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-shops@site.com', 'gender' => 'female',
        'phone' => '+22905555555', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function shopWarehouse(array $overrides = []): ProductWarehouse
{
    // NB : la table product_warehouses ne porte plus que `code` + `status`
    // (name/address/city/phone vivent dans product_warehouse_translations ;
    // is_default est remplacé par le pivot shop_warehouse.is_primary).
    return ProductWarehouse::create(array_merge([
        'code' => 'WH-'.strtoupper(uniqid()),
        'status' => 'active',
    ], $overrides));
}

function shopPayload(array $overrides = []): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();
    $warehouse = shopWarehouse();

    return array_merge([
        'code' => 'SHOP-'.strtoupper(uniqid()),
        'email' => 'shop@site.com',
        'phone' => '+229 21 00 00 00',
        'address' => 'Adresse de test',
        'city' => 'Cotonou',
        'status' => 'inactive',
        'sort_order' => random_int(1, 9999),
        'warehouses' => [$warehouse->uuid],
        'primary_warehouse_uuid' => $warehouse->uuid,
        'translations' => [
            ['lang_id' => $fr->id, 'name' => 'Boutique '.uniqid(), 'description' => 'Description de test'],
        ],
    ], $overrides);
}

it('liste les boutiques avec DataTables', function () {
    $admin = shopAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.shops.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('shops-table');
});

it('crée une boutique avec sa traduction', function () {
    $admin = shopAdmin();

    $this->actingAs($admin)
        ->post(route('backend.shops.store'), shopPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Shop::count())->toBe(1);
    expect(Shop::firstOrFail()->translations()->count())->toBe(1);
});

it('refuse un code de boutique dupliqué', function () {
    $admin = shopAdmin();

    $payload = shopPayload();
    $this->actingAs($admin)->post(route('backend.shops.store'), $payload);

    $this->actingAs($admin)
        ->post(route('backend.shops.store'), shopPayload(['code' => $payload['code']]))
        ->assertSessionHasErrors(['code']);

    expect(Shop::count())->toBe(1);
});

it('active et désactive une boutique', function () {
    $admin = shopAdmin();

    $this->actingAs($admin)->post(route('backend.shops.store'), shopPayload());
    $shop = Shop::firstOrFail();

    $this->actingAs($admin)->patch(route('backend.shops.activate', ['shop' => $shop->uuid]));
    expect($shop->fresh()->status->value)->toBe('active');

    $this->actingAs($admin)->patch(route('backend.shops.deactivate', ['shop' => $shop->uuid]));
    expect($shop->fresh()->status->value)->toBe('inactive');
});

it('refuse la suppression d\'une boutique active puis autorise après désactivation', function () {
    $admin = shopAdmin();

    $this->actingAs($admin)->post(route('backend.shops.store'), shopPayload(['status' => 'active']));
    $shop = Shop::firstOrFail();

    $this->actingAs($admin)
        ->from(route('backend.shops.index'))
        ->delete(route('backend.shops.destroy', ['shop' => $shop->uuid]))
        ->assertRedirect(route('backend.shops.index'))
        ->assertSessionHas('error');

    expect(Shop::find($shop->id))->not->toBeNull();

    // Désactivée → suppression autorisée
    $shop->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.shops.destroy', ['shop' => $shop->uuid]))
        ->assertRedirect();

    expect(Shop::find($shop->id))->toBeNull();
});

it('passe une boutique en corbeille puis la restaure', function () {
    $admin = shopAdmin();

    $this->actingAs($admin)->post(route('backend.shops.store'), shopPayload());
    $shop = Shop::firstOrFail();
    $shop->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.shops.destroy', ['shop' => $shop->uuid]))
        ->assertRedirect();

    expect(Shop::find($shop->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.shops.trashed'))
        ->patch(route('backend.shops.restore', ['uuid' => $shop->uuid]))
        ->assertRedirect();

    expect(Shop::find($shop->id))->not->toBeNull();
});

it('rattache les entrepôts via le pivot shop_warehouse (au moins un, un seul principal)', function () {
    $admin = shopAdmin();
    $fr = Lang::where('code', 'fr')->firstOrFail();

    $w1 = shopWarehouse();
    $w2 = shopWarehouse();

    $this->actingAs($admin)->post(route('backend.shops.store'), array_merge(shopPayload(), [
        'warehouses' => [$w1->uuid, $w2->uuid],
        'primary_warehouse_uuid' => $w2->uuid,
        'translations' => [
            ['lang_id' => $fr->id, 'name' => 'Boutique pivot '.uniqid(), 'description' => 'Desc.'],
        ],
    ]))->assertSessionHas('success');

    $shop = Shop::firstOrFail();

    expect($shop->warehouses()->count())->toBe(2);
    expect($shop->primaryWarehouse()->first()?->id)->toBe($w2->id);
});

it('affiche la fiche boutique (show) sans erreur serveur', function () {
    $admin = shopAdmin();

    $this->actingAs($admin)->post(route('backend.shops.store'), shopPayload());
    $shop = Shop::firstOrFail();

    // La relation shopUsers (pivot) est chargée par le show : c'est ce chargement
    // qui levait un 500 avant la conversion du personnel en pivot.
    $this->actingAs($admin)
        ->get(route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']))
        ->assertOk();
});

it('déclare shopUsers comme relation bloquante pour Shop (dependentRelations)', function () {
    $property = new ReflectionProperty(new Shop, 'dependentRelations');
    expect($property->getValue(new Shop))->toBe(['shopUsers']);
});
