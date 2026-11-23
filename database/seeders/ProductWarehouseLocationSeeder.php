<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationTranslation;
use Illuminate\Database\Seeder;

class ProductWarehouseLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Emplacements hiérarchiques des entrepôts réellement seedés
        // (voir ProductWarehouseSeeder : COT-01, PN-01, RET-01, TRA-01).
        // NB : les parents sont déclarés AVANT leurs enfants dans le tableau.
        $locations = [
            // ── COT-01 (Cotonou, stockage, actif, par défaut) ──
            [
                'warehouse_code' => 'COT-01',
                'parent_code' => null,
                'type' => 'zone',
                'code' => 'COT-01-Z1',
                'sort_order' => 1,
                'status' => 'active',
                'notes' => 'Zone principale de réception et de stockage.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zone A', 'description' => 'Zone principale de réception et de stockage.'],
                    ['lang_code' => 'en', 'name' => 'Zone A', 'description' => 'Main receiving and storage zone.'],
                ],
            ],
            [
                'warehouse_code' => 'COT-01',
                'parent_code' => 'COT-01-Z1',
                'type' => 'aisle',
                'code' => 'COT-01-A1',
                'sort_order' => 2,
                'status' => 'active',
                'notes' => 'Allée centrale de la zone A.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Allée 1', 'description' => 'Allée centrale de la zone A.'],
                    ['lang_code' => 'en', 'name' => 'Aisle 1', 'description' => 'Central aisle of zone A.'],
                ],
            ],
            [
                'warehouse_code' => 'COT-01',
                'parent_code' => 'COT-01-A1',
                'type' => 'shelf',
                'code' => 'COT-01-S1',
                'sort_order' => 3,
                'status' => 'active',
                'notes' => 'Étagère des produits de soin.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Étagère 1', 'description' => 'Étagère des produits de soin.'],
                    ['lang_code' => 'en', 'name' => 'Shelf 1', 'description' => 'Care products shelf.'],
                ],
            ],
            [
                'warehouse_code' => 'COT-01',
                'parent_code' => 'COT-01-S1',
                'type' => 'bin',
                'code' => 'COT-01-B1',
                'sort_order' => 4,
                'status' => 'active',
                'notes' => 'Bac 1 de l\'étagère 1.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bac 1', 'description' => 'Bac 1 de l\'étagère 1.'],
                    ['lang_code' => 'en', 'name' => 'Bin 1', 'description' => 'Bin 1 of shelf 1.'],
                ],
            ],
            // ── PN-01 (Porto-Novo, stockage, actif) ──
            // Le nom « Zone A » est volontairement réutilisé : il est unique par entrepôt, pas globalement.
            [
                'warehouse_code' => 'PN-01',
                'parent_code' => null,
                'type' => 'zone',
                'code' => 'PN-01-Z1',
                'sort_order' => 1,
                'status' => 'active',
                'notes' => 'Zone unique de l\'entrepôt de Porto-Novo.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zone A', 'description' => 'Zone unique de l\'entrepôt de Porto-Novo.'],
                    ['lang_code' => 'en', 'name' => 'Zone A', 'description' => 'Single zone of the Porto-Novo warehouse.'],
                ],
            ],
            [
                'warehouse_code' => 'PN-01',
                'parent_code' => 'PN-01-Z1',
                'type' => 'shelf',
                'code' => 'PN-01-S1',
                'sort_order' => 2,
                'status' => 'active',
                'notes' => 'Étagère de réassort.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Étagère 1', 'description' => 'Étagère de réassort.'],
                    ['lang_code' => 'en', 'name' => 'Shelf 1', 'description' => 'Restocking shelf.'],
                ],
            ],
            [
                'warehouse_code' => 'PN-01',
                'parent_code' => 'PN-01-S1',
                'type' => 'bin',
                'code' => 'PN-01-B1',
                'sort_order' => 3,
                'status' => 'inactive',
                'notes' => 'Bac en cours de réorganisation.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bac 1', 'description' => 'Bac en cours de réorganisation.'],
                    ['lang_code' => 'en', 'name' => 'Bin 1', 'description' => 'Bin being reorganised.'],
                ],
            ],

            // ── RET-01 (retours, actif) ──
            [
                'warehouse_code' => 'RET-01',
                'parent_code' => null,
                'type' => 'zone',
                'code' => 'RET-01-Z1',
                'sort_order' => 1,
                'status' => 'active',
                'notes' => 'Zone de réception des retours clients.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zone retours', 'description' => 'Zone de réception des retours clients.'],
                    ['lang_code' => 'en', 'name' => 'Returns zone', 'description' => 'Customer returns receiving zone.'],
                ],
            ],
            [
                'warehouse_code' => 'RET-01',
                'parent_code' => 'RET-01-Z1',
                'type' => 'shelf',
                'code' => 'RET-01-S1',
                'sort_order' => 2,
                'status' => 'active',
                'notes' => 'Étagère de contrôle des retours.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Étagère contrôle', 'description' => 'Étagère de contrôle des retours.'],
                    ['lang_code' => 'en', 'name' => 'Inspection shelf', 'description' => 'Returns inspection shelf.'],
                ],
            ],

            // ── TRA-01 (transit, inactif) ──
            [
                'warehouse_code' => 'TRA-01',
                'parent_code' => null,
                'type' => 'zone',
                'code' => 'TRA-01-Z1',
                'sort_order' => 1,
                'status' => 'inactive',
                'notes' => 'Zone de transit (entrepôt inactif).',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zone transit', 'description' => 'Zone de transit (entrepôt inactif).'],
                    ['lang_code' => 'en', 'name' => 'Transit zone', 'description' => 'Transit zone (inactive warehouse).'],
                ],
            ],
        ];

        // Emplacements déjà créés, indexés par code (pour résoudre parent_id)
        $created = [];

        foreach ($locations as $data) {

            $warehouse = ProductWarehouse::where('code', $data['warehouse_code'])->first();

            if (! $warehouse) {
                $this->command->error('Entrepôt non trouvé pour le code : '.$data['warehouse_code']."\n");

                continue;
            }

            $parent = $data['parent_code'] !== null ? ($created[$data['parent_code']] ?? null) : null;

            if ($data['parent_code'] !== null && ! $parent) {
                $this->command->error('Emplacement parent non trouvé pour le code : '.$data['parent_code']."\n");

                continue;
            }

            $location = ProductWarehouseLocation::create([
                'warehouse_id' => $warehouse->id,
                'parent_id' => $parent?->id,
                'type' => $data['type'],
                'code' => $data['code'],
                'sort_order' => $data['sort_order'],
                'notes' => $data['notes'],
                'status' => $data['status'],
            ]);

            $created[$data['code']] = $location;

            // Traductions fr / en de l'emplacement
            foreach ($data['translations'] as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                ProductWarehouseLocationTranslation::create([
                    'location_id' => $location->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
