<?php

declare(strict_types=1);

use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use Database\Seeders\LangSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

/**
 * La colonne `name` a été RETIRÉE de `product_warehouses` (le nom vit dans
 * product_warehouse_translations). Plusieurs appels historiques `orderBy('name')`
 * et affichages `$warehouse->name` subsistaient et provoquaient :
 *   - un 500 (`Unknown column 'name' in 'order clause'`) ;
 *   - des noms vides à l'affichage.
 *
 * Ce test verrouille les deux garde-fous ajoutés sur le modèle :
 *   - ProductWarehouseBuilder (orderBy('name') → orderBy('code')) ;
 *   - getNameAttribute() (nom traduit actif, repli sur code).
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
});

it('ne possède plus la colonne name sur la table principale', function () {
    expect(Schema::hasColumn('product_warehouses', 'name'))->toBeFalse();
});

it('ne plante pas sur orderBy(name) (redirigé vers code par le builder)', function () {
    ProductWarehouse::create(['code' => 'WH-B', 'status' => 'active']);
    ProductWarehouse::create(['code' => 'WH-A', 'status' => 'active']);

    // Avant correctif : « Unknown column 'name' in 'order clause' » (500).
    $codes = ProductWarehouse::orderBy('name')->pluck('code')->all();

    expect($codes)->toBe(['WH-A', 'WH-B']);

    // Idem via le scope active() utilisé par ShopController create/edit.
    $active = ProductWarehouse::active()->orderBy('name')->pluck('code')->all();

    expect($active)->toBe(['WH-A', 'WH-B']);
});

it('expose $warehouse->name via le nom traduit actif', function () {
    $fr = Lang::where('code', 'fr')->firstOrFail();

    $warehouse = ProductWarehouse::create(['code' => 'COT-01', 'status' => 'active']);

    ProductWarehouseTranslation::create([
        'warehouse_id' => $warehouse->id,
        'lang_id' => $fr->id,
        'name' => 'Cotonou — Entrepôt principal',
    ]);

    expect($warehouse->fresh()->name)->toBe('Cotonou — Entrepôt principal');
});

it('replie $warehouse->name sur le code sans traduction', function () {
    $warehouse = ProductWarehouse::create(['code' => 'PKO-01', 'status' => 'active']);

    expect($warehouse->fresh()->name)->toBe('PKO-01');
});
