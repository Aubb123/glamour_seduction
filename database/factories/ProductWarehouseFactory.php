<?php

namespace Database\Factories;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Models\ProductWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductWarehouse>
 */
class ProductWarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * NB : les champs traduisibles (name, address, city, phone) sont portés par
     * product_warehouse_translations. « Entrepôt par défaut » est une notion PAR
     * BOUTIQUE (pivot shop_warehouse.is_primary), gérée par ShopService.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'WH-'.strtoupper($this->faker->unique()->bothify('??##')),
            'status' => ProductWarehouseStatus::Inactive,
        ];
    }

    /**
     * État : entrepôt actif (utilisable pour les stocks).
     */
    public function active(): static
    {
        return $this->state(fn () => ['status' => ProductWarehouseStatus::Active]);
    }
}
