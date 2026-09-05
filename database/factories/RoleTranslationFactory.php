<?php

namespace Database\Factories;

use App\Models\RoleTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoleTranslation>
 */
class RoleTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // role_id et lang_id sont fournis via ->for() / ->for() lors de l'utilisation
            'label' => fake()->unique()->jobTitle(), // Le slug est généré automatiquement depuis le label (HasSlug)
            'description' => fake()->paragraph(),
        ];
    }
}
