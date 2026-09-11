<?php

namespace Database\Seeders;

use App\Models\ProductStockMovement;
use App\Models\ProductWarehouseStock;
use Illuminate\Database\Seeder;

class ProductStockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * NB : les mouvements sont un journal d'audit immuable. Le seeder rejoue
     * l'entrée initiale qui a constitué le stock existant (type « in »),
     * afin que chaque ligne de stock ait au moins un mouvement de traçabilité.
     */
    public function run(): void
    {
        $stocks = ProductWarehouseStock::withTrashed()->get();

        foreach ($stocks as $stock) {
            // Ne crée un mouvement que si la ligne de stock a du contenu
            if ($stock->quantity <= 0) {
                continue;
            }

            ProductStockMovement::create([
                'warehouse_id' => $stock->warehouse_id,
                'product_variant_id' => $stock->product_variant_id,
                'type' => 'in',
                'quantity' => $stock->quantity,
                'note' => 'Stock initial (seeder)',
                'created_by' => null,
            ]);
        }
    }
}
