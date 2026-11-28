<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductWarehouseStock>
 */
class ProductWarehouseStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * NB : un couple (entrepôt, variante) est unique ; la quantité est positive.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_id' => ProductWarehouse::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'quantity' => $this->faker->numberBetween(0, 9999),
        ];
    }
}
