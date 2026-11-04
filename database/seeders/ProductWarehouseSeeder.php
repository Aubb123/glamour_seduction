<?php

namespace Database\Seeders;

use App\Models\ProductWarehouse;
use Illuminate\Database\Seeder;

class ProductWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'code' => 'COT-01',
                'name' => 'Cotonou — Entrepôt principal',
                'address' => 'Zone industrielle, Route de l\'Aéroport',
                'city' => 'Cotonou',
                'phone' => '+229 21 00 00 01',
                'status' => 'active',
                'is_default' => true,
            ],
            [
                'code' => 'PKO-01',
                'name' => 'Porto-Novo — Entrepôt secondaire',
                'address' => 'Avenue Steinmetz',
                'city' => 'Porto-Novo',
                'phone' => '+229 21 00 00 02',
                'status' => 'active',
                'is_default' => false,
            ],
            [
                'code' => 'PKU-01',
                'name' => 'Parakou — Entrepôt régional',
                'address' => 'Quartier Banikanni',
                'city' => 'Parakou',
                'phone' => '+229 21 00 00 03',
                'status' => 'inactive',
                'is_default' => false,
            ],
        ];

        foreach ($warehouses as $data) {
            ProductWarehouse::create($data);
        }
    }
}
