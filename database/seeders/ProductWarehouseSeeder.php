<?php

namespace Database\Seeders;

use App\Models\GeoCountry;
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
        // Pays utilisé pour localiser les entrepôts (Bénin)
        $benin = GeoCountry::where('code', 'BEN')->first();

        $warehouses = [
            [
                'code' => 'COT-01',
                'sort_order' => 1,
                'type' => 'storage',
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
                    ['lang_code' => 'fr', 'name' => 'Entrepôt principal Cotonou', 'slug' => 'entrepot-principal-cotonou', 'description' => 'Entrepôt principal de la boutique, situé à Cotonou. Il centralise la réception, le stockage et la préparation des commandes.'],
                    ['lang_code' => 'en', 'name' => 'Cotonou Main Warehouse', 'slug' => 'cotonou-main-warehouse', 'description' => 'Main store warehouse located in Cotonou. It handles receiving, storage and order preparation.'],
                ],
            ],
            [
                'code' => 'PN-01',
                'sort_order' => 2,
                'type' => 'storage',
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
                    ['lang_code' => 'fr', 'name' => 'Entrepôt Porto-Novo', 'slug' => 'entrepot-porto-novo', 'description' => 'Entrepôt secondaire desservant la zone est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Porto-Novo Warehouse', 'slug' => 'porto-novo-warehouse', 'description' => 'Secondary warehouse serving the eastern part of Benin.'],
                ],
            ],
            [
                'code' => 'RET-01',
                'sort_order' => 3,
                'type' => 'returns',
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
                    ['lang_code' => 'fr', 'name' => 'Entrepôt des retours', 'slug' => 'entrepot-des-retours', 'description' => 'Entrepôt dédié au contrôle, au reconditionnement et à la remise en stock des retours clients.'],
                    ['lang_code' => 'en', 'name' => 'Returns Warehouse', 'slug' => 'returns-warehouse', 'description' => 'Dedicated warehouse for inspecting, refurbishing and restocking customer returns.'],
                ],
            ],
            [
                'code' => 'TRA-01',
                'sort_order' => 4,
                'type' => 'transit',
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
                    ['lang_code' => 'fr', 'name' => 'Entrepôt de transit', 'slug' => 'entrepot-de-transit', 'description' => 'Entrepôt de transit utilisé pour les réassorts et les expéditions groupées.'],
                    ['lang_code' => 'en', 'name' => 'Transit Warehouse', 'slug' => 'transit-warehouse', 'description' => 'Transit warehouse used for restocking and bulk shipments.'],
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
                'country_id' => $benin?->id,
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
                    'slug' => $translation['slug'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
