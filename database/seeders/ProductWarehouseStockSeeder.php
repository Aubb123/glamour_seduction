<?php

namespace Database\Seeders;

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
        // Récupère les entrepôts et les variantes seedés par les modules précédents
        $warehouses = ProductWarehouse::where('status', 'active')->get();
        $variants = ProductVariant::all();

        // Crée un stock initial pour chaque couple (entrepôt actif, variante)
        foreach ($variants as $variant) {
            foreach ($warehouses as $warehouse) {
                ProductWarehouseStock::create([
                    'warehouse_id' => $warehouse->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => fake()->numberBetween(10, 200),
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);
            }
        }
    }
}
