<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\Products\Warehouses\ProductWarehouseStockTrackingMode;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseStock;
use Illuminate\Database\Seeder;

class ProductWarehouseStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Variantes de référence ──
        $variants = ProductVariant::orderBy('id')->get();

        if ($variants->isEmpty()) {
            $this->command->error('Aucune variante de produit trouvée. Veuillez exécuter ProductVariantSeeder avant.');

            return;
        }

        // ── Données des stocks (entrepôts en mode « warehouse » uniquement) ──
        // (warehouse_code, sku, quantity)
        $stocks = [
            // RET-01 (retours, actif) — stock global
            ['warehouse_code' => 'RET-01', 'sku' => 'MAGIC-ROUGE', 'quantity' => 25],
            ['warehouse_code' => 'RET-01', 'sku' => 'MAGIC-NOIR',  'quantity' => 18],
            ['warehouse_code' => 'RET-01', 'sku' => 'MAGIC-BLANC', 'quantity' => 12],

            // TRA-01 (transit, inactif) — stock global
            ['warehouse_code' => 'TRA-01', 'sku' => 'MAGIC-ROUGE', 'quantity' => 50],
            ['warehouse_code' => 'TRA-01', 'sku' => 'MAGIC-NOIR',  'quantity' => 30],
            ['warehouse_code' => 'TRA-01', 'sku' => 'MAGIC-BLANC', 'quantity' => 0],
        ];

        foreach ($stocks as $data) {

            // ── Entrepôt ──
            $warehouse = ProductWarehouse::where('code', $data['warehouse_code'])->first();

            if (! $warehouse) {
                $this->command->error('Entrepôt non trouvé pour le code : '.$data['warehouse_code']."\n");

                continue;
            }

            if ($warehouse->stock_tracking_mode !== ProductWarehouseStockTrackingMode::Warehouse) {
                $this->command->warn("L'entrepôt {$data['warehouse_code']} n'est pas en mode « warehouse », stock ignoré.");

                continue;
            }

            // ── Variante ──
            $variant = ProductVariant::where('sku', $data['sku'])->first();

            if (! $variant) {
                $this->command->error('Variante non trouvée pour le SKU : '.$data['sku']."\n");

                continue;
            }

            ProductWarehouseStock::updateOrCreate(
                [
                    'warehouse_id' => $warehouse->id,
                    'product_variant_id' => $variant->id,
                ],
                [
                    'quantity' => $data['quantity'],
                ]
            );
        }

        $this->command->info('ProductWarehouseStockSeeder : stocks globaux créés.');
    }
}
