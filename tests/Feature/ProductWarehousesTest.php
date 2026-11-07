<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
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
 * Module Produits — Entrepôts (`product_warehouses`) et leurs traductions
 * (`product_warehouse_translations`).
 *
 * NB : « entrepôt par défaut » n'est plus une colonne globale : c'est une notion
 * PAR BOUTIQUE, portée par le pivot `shop_warehouse.is_primary` (ShopService).
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

function warehouseAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-warehouses@site.com', 'gender' => 'female',
        'phone' => '+22904444444', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

/**
 * Payload de création/mise à jour d'un entrepôt avec ses traductions.
 * Le nom de traduction est unique par langue (contrainte DB), d'où le suffixe.
 */
function warehousePayload(array $overrides = []): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();
    $en = Lang::where('code', 'en')->firstOrFail();
    $suffix = strtoupper(uniqid());

    $defaults = [
        'code' => 'WH-'.$suffix,
        'status' => 'inactive',
        'translations' => [
            [
                'lang_id' => $fr->id,
                'name' => 'Entrepôt de test '.$suffix,
                'address' => 'Zone industrielle',
                'city' => 'Cotonou',
                'phone' => '+229 21 00 00 99',
            ],
            [
                'lang_id' => $en->id,
                'name' => 'Test warehouse '.$suffix,
                'address' => 'Industrial zone',
                'city' => 'Cotonou',
                'phone' => '+229 21 00 00 99',
            ],
        ],
    ];

    return array_merge($defaults, $overrides);
}

it('liste les entrepôts avec DataTables', function () {
    $admin = warehouseAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouses-table');
});

it('affiche la page de création d\'entrepôt', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouses.create'))
        ->assertOk();
});

it('crée un entrepôt avec ses traductions fr/en', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-warehouses.store'), warehousePayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    $warehouse = ProductWarehouse::firstOrFail();

    expect(ProductWarehouse::count())->toBe(1);
    expect($warehouse->status->value)->toBe('inactive');
    expect($warehouse->translations()->count())->toBe(2);
    expect(ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)->count())->toBe(2);
});

it('refuse un code dupliqué', function () {
    $admin = warehouseAdmin();

    $payload = warehousePayload(['status' => 'active']);
    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), $payload);

    $this->actingAs($admin)
        ->post(route('backend.product-warehouses.store'), warehousePayload(['code' => $payload['code']]))
        ->assertRedirect()
        ->assertSessionHasErrors(['code']);

    expect(ProductWarehouse::count())->toBe(1);
});

it('affiche le détail d\'un entrepôt (nom traduit)', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]))
        ->assertOk()
        ->getContent();

    expect($html)->toContain($warehouse->code);
});

it('met à jour un entrepôt et ses traductions', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $fr = Lang::where('code', 'fr')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('backend.product-warehouses.update', ['productWarehouse' => $warehouse->uuid]), [
            'code' => $warehouse->code,
            'translations' => [
                [
                    'lang_id' => $fr->id,
                    'name' => 'Entrepôt renommé '.strtoupper(uniqid()),
                    'address' => 'Nouvelle adresse',
                    'city' => 'Porto-Novo',
                    'phone' => '+229 21 00 00 77',
                ],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $translation = ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)
        ->where('lang_id', $fr->id)
        ->firstOrFail();

    expect($translation->city)->toBe('Porto-Novo');
    expect($translation->address)->toBe('Nouvelle adresse');
});

it('active et désactive un entrepôt', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.product-warehouses.activate', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect($warehouse->fresh()->status->value)->toBe('active');

    $this->actingAs($admin)
        ->patch(route('backend.product-warehouses.deactivate', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect($warehouse->fresh()->status->value)->toBe('inactive');
});

it('refuse la suppression d\'un entrepôt actif puis autorise après désactivation', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload(['status' => 'active']));
    $warehouse = ProductWarehouse::firstOrFail();

    expect($warehouse->status->value)->toBe('active');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.index'))
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect(route('backend.product-warehouses.index'))
        ->assertSessionHas('error');

    expect(ProductWarehouse::find($warehouse->id))->not->toBeNull();

    $warehouse->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect(ProductWarehouse::find($warehouse->id))->toBeNull();
});

it('passe un entrepôt en corbeille puis le restaure', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect();

    expect(ProductWarehouse::find($warehouse->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.trashed'))
        ->patch(route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]))
        ->assertRedirect();

    expect(ProductWarehouse::find($warehouse->id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Traductions (product_warehouse_translations)
// ─────────────────────────────────────────────

it('refuse de supprimer la dernière traduction d\'un entrepôt', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $translations = ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)->get();

    // 1re suppression autorisée (2 traductions restantes → 1)
    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-translations.destroy', ['uuid' => $translations[0]->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    // 2e suppression refusée : c'est la dernière traduction
    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]))
        ->delete(route('backend.product-warehouse-translations.destroy', ['uuid' => $translations[1]->uuid]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)->count())->toBe(1);
});

it('restaure une traduction en corbeille', function () {
    $admin = warehouseAdmin();

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload());
    $warehouse = ProductWarehouse::firstOrFail();

    $translation = ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-translations.destroy', ['uuid' => $translation->uuid]))
        ->assertRedirect();

    expect(ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)->count())->toBe(1);

    $this->actingAs($admin)
        ->get(route('backend.product-warehouse-translations.trashed', ['warehouse_uuid' => $warehouse->uuid]))
        ->assertOk();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-translations.trashed', ['warehouse_uuid' => $warehouse->uuid]))
        ->patch(route('backend.product-warehouse-translations.restore', ['uuid' => $translation->uuid]))
        ->assertRedirect();

    expect(ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)->count())->toBe(2);
});

it('déclare dependentRelations = [stocks, movements, warehouseUsers] pour ProductWarehouse', function () {
    $warehouse = new ProductWarehouse;

    $property = new ReflectionProperty($warehouse, 'dependentRelations');
    expect($property->getValue($warehouse))->toBe(['stocks', 'movements', 'warehouseUsers']);
});

it('active les traductions et la bonne clé étrangère pour ProductWarehouse', function () {
    $warehouse = new ProductWarehouse;

    expect($warehouse->getTranslationModelName())->toBe(ProductWarehouseTranslation::class);
    expect($warehouse->getTranslationForeignKey())->toBe('warehouse_id');
});

it('expose la relation shops (is_default par boutique via le pivot shop_warehouse)', function () {
    $warehouse = new ProductWarehouse;

    $relation = $warehouse->shops();

    expect($relation->getTable())->toBe('shop_warehouse');
    expect($relation->getPivotColumns())->toContain('is_primary');
    // Plus aucune colonne is_default globale sur les entrepôts
    expect(\Illuminate\Support\Facades\Schema::hasColumn('product_warehouses', 'is_default'))->toBeFalse();
});
