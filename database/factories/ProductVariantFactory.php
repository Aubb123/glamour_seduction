<?php

namespace Database\Factories;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * NB : une variante appartient à un produit (product_id) ; le SKU est unique.
     * `price` reste NULL par défaut (la variante hérite alors de base_price du produit).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper('VAR-'.$this->faker->unique()->bothify('??##??')),
            'barcode' => $this->faker->optional()->ean13(),
            'price' => $this->faker->optional()->randomFloat(2, 1, 9999),
            'status' => ProductVariantStatus::Active,
            'is_default' => false,
        ];
    }

    /**
     * État : variante inactive (masquée temporairement).
     */
    public function inactive(): static
    {
        return $this->state(fn () => ['status' => ProductVariantStatus::Inactive]);
    }

    /**
     * État : variante par défaut du produit (une seule par produit).
     */
    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
