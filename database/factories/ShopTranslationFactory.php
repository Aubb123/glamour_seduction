<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\ShopTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopTranslation>
 */
class ShopTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'lang_id' => 1,
            'name' => $this->faker->unique()->company(),
            'description' => $this->faker->sentence(),
        ];
    }
}
