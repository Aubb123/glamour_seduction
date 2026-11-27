<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\GeoDivision;
use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\GeoDivisionSeeder;
use Database\Seeders\GeoDivisionTypeSeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Boutique — Entrepôts de produits (`product-warehouses`) et leurs traductions.
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(SettingSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(GeoDivisionTypeSeeder::class);
    $this->seed(GeoDivisionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function warehousesAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'division_id' => $division->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function warehousesModerator(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();

    $moderator = User::create([
        'country_id' => $country->id,
        'division_id' => $division->id,
        'first_name' => 'Marc', 'last_name' => 'Dupont',
        'email' => 'moderator@site.com', 'gender' => 'male',
        'phone' => '+22900000001', 'password' => 'password',
        'status' => 'active',
    ]);

    $moderator->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $moderator->assignRole('moderator');

    return $moderator;
}

function warehousePayload(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();
    $division = GeoDivision::where('country_id', $country->id)->firstOrFail();
    $uid = strtoupper(uniqid());

    return array_merge([
        'code' => 'WH-'.substr($uid, -8),
        'sort_order' => random_int(1, 1000000000),
        'type' => 'storage',
        'stock_tracking_mode' => 'warehouse',
        'status' => 'inactive',
        'is_default' => false,
        'country_id' => $country->id,
        'division_id' => $division->id,
        'city' => 'Cotonou',
        'address' => 'Zone industrielle, Cotonou',
        'latitude' => '6.3653600',
        'longitude' => '2.4183300',
        'phone' => '+22997000009',
        'email' => 'entrepot'.$uid.'@example.test',
        'notes' => 'Entrepôt de test.',
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Entrepôt '.$uid, 'description' => 'Description FR.'],
            ['lang_id' => $langEn->id, 'name' => 'Warehouse '.$uid, 'description' => 'Description EN.'],
        ],
    ], $overrides);
}

function createWarehouse($test, User $admin, array $overrides = []): ProductWarehouse
{
    $test->actingAs($admin)->post(route('backend.product-warehouses.store'), warehousePayload($overrides));

    return ProductWarehouse::latest('id')->firstOrFail();
}

it('liste les entrepôts avec DataTables', function () {
    $admin = warehousesAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouses-table');
    expect($html)->toContain("$('#product-warehouses-table').DataTable");
});

it('affiche la page de création d’un entrepôt', function () {
    $admin = warehousesAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.create'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('create-warehouse-form');
    expect($html)->toContain('translations[fr][name]');
    expect($html)->toContain('translations[en][name]');
});

it('crée un entrepôt avec ses traductions', function () {
    $admin = warehousesAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-warehouses.store'), warehousePayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    $warehouse = ProductWarehouse::firstOrFail();
    expect($warehouse->translations()->count())->toBe(2);
    expect($warehouse->code)->toBeString();
    expect($warehouse->status->value)->toBe('inactive');
    expect($warehouse->is_default)->toBeFalse();
    expect($warehouse->active_translation?->slug)->not->toBeNull();
});

it('accepte le mode de suivi du stock « warehouse » et « location »', function () {
    $admin = warehousesAdmin();

    $warehouse = createWarehouse($this, $admin, ['stock_tracking_mode' => 'warehouse', 'code' => 'STM-WH']);
    expect($warehouse->fresh()->stock_tracking_mode->value)->toBe('warehouse');

    $location = createWarehouse($this, $admin, ['stock_tracking_mode' => 'location', 'code' => 'STM-LOC']);
    expect($location->fresh()->stock_tracking_mode->value)->toBe('location');
});

it('rejette un mode de suivi du stock invalide', function () {
    $admin = warehousesAdmin();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.create'))
        ->post(route('backend.product-warehouses.store'), warehousePayload(['stock_tracking_mode' => 'invalid']))
        ->assertSessionHasErrors(['stock_tracking_mode']);

    expect(ProductWarehouse::count())->toBe(0);
});

it('utilise « warehouse » par défaut pour le mode de suivi du stock', function () {
    $admin = warehousesAdmin();

    $warehouse = createWarehouse($this, $admin, ['code' => 'STM-DEF']);

    expect($warehouse->fresh()->stock_tracking_mode->value)->toBe('warehouse');
});

it('refuse un code en doublon ou invalide', function () {
    $admin = warehousesAdmin();
    $payload = warehousePayload(['code' => 'COT-TEST']);

    $this->actingAs($admin)->post(route('backend.product-warehouses.store'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    // Doublon
    $duplicate = warehousePayload([
        'code' => 'COT-TEST',
        'translations' => [
            ['lang_id' => Lang::where('code', 'fr')->value('id'), 'name' => 'Doublon FR '.uniqid(), 'description' => null],
            ['lang_id' => Lang::where('code', 'en')->value('id'), 'name' => 'Duplicate EN '.uniqid(), 'description' => null],
        ],
    ]);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.create'))
        ->post(route('backend.product-warehouses.store'), $duplicate)
        ->assertSessionHasErrors(['code']);

    // Format invalide
    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.create'))
        ->post(route('backend.product-warehouses.store'), warehousePayload(['code' => 'code invalide!']))
        ->assertSessionHasErrors(['code']);

    expect(ProductWarehouse::count())->toBe(1);
});

it('ne conserve qu’un seul entrepôt par défaut (bascule)', function () {
    $admin = warehousesAdmin();

    $first = createWarehouse($this, $admin, ['status' => 'active', 'is_default' => true, 'code' => 'DEF-A']);
    expect($first->fresh()->is_default)->toBeTrue();

    $second = createWarehouse($this, $admin, ['status' => 'active', 'is_default' => true, 'code' => 'DEF-B']);

    expect($second->fresh()->is_default)->toBeTrue();
    expect($first->fresh()->is_default)->toBeFalse();
    expect(ProductWarehouse::where('is_default', true)->count())->toBe(1);
});

it('refuse un entrepôt par défaut inactif ou non dédié au stockage', function () {
    $admin = warehousesAdmin();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.create'))
        ->post(route('backend.product-warehouses.store'), warehousePayload([
            'status' => 'inactive',
            'is_default' => true,
        ]))
        ->assertSessionHasErrors(['is_default']);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.create'))
        ->post(route('backend.product-warehouses.store'), warehousePayload([
            'type' => 'returns',
            'status' => 'active',
            'is_default' => true,
        ]))
        ->assertSessionHasErrors(['is_default']);

    expect(ProductWarehouse::count())->toBe(0);
});

it('ne permet pas de modifier le code après création', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin, ['code' => 'FIXE-01']);

    $payload = warehousePayload([
        'code' => 'NOUVEAU-02',
        'translations' => [
            ['lang_id' => Lang::where('code', 'fr')->value('id'), 'name' => 'Nom modifié '.uniqid(), 'description' => null],
            ['lang_id' => Lang::where('code', 'en')->value('id'), 'name' => 'Updated name '.uniqid(), 'description' => null],
        ],
    ]);

    $this->actingAs($admin)
        ->put(route('backend.product-warehouses.update', ['productWarehouse' => $warehouse->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($warehouse->fresh()->code)->toBe('FIXE-01');
});

it('retourne une 404 quand le slug ne correspond pas à l’entrepôt', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin);

    $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid, 'slug' => 'slug-incorrect']))
        ->assertNotFound();
});

it('affiche le détail d’un entrepôt (show avec slug)', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin);
    $slug = $warehouse->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('affiche la page d’édition d’un entrepôt avec le code en lecture seule', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin, ['code' => 'EDIT-01']);

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.edit', ['productWarehouse' => $warehouse->uuid]))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('edit-warehouse-form');
    expect($html)->toContain('value="EDIT-01"');
    expect($html)->toContain('readonly');
});

it('refuse la suppression d’un entrepôt actif', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin, ['status' => 'active', 'code' => 'ACT-01']);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.index'))
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($warehouse->fresh()->trashed())->toBeFalse();
});

it('gère la corbeille, la restauration et la suppression définitive', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin, ['status' => 'inactive', 'code' => 'TRASH-01']);

    // Suppression temporaire
    $this->actingAs($admin)
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($warehouse->fresh()->trashed())->toBeTrue();
    expect(ProductWarehouseTranslation::onlyTrashed()->where('warehouse_id', $warehouse->id)->count())->toBe(2);

    // Corbeille
    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouses.trashed'))
        ->assertOk()
        ->getContent();
    expect($html)->toContain('product-warehouses-table');
    expect($html)->toContain($warehouse->code);

    // Restauration (restaure aussi les traductions)
    $this->actingAs($admin)
        ->patch(route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($warehouse->fresh()->trashed())->toBeFalse();
    expect(ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)->count())->toBe(2);

    // Nouvelle suppression puis suppression définitive
    $this->actingAs($admin)
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertSessionHas('success');

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouses.force_destroy', ['uuid' => $warehouse->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductWarehouse::withTrashed()->where('uuid', $warehouse->uuid)->exists())->toBeFalse();
});

it('protège la dernière traduction d’un entrepôt', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin, ['code' => 'TRAD-01']);

    $translations = $warehouse->translations()->get();
    expect($translations)->toHaveCount(2);

    // Suppression d'une première traduction : autorisée
    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-translations.destroy', ['uuid' => $translations[0]->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($warehouse->translations()->count())->toBe(1);

    // Suppression de la dernière traduction active : refusée
    $remaining = $warehouse->translations()->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-translations.destroy', ['uuid' => $remaining->uuid]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($warehouse->translations()->count())->toBe(1);
});

it('respecte les droits par rôle sur les actions sensibles', function () {
    $admin = warehousesAdmin();
    $warehouse = createWarehouse($this, $admin, ['status' => 'inactive', 'code' => 'RBAC-01']);
    $translation = $warehouse->translations()->firstOrFail();

    $moderator = warehousesModerator();

    // Actions de lecture / écriture autorisées
    $this->actingAs($moderator)->get(route('backend.product-warehouses.index'))->assertOk();
    $this->actingAs($moderator)->get(route('backend.product-warehouses.create'))->assertOk();

    // Actions sensibles interdites
    $this->actingAs($moderator)
        ->patch(route('backend.product-warehouses.change_status', ['productWarehouse' => $warehouse->uuid]), ['status' => 'active'])
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->get(route('backend.product-warehouses.trashed'))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->patch(route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouses.force_destroy', ['uuid' => $warehouse->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouse-translations.destroy', ['uuid' => $translation->uuid]))
        ->assertForbidden();
});

it('n’affiche aucune clé de langue brute dans les messages de validation', function () {
    $admin = warehousesAdmin();

    // Code invalide
    $response = $this->actingAs($admin)
        ->from(route('backend.product-warehouses.create'))
        ->post(route('backend.product-warehouses.store'), warehousePayload(['code' => 'code invalide!']));

    $response->assertSessionHasErrors(['code']);
    $codeMessage = session('errors')->first('code');

    expect($codeMessage)->toBeString()->not->toBeEmpty();
    expect($codeMessage)->not->toContain('warehouses-messages-validation');
    expect($codeMessage)->not->toContain('code.regex');
});
