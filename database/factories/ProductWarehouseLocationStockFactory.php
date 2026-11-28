<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductWarehouseLocationStock>
 */
class ProductWarehouseLocationStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * NB : un couple (emplacement, variante) est unique ; la quantité est positive.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => ProductWarehouseLocation::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'quantity' => $this->faker->numberBetween(0, 9999),
        ];
    }
}
