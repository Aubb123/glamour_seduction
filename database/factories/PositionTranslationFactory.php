<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\PositionTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PositionTranslation>
 */
class PositionTranslationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'position_id' => Position::factory(),
            'lang_id' => 1,
            'name' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->sentence(),
        ];
    }
}
