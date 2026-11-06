<?php

namespace Database\Factories;

use App\Enums\Shops\Positions\PositionContext;
use App\Enums\Shops\Positions\PositionStatus;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'POS-'.strtoupper($this->faker->unique()->bothify('??##')),
            'context' => PositionContext::Both,
            'status' => PositionStatus::Inactive,
            'sort_order' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => PositionStatus::Active]);
    }

    public function shop(): static
    {
        return $this->state(fn () => ['context' => PositionContext::Shop]);
    }

    public function warehouse(): static
    {
        return $this->state(fn () => ['context' => PositionContext::Warehouse]);
    }
}
