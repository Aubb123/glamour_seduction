<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\Shop;
use App\Models\ShopTranslation;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = [
            [
                'code' => 'COT-SHOP-01',
                'email' => 'cotonou@glamour-seduction.bj',
                'phone' => '+229 21 00 10 01',
                'address' => 'Boulevard Saint-Michel',
                'city' => 'Cotonou',
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 1,
                'warehouse_codes' => ['COT-01', 'PKO-01'],
                'primary_warehouse_code' => 'COT-01',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Glamour Cotonou', 'description' => 'Boutique principale de Cotonou.'] ,
                    ['lang_code' => 'en', 'name' => 'Glamour Cotonou', 'description' => 'Main shop in Cotonou.'],
                ],
            ],
            [
                'code' => 'PKO-SHOP-01',
                'email' => 'portonovo@glamour-seduction.bj',
                'phone' => '+229 21 00 10 02',
                'address' => 'Avenue Jean Bayol',
                'city' => 'Porto-Novo',
                'status' => 'active',
                'is_default' => false,
                'sort_order' => 2,
                'warehouse_codes' => ['PKO-01'],
                'primary_warehouse_code' => 'PKO-01',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Glamour Porto-Novo', 'description' => 'Boutique de Porto-Novo.'],
                    ['lang_code' => 'en', 'name' => 'Glamour Porto-Novo', 'description' => 'Shop in Porto-Novo.'],
                ],
            ],
            [
                'code' => 'PKU-SHOP-01',
                'email' => 'parakou@glamour-seduction.bj',
                'phone' => '+229 21 00 10 03',
                'address' => 'Quartier Zongo',
                'city' => 'Parakou',
                'status' => 'inactive',
                'is_default' => false,
                'sort_order' => 3,
                'warehouse_codes' => ['PKU-01'],
                'primary_warehouse_code' => 'PKU-01',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Glamour Parakou', 'description' => 'Boutique régionale de Parakou.'] ,
                    ['lang_code' => 'en', 'name' => 'Glamour Parakou', 'description' => 'Regional shop in Parakou.'],
                ],
            ],
        ];

        foreach ($shops as $data) {

            $translations = $data['translations'];
            $warehouseCodes = $data['warehouse_codes'];
            $primaryWarehouseCode = $data['primary_warehouse_code'];
            unset($data['translations'], $data['warehouse_codes'], $data['primary_warehouse_code']);

            // Création de la boutique
            $shop = Shop::create($data);

            // Création des traductions
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                ShopTranslation::create([
                    'shop_id' => $shop->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }

            // Rattachement des entrepôts (au moins un — règle métier)
            $warehouseIds = ProductWarehouse::whereIn('code', $warehouseCodes)->pluck('id', 'code');
            $primaryId = $warehouseIds[$primaryWarehouseCode] ?? $warehouseIds->first();

            $syncData = [];

            foreach ($warehouseIds as $code => $id) {
                $syncData[$id] = ['is_primary' => $id === $primaryId];
            }

            if (! empty($syncData)) {
                $shop->warehouses()->sync($syncData);
            }
        }
    }
}
