<?php

namespace Database\Seeders;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use Illuminate\Database\Seeder;

class ProductWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Chaque entrepôt est créé avec ses traductions (fr + en) : le nom, l'adresse,
     * la ville et le téléphone vivent désormais dans product_warehouse_translations.
     *
     * NB : « entrepôt par défaut » (is_default) n'est plus géré ici : c'est une notion
     * PAR BOUTIQUE, portée par le pivot shop_warehouse.is_primary (ShopSeeder).
     */
    public function run(): void
    {
        // Langues de référence (fr / en) — le seeder est idempotent
        $fr = Lang::where('code', 'fr')->first();
        $en = Lang::where('code', 'en')->first();

        $warehouses = [
            [
                'code' => 'COT-01',
                'status' => ProductWarehouseStatus::Active,
                'translations' => [
                    'fr' => [
                        'name' => 'Cotonou — Entrepôt principal',
                        'address' => 'Zone industrielle, Route de l\'Aéroport',
                        'city' => 'Cotonou',
                        'phone' => '+229 21 00 00 01',
                    ],
                    'en' => [
                        'name' => 'Cotonou — Main warehouse',
                        'address' => 'Industrial zone, Airport Road',
                        'city' => 'Cotonou',
                        'phone' => '+229 21 00 00 01',
                    ],
                ],
            ],
            [
                'code' => 'PKO-01',
                'status' => ProductWarehouseStatus::Active,
                'translations' => [
                    'fr' => [
                        'name' => 'Porto-Novo — Entrepôt secondaire',
                        'address' => 'Avenue Steinmetz',
                        'city' => 'Porto-Novo',
                        'phone' => '+229 21 00 00 02',
                    ],
                    'en' => [
                        'name' => 'Porto-Novo — Secondary warehouse',
                        'address' => 'Steinmetz Avenue',
                        'city' => 'Porto-Novo',
                        'phone' => '+229 21 00 00 02',
                    ],
                ],
            ],
            [
                'code' => 'PKU-01',
                'status' => ProductWarehouseStatus::Inactive,
                'translations' => [
                    'fr' => [
                        'name' => 'Parakou — Entrepôt régional',
                        'address' => 'Quartier Banikanni',
                        'city' => 'Parakou',
                        'phone' => '+229 21 00 00 03',
                    ],
                    'en' => [
                        'name' => 'Parakou — Regional warehouse',
                        'address' => 'Banikanni district',
                        'city' => 'Parakou',
                        'phone' => '+229 21 00 00 03',
                    ],
                ],
            ],
        ];

        foreach ($warehouses as $data) {

            $warehouse = ProductWarehouse::firstOrCreate(
                ['code' => $data['code']],
                [
                    'status' => $data['status'],
                ],
            );

            foreach ($data['translations'] as $code => $translation) {

                $lang = $code === 'fr' ? $fr : $en;

                if (! $lang) {
                    continue;
                }

                ProductWarehouseTranslation::firstOrCreate(
                    ['warehouse_id' => $warehouse->id, 'lang_id' => $lang->id],
                    [
                        'name' => $translation['name'],
                        'address' => $translation['address'] ?? null,
                        'city' => $translation['city'] ?? null,
                        'phone' => $translation['phone'] ?? null,
                    ],
                );
            }
        }
    }
}
