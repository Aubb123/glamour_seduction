<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\Products\Warehouses\ProductWarehouseStockTrackingMode;
use App\Models\ProductVariant;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationStock;
use Illuminate\Database\Seeder;

class ProductWarehouseLocationStockSeeder extends Seeder
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

        // ── Données des stocks par emplacement (entrepôts en mode « location » uniquement) ──
        // (location_code, sku, quantity)
        $stocks = [
            // COT-01 (Cotonou, actif, par défaut) — bacs de stockage
            ['location_code' => 'COT-01-B1', 'sku' => 'MAGIC-ROUGE', 'quantity' => 15],
            ['location_code' => 'COT-01-B1', 'sku' => 'MAGIC-NOIR',  'quantity' => 10],
            ['location_code' => 'COT-01-B1', 'sku' => 'MAGIC-BLANC', 'quantity' => 8],

            // PN-01 (Porto-Novo, actif) — bac de réassort
            ['location_code' => 'PN-01-B1',  'sku' => 'MAGIC-ROUGE', 'quantity' => 20],
            ['location_code' => 'PN-01-B1',  'sku' => 'MAGIC-NOIR',  'quantity' => 15],
            ['location_code' => 'PN-01-B1',  'sku' => 'MAGIC-BLANC', 'quantity' => 5],
        ];

        foreach ($stocks as $data) {

            // ── Emplacement ──
            $location = ProductWarehouseLocation::where('code', $data['location_code'])->first();

            if (! $location) {
                $this->command->error('Emplacement non trouvé pour le code : '.$data['location_code']."\n");

                continue;
            }

            if (! $location->warehouse || $location->warehouse->stock_tracking_mode !== ProductWarehouseStockTrackingMode::Location) {
                $this->command->warn("L'emplacement {$data['location_code']} n'appartient pas à un entrepôt en mode « location », stock ignoré.");

                continue;
            }

            // ── Variante ──
            $variant = ProductVariant::where('sku', $data['sku'])->first();

            if (! $variant) {
                $this->command->error('Variante non trouvée pour le SKU : '.$data['sku']."\n");

                continue;
            }

            ProductWarehouseLocationStock::updateOrCreate(
                [
                    'location_id' => $location->id,
                    'product_variant_id' => $variant->id,
                ],
                [
                    'quantity' => $data['quantity'],
                ]
            );
        }

        $this->command->info('ProductWarehouseLocationStockSeeder : stocks par emplacement créés.');
    }
}
