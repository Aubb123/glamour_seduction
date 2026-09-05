<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Format attendu : modele.action (ex: roles.edit)
            // 'name' => Str::slug(fake()->unique()->word()).'.'.Str::slug(fake()->word()),
            // 'guard_name' => 'web',
            // 'status' => 'active',
        ];
    }

    /**
     * Permission inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
