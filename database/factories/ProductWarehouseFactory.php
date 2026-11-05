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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'WH-'.strtoupper($this->faker->unique()->bothify('??##')),
            'name' => $this->faker->company().' — Entrepôt',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'phone' => $this->faker->phoneNumber(),
            'status' => ProductWarehouseStatus::Inactive,
            'is_default' => false,
        ];
    }

    /**
     * État : entrepôt actif (utilisable pour les stocks).
     */
    public function active(): static
    {
        return $this->state(fn () => ['status' => ProductWarehouseStatus::Active]);
    }

    /**
     * État : entrepôt par défaut (réservations). Un seul par boutique.
     */
    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
