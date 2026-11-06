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
     * product_warehouse_translations. La factory ne renseigne donc que les données
     * non traduites ; utilisez le state withTranslations() pour créer les traductions.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'WH-'.strtoupper($this->faker->unique()->bothify('??##')),
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
