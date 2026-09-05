<?php

namespace Database\Factories;

use App\Models\Lang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lang>
 */
class LangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->languageCode(), // fr, en, ar...
            'name' => fake()->unique()->word(), // Français, English...
            'native' => fake()->unique()->word(), // Nom dans sa propre langue
            'direction' => fake()->randomElement(['ltr', 'rtl']),
            'file_path' => 'flags/'.fake()->unique()->languageCode().'.svg',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => fake()->unique()->numberBetween(1, 10000),
        ];
    }
}
