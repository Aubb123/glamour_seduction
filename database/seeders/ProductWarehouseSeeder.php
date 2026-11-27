<?php

namespace Database\Seeders;

use App\Models\GeoDivision;
use App\Models\GeoDivisionType;
use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use Illuminate\Database\Seeder;

class ProductWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Commune utilisée pour localiser les entrepôts (Cotonou, Bénin)
        $communeType = GeoDivisionType::where('code', 'commune')->first();
        $cotonou = GeoDivision::where('division_type_id', $communeType?->id)
            ->whereHas('translations', fn ($query) => $query->where('name', 'Cotonou'))
            ->first();

        $warehouses = [
            [
                'code' => 'COT-01',
                'sort_order' => 1,
                'type' => 'storage',
                'stock_tracking_mode' => 'location',
                'city' => 'Cotonou',
                'address' => 'Zone industrielle de Fidjrossè, Cotonou',
                'latitude' => 6.3653600,
                'longitude' => 2.4183300,
                'phone' => '+22997000001',
                'email' => 'cotonou@glamour-seduction.test',
                'notes' => 'Entrepôt principal de la boutique : réception, stockage et préparation des commandes.',
                'status' => 'active',
                'is_default' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Entrepôt principal Cotonou', 'description' => 'Entrepôt principal de la boutique, situé à Cotonou. Il centralise la réception, le stockage et la préparation des commandes.'],
                    ['lang_code' => 'en', 'name' => 'Cotonou Main Warehouse', 'description' => 'Main store warehouse located in Cotonou. It handles receiving, storage and order preparation.'],
                ],
            ],
            [
                'code' => 'PN-01',
                'sort_order' => 2,
                'type' => 'storage',
                'stock_tracking_mode' => 'location',
                'city' => 'Porto-Novo',
                'address' => 'Avenue Jean Bayol, Porto-Novo',
                'latitude' => 6.4969000,
                'longitude' => 2.6289000,
                'phone' => '+22997000002',
                'email' => 'portonovo@glamour-seduction.test',
                'notes' => 'Entrepôt secondaire pour la zone est du pays.',
                'status' => 'active',
                'is_default' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Entrepôt Porto-Novo', 'description' => 'Entrepôt secondaire desservant la zone est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Porto-Novo Warehouse', 'description' => 'Secondary warehouse serving the eastern part of Benin.'],
                ],
            ],
            [
                'code' => 'RET-01',
                'sort_order' => 3,
                'type' => 'returns',
                'stock_tracking_mode' => 'warehouse',
                'city' => 'Cotonou',
                'address' => 'Rue 12.045, Akpakpa, Cotonou',
                'latitude' => 6.3700000,
                'longitude' => 2.4400000,
                'phone' => '+22997000003',
                'email' => 'retours@glamour-seduction.test',
                'notes' => 'Entrepôt dédié au contrôle et au reconditionnement des retours clients.',
                'status' => 'active',
                'is_default' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Entrepôt des retours', 'description' => 'Entrepôt dédié au contrôle, au reconditionnement et à la remise en stock des retours clients.'],
                    ['lang_code' => 'en', 'name' => 'Returns Warehouse', 'description' => 'Dedicated warehouse for inspecting, refurbishing and restocking customer returns.'],
                ],
            ],
            [
                'code' => 'TRA-01',
                'sort_order' => 4,
                'type' => 'transit',
                'stock_tracking_mode' => 'warehouse',
                'city' => 'Cotonou',
                'address' => 'Port de Cotonou, terminal logistique',
                'latitude' => 6.3500000,
                'longitude' => 2.4300000,
                'phone' => '+22997000004',
                'email' => 'transit@glamour-seduction.test',
                'notes' => 'Entrepôt de transit utilisé lors des réassorts et des expéditions groupées.',
                'status' => 'inactive',
                'is_default' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Entrepôt de transit', 'description' => 'Entrepôt de transit utilisé pour les réassorts et les expéditions groupées.'],
                    ['lang_code' => 'en', 'name' => 'Transit Warehouse', 'description' => 'Transit warehouse used for restocking and bulk shipments.'],
                ],
            ],
        ];

        foreach ($warehouses as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer l'entrepôt
            $warehouse = ProductWarehouse::create([
                'code' => $data['code'],
                'sort_order' => $data['sort_order'],
                'type' => $data['type'],
                'stock_tracking_mode' => $data['stock_tracking_mode'],
                'division_id' => $cotonou?->id,
                'city' => $data['city'],
                'address' => $data['address'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'notes' => $data['notes'],
                'status' => $data['status'],
                'is_default' => $data['is_default'],
            ]);

            // Créer les traductions
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                ProductWarehouseTranslation::create([
                    'warehouse_id' => $warehouse->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
