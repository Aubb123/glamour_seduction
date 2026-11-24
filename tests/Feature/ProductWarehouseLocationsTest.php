<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationTranslation;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\ProductWarehouseSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Boutique — Emplacements d'entrepôt (`product-warehouse-locations`) et leurs traductions.
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(SettingSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    $this->seed(ProductWarehouseSeeder::class);
    bootV1WebRoutes();
});

function locationsAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
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

function locationsModerator(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $moderator = User::create([
        'country_id' => $country->id,
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

/**
 * Charge utile d'un emplacement (entrepôt COT-01 par défaut).
 */
function locationPayload(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();
    $warehouse = ProductWarehouse::where('code', 'COT-01')->firstOrFail();
    $uid = strtoupper(uniqid());

    return array_merge([
        'warehouse_id' => $warehouse->id,
        'parent_id' => null,
        'type' => 'shelf',
        'status' => 'inactive',
        'code' => 'LOC-'.substr($uid, -8),
        'sort_order' => random_int(1, 1000000000),
        'notes' => 'Emplacement de test.',
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Emplacement '.$uid, 'description' => 'Description FR.'],
            ['lang_id' => $langEn->id, 'name' => 'Location '.$uid, 'description' => 'Description EN.'],
        ],
    ], $overrides);
}

/**
 * Crée un emplacement via HTTP et renvoie le modèle persisté.
 */
function createLocation($test, User $admin, array $overrides = []): ProductWarehouseLocation
{
    $test->actingAs($admin)->post(route('backend.product-warehouse-locations.store'), locationPayload($overrides));

    return ProductWarehouseLocation::latest('id')->firstOrFail();
}

it('liste les emplacements avec DataTables', function () {
    $admin = locationsAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouse-locations.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouse-locations-table');
    expect($html)->toContain("$('#product-warehouse-locations-table').DataTable");
});

it("affiche la page de création d'un emplacement", function () {
    $admin = locationsAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouse-locations.create'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('create-location-form');
    expect($html)->toContain('translations[fr][name]');
    expect($html)->toContain('translations[en][name]');
    expect($html)->toContain('data-next-sort-orders');
});

it('crée un emplacement avec ses traductions', function () {
    $admin = locationsAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-locations.store'), locationPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    $location = ProductWarehouseLocation::firstOrFail();

    expect($location->translations()->count())->toBe(2);
    expect($location->status->value)->toBe('inactive');
    expect($location->parent_id)->toBeNull();
    expect($location->active_translation?->name)->not->toBeNull();
});

it("pré-remplit le prochain ordre d'affichage global", function () {
    $admin = locationsAdmin();

    createLocation($this, $admin, ['sort_order' => 40]);

    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouse-locations.create'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('value="41"');
});

it("refuse un sort_order déjà utilisé dans le même entrepôt mais l'accepte dans un autre", function () {
    $admin = locationsAdmin();
    $cot = ProductWarehouse::where('code', 'COT-01')->firstOrFail();
    $pn = ProductWarehouse::where('code', 'PN-01')->firstOrFail();

    createLocation($this, $admin, ['warehouse_id' => $cot->id, 'sort_order' => 500]);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.create'))
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $cot->id,
            'sort_order' => 500,
        ]))
        ->assertSessionHasErrors(['sort_order']);

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $pn->id,
            'sort_order' => 500,
        ]))
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('accepte de conserver son propre sort_order en édition', function () {
    $admin = locationsAdmin();
    $location = createLocation($this, $admin, ['sort_order' => 700]);

    $payload = locationPayload([
        'warehouse_id' => $location->warehouse_id,
        'sort_order' => 700,
        'translations' => [
            ['lang_id' => Lang::where('code', 'fr')->value('id'), 'name' => $location->translations()->first()->name, 'description' => 'Description de test.'],
            ['lang_id' => Lang::where('code', 'en')->value('id'), 'name' => 'Updated '.uniqid(), 'description' => 'Description de test.'],
        ],
    ]);

    $this->actingAs($admin)
        ->put(route('backend.product-warehouse-locations.update', ['productWarehouseLocation' => $location->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($location->fresh()->sort_order)->toBe(700);
});

it('accepte le même nom dans deux entrepôts mais le refuse deux fois dans le même', function () {
    $admin = locationsAdmin();
    $cot = ProductWarehouse::where('code', 'COT-01')->firstOrFail();
    $pn = ProductWarehouse::where('code', 'PN-01')->firstOrFail();
    $langFr = Lang::where('code', 'fr')->value('id');
    $langEn = Lang::where('code', 'en')->value('id');

    $translations = [
        ['lang_id' => $langFr, 'name' => 'Zone Partagée', 'description' => 'Description de test.'],
        ['lang_id' => $langEn, 'name' => 'Shared Zone', 'description' => 'Description de test.'],
    ];

    // 1. COT-01
    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $cot->id,
            'translations' => $translations,
        ]))
        ->assertSessionHas('success');

    // 2. PN-01 : même nom, autre entrepôt → autorisé
    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $pn->id,
            'translations' => $translations,
        ]))
        ->assertSessionHas('success');

    // 3. COT-01 : même nom, même entrepôt, même langue → refusé
    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.create'))
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $cot->id,
            'translations' => $translations,
        ]))
        ->assertSessionHasErrors(['translations.0.name']);

    expect(ProductWarehouseLocation::where('warehouse_id', $cot->id)->count())->toBe(1);
});

it('accepte le même nom deux fois dans le même entrepôt pour des langues différentes', function () {
    $admin = locationsAdmin();
    $langFr = Lang::where('code', 'fr')->value('id');
    $langEn = Lang::where('code', 'en')->value('id');

    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'translations' => [
                ['lang_id' => $langFr, 'name' => 'Nom Identique', 'description' => 'Description de test.'],
                ['lang_id' => $langEn, 'name' => 'Nom Identique', 'description' => 'Description de test.'],
            ],
        ]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductWarehouseLocation::firstOrFail()->translations()->count())->toBe(2);
});

it('refuse un nom déjà utilisé dans le même entrepôt lors de l’édition', function () {
    $admin = locationsAdmin();
    $langFr = Lang::where('code', 'fr')->value('id');
    $langEn = Lang::where('code', 'en')->value('id');

    createLocation($this, $admin, ['translations' => [
        ['lang_id' => $langFr, 'name' => 'Nom A', 'description' => 'Description de test.'],
        ['lang_id' => $langEn, 'name' => 'Name A', 'description' => 'Description de test.'],
    ]]);

    $second = createLocation($this, $admin, ['translations' => [
        ['lang_id' => $langFr, 'name' => 'Nom B', 'description' => 'Description de test.'],
        ['lang_id' => $langEn, 'name' => 'Name B', 'description' => 'Description de test.'],
    ]]);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.edit', ['productWarehouseLocation' => $second->uuid]))
        ->put(route('backend.product-warehouse-locations.update', ['productWarehouseLocation' => $second->uuid]), locationPayload([
            'warehouse_id' => $second->warehouse_id,
            'sort_order' => $second->sort_order,
            'translations' => [
                ['lang_id' => $langFr, 'name' => 'Nom A', 'description' => 'Description de test.'],
                ['lang_id' => $langEn, 'name' => 'Name B', 'description' => 'Description de test.'],
            ],
        ]))
        ->assertSessionHasErrors(['translations.0.name']);
});

it('accepte une édition sans changer le nom', function () {
    $admin = locationsAdmin();
    $location = createLocation($this, $admin);

    $translations = $location->translations()->get()->map(fn ($translation) => [
        'lang_id' => $translation->lang_id,
        'name' => $translation->name,
        'description' => $translation->description,
    ])->all();

    $this->actingAs($admin)
        ->put(route('backend.product-warehouse-locations.update', ['productWarehouseLocation' => $location->uuid]), locationPayload([
            'warehouse_id' => $location->warehouse_id,
            'sort_order' => $location->sort_order,
            'translations' => $translations,
        ]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($location->fresh()->translations()->count())->toBe(2);
});

it('refuse un code en doublon dans le même entrepôt (corbeille incluse) mais l’accepte ailleurs', function () {
    $admin = locationsAdmin();
    $cot = ProductWarehouse::where('code', 'COT-01')->firstOrFail();
    $pn = ProductWarehouse::where('code', 'PN-01')->firstOrFail();

    $location = createLocation($this, $admin, ['warehouse_id' => $cot->id, 'code' => 'DUP-01']);

    // Mise en corbeille (le code reste réservé)
    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-locations.destroy', ['productWarehouseLocation' => $location->uuid]))
        ->assertSessionHas('success');

    expect($location->fresh()->trashed())->toBeTrue();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.create'))
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $cot->id,
            'code' => 'DUP-01',
        ]))
        ->assertSessionHasErrors(['code']);

    // Autre entrepôt : autorisé
    $this->actingAs($admin)
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $pn->id,
            'code' => 'DUP-01',
        ]))
        ->assertSessionHas('success');
});

it('refuse un code invalide', function () {
    $admin = locationsAdmin();

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.create'))
        ->post(route('backend.product-warehouse-locations.store'), locationPayload(['code' => 'code invalide!']))
        ->assertSessionHasErrors(['code']);

    expect(ProductWarehouseLocation::count())->toBe(0);
});

it('ne permet pas de modifier le code ni l’entrepôt après création', function () {
    $admin = locationsAdmin();
    $pn = ProductWarehouse::where('code', 'PN-01')->firstOrFail();

    $location = createLocation($this, $admin, ['code' => 'FIXE-01']);

    $this->actingAs($admin)
        ->put(route('backend.product-warehouse-locations.update', ['productWarehouseLocation' => $location->uuid]), locationPayload([
            'warehouse_id' => $pn->id,
            'code' => 'NOUVEAU-02',
            'sort_order' => $location->sort_order,
            'translations' => $location->translations()->get()->map(fn ($translation) => [
                'lang_id' => $translation->lang_id,
                'name' => $translation->name,
                'description' => 'Description de test.',
            ])->all(),
        ]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $fresh = $location->fresh();

    expect($fresh->code)->toBe('FIXE-01');
    expect($fresh->warehouse_id)->toBe($location->warehouse_id);
});

it('autorise librement les types (bin sous une zone)', function () {
    $admin = locationsAdmin();

    $zone = createLocation($this, $admin, ['type' => 'zone']);

    $bin = createLocation($this, $admin, [
        'type' => 'bin',
        'parent_id' => $zone->id,
    ]);

    expect($bin->fresh()->parent_id)->toBe($zone->id);
    expect($bin->fresh()->type->value)->toBe('bin');
});

it("refuse un parent d'un autre entrepôt ou en corbeille", function () {
    $admin = locationsAdmin();
    $cot = ProductWarehouse::where('code', 'COT-01')->firstOrFail();
    $pn = ProductWarehouse::where('code', 'PN-01')->firstOrFail();

    // Parent d'un autre entrepôt
    $foreignParent = createLocation($this, $admin, ['warehouse_id' => $pn->id]);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.create'))
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $cot->id,
            'parent_id' => $foreignParent->id,
        ]))
        ->assertSessionHasErrors(['parent_id']);

    // Parent en corbeille
    $trashedParent = createLocation($this, $admin, ['warehouse_id' => $cot->id]);

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-locations.destroy', ['productWarehouseLocation' => $trashedParent->uuid]))
        ->assertSessionHas('success');

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.create'))
        ->post(route('backend.product-warehouse-locations.store'), locationPayload([
            'warehouse_id' => $cot->id,
            'parent_id' => $trashedParent->id,
        ]))
        ->assertSessionHasErrors(['parent_id']);
});

it('refuse un parent égal à soi-même ou un cycle', function () {
    $admin = locationsAdmin();

    $root = createLocation($this, $admin, ['type' => 'zone']);
    $child = createLocation($this, $admin, ['type' => 'shelf', 'parent_id' => $root->id]);

    $rootTranslations = $root->translations()->get()->map(fn ($translation) => [
        'lang_id' => $translation->lang_id,
        'name' => $translation->name,
        'description' => 'Description de test.',
    ])->all();

    // Self
    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.edit', ['productWarehouseLocation' => $root->uuid]))
        ->put(route('backend.product-warehouse-locations.update', ['productWarehouseLocation' => $root->uuid]), locationPayload([
            'warehouse_id' => $root->warehouse_id,
            'sort_order' => $root->sort_order,
            'parent_id' => $root->id,
            'translations' => $rootTranslations,
        ]))
        ->assertSessionHasErrors(['parent_id']);

    // Cycle (root rattaché à son propre enfant)
    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.edit', ['productWarehouseLocation' => $root->uuid]))
        ->put(route('backend.product-warehouse-locations.update', ['productWarehouseLocation' => $root->uuid]), locationPayload([
            'warehouse_id' => $root->warehouse_id,
            'sort_order' => $root->sort_order,
            'parent_id' => $child->id,
            'translations' => $rootTranslations,
        ]))
        ->assertSessionHasErrors(['parent_id']);

    expect($root->fresh()->parent_id)->toBeNull();
});

it('retourne une 404 quand le code ne correspond à aucun emplacement', function () {
    $admin = locationsAdmin();
    $cot = ProductWarehouse::where('code', 'COT-01')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => '00000000-0000-0000-0000-000000000000']))
        ->assertNotFound();
});

it('refuse la suppression d’un emplacement actif ou ayant des enfants', function () {
    $admin = locationsAdmin();

    // Actif → refusé
    $active = createLocation($this, $admin, ['status' => 'active', 'code' => 'ACT-01']);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.index'))
        ->delete(route('backend.product-warehouse-locations.destroy', ['productWarehouseLocation' => $active->uuid]))
        ->assertSessionHas('error');

    expect($active->fresh()->trashed())->toBeFalse();

    // Avec enfants → refusé
    $parent = createLocation($this, $admin, ['type' => 'zone', 'code' => 'PAR-01']);
    createLocation($this, $admin, ['type' => 'shelf', 'parent_id' => $parent->id, 'code' => 'CHI-01']);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.index'))
        ->delete(route('backend.product-warehouse-locations.destroy', ['productWarehouseLocation' => $parent->uuid]))
        ->assertSessionHas('error');

    expect($parent->fresh()->trashed())->toBeFalse();
});

it('empêche la suppression d’un entrepôt qui possède des emplacements', function () {
    $admin = locationsAdmin();

    $warehouse = ProductWarehouse::create([
        'sort_order' => 99,
        'code' => 'TMP-99',
        'type' => 'storage',
        'status' => 'inactive',
        'is_default' => false,
    ]);

    $location = createLocation($this, $admin, ['warehouse_id' => $warehouse->id]);

    $this->actingAs($admin)
        ->from(route('backend.product-warehouses.index'))
        ->delete(route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]))
        ->assertSessionHas('error');

    expect($warehouse->fresh()->trashed())->toBeFalse();
    expect($location->fresh())->not->toBeNull();
});

it('gère la corbeille, la restauration et la suppression définitive', function () {
    $admin = locationsAdmin();
    $location = createLocation($this, $admin, ['code' => 'TRASH-01']);

    // Suppression temporaire
    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-locations.destroy', ['productWarehouseLocation' => $location->uuid]))
        ->assertSessionHas('success');

    expect($location->fresh()->trashed())->toBeTrue();
    expect(ProductWarehouseLocationTranslation::onlyTrashed()->where('location_id', $location->id)->count())->toBe(2);

    // Page corbeille
    $html = $this->actingAs($admin)
        ->get(route('backend.product-warehouse-locations.trashed'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-warehouse-locations-table');
    expect($html)->toContain('TRASH-01');

    // Restauration (restaure aussi les traductions)
    $this->actingAs($admin)
        ->patch(route('backend.product-warehouse-locations.restore', ['uuid' => $location->uuid]))
        ->assertSessionHas('success');

    expect($location->fresh()->trashed())->toBeFalse();
    expect($location->fresh()->translations()->count())->toBe(2);

    // Nouvelle suppression puis suppression définitive
    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-locations.destroy', ['productWarehouseLocation' => $location->uuid]))
        ->assertSessionHas('success');

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-locations.force_destroy', ['uuid' => $location->uuid]))
        ->assertSessionHas('success');

    expect(ProductWarehouseLocation::withTrashed()->where('uuid', $location->uuid)->exists())->toBeFalse();
});

it('protège la dernière traduction d’un emplacement', function () {
    $admin = locationsAdmin();
    $location = createLocation($this, $admin);

    $translations = $location->translations()->get();
    expect($translations)->toHaveCount(2);

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-location-translations.destroy', ['uuid' => $translations[0]->uuid]))
        ->assertSessionHas('success');

    expect($location->fresh()->translations()->count())->toBe(1);

    $remaining = $location->fresh()->translations()->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.product-warehouse-location-translations.destroy', ['uuid' => $remaining->uuid]))
        ->assertSessionHas('error');

    expect($location->fresh()->translations()->count())->toBe(1);
});

it('respecte les droits par rôle sur les actions sensibles', function () {
    $admin = locationsAdmin();
    $location = createLocation($this, $admin, ['code' => 'RBAC-01']);
    $translation = $location->translations()->firstOrFail();

    $moderator = locationsModerator();

    // Actions de lecture / écriture autorisées
    $this->actingAs($moderator)->get(route('backend.product-warehouse-locations.index'))->assertOk();
    $this->actingAs($moderator)->get(route('backend.product-warehouse-locations.create'))->assertOk();

    $params = ['productWarehouseLocation' => $location->uuid];

    // Actions sensibles interdites
    $this->actingAs($moderator)
        ->patch(route('backend.product-warehouse-locations.activate', $params))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouse-locations.destroy', $params))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->get(route('backend.product-warehouse-locations.trashed'))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->patch(route('backend.product-warehouse-locations.restore', ['uuid' => $location->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouse-locations.force_destroy', ['uuid' => $location->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('backend.product-warehouse-location-translations.destroy', ['uuid' => $translation->uuid]))
        ->assertForbidden();
});

it('n’affiche aucune clé de langue brute dans les messages de validation', function () {
    $admin = locationsAdmin();

    $response = $this->actingAs($admin)
        ->from(route('backend.product-warehouse-locations.create'))
        ->post(route('backend.product-warehouse-locations.store'), locationPayload(['code' => 'code invalide!']));

    $response->assertSessionHasErrors(['code']);

    $codeMessage = session('errors')->first('code');

    expect($codeMessage)->toBeString()->not->toBeEmpty();
    expect($codeMessage)->not->toContain('warehouse-locations-messages-validation');
    expect($codeMessage)->not->toContain('code.regex');
});
