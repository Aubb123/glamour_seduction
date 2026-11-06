<?php

namespace Database\Factories;

use App\Enums\Shops\Shops\ShopStatus;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'SHOP-'.strtoupper($this->faker->unique()->bothify('??##')),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'status' => ShopStatus::Inactive,
            'is_default' => false,
            'sort_order' => 0,
        ];
    }

    /**
     * État : boutique active (ouverte).
     */
    public function active(): static
    {
        return $this->state(fn () => ['status' => ShopStatus::Active]);
    }

    /**
     * État : boutique par défaut (une seule par plateforme).
     */
    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
