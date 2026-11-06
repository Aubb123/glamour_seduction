<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Models\Position;
use App\Models\PositionTranslation;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'code' => 'SHOP-MANAGER',
                'context' => 'shop',
                'status' => 'active',
                'sort_order' => 1,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Responsable de boutique', 'description' => 'Gère la boutique et son personnel.'],
                    ['lang_code' => 'en', 'name' => 'Shop Manager', 'description' => 'Manages the shop and its staff.'],
                ],
            ],
            [
                'code' => 'SHOP-SELLER',
                'context' => 'shop',
                'status' => 'active',
                'sort_order' => 2,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Vendeur', 'description' => 'Conseille et sert les clients.'],
                    ['lang_code' => 'en', 'name' => 'Seller', 'description' => 'Advises and serves customers.'],
                ],
            ],
            [
                'code' => 'WH-KEEPER',
                'context' => 'warehouse',
                'status' => 'active',
                'sort_order' => 3,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Magasinier', 'description' => 'Gère les entrées et sorties de stock.'],
                    ['lang_code' => 'en', 'name' => 'Warehouse Keeper', 'description' => 'Manages stock in and out.'],
                ],
            ],
            [
                'code' => 'WH-MANAGER',
                'context' => 'warehouse',
                'status' => 'active',
                'sort_order' => 4,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Chef d\'entrepôt', 'description' => 'Supervise l\'entrepôt et son équipe.'],
                    ['lang_code' => 'en', 'name' => 'Warehouse Manager', 'description' => 'Supervises the warehouse and its team.'],
                ],
            ],
        ];

        foreach ($positions as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            $position = Position::create($data);

            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                PositionTranslation::create([
                    'position_id' => $position->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
