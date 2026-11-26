<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Lang;
use App\Models\ProductBrand;
use App\Models\ProductBrandTranslation;
use Illuminate\Database\Seeder;

class ProductBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Lelo',        'description' => 'Marque premium de produits de bien-être intime, alliant design suédois et technologie de pointe.'],
                    ['lang_code' => 'en', 'name' => 'Lelo',        'description' => 'Premium intimate wellness brand combining Swedish design and cutting-edge technology.'],
                ],
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Satisfyer',  'description' => 'Marque innovante connue pour ses stimulateurs à pression d\'air et ses produits accessibles.'],
                    ['lang_code' => 'en', 'name' => 'Satisfyer',  'description' => 'Innovative brand known for its air-pressure stimulators and accessible products.'],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'We-Vibe',    'description' => 'Spécialiste des jouets connectés et du plaisir à deux, avec une réputation mondiale solide.'],
                    ['lang_code' => 'en', 'name' => 'We-Vibe',    'description' => 'Specialist in connected toys and couple pleasure, with a strong worldwide reputation.'],
                ],
            ],
            [
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Jimmy Jane', 'description' => 'Marque élégante et féminine fondée par une femme, aux lignes douces et raffinées.'],
                    ['lang_code' => 'en', 'name' => 'Jimmy Jane', 'description' => 'Elegant, woman-founded brand with soft and refined lines.'],
                ],
            ],
            [
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Womanizer',  'description' => 'Pionnier de la technologie de plaisir par pression d\'air, plébiscité dans le monde entier.'],
                    ['lang_code' => 'en', 'name' => 'Womanizer',  'description' => 'Pioneer of air-pressure pleasure technology, acclaimed worldwide.'],
                ],
            ],
            [
                'sort_order' => 6,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Fun Factory', 'description' => 'Marque allemande audacieuse aux produits colorés et ludiques, fabriqués à Brême.'],
                    ['lang_code' => 'en', 'name' => 'Fun Factory', 'description' => 'Bold German brand with colorful, playful products manufactured in Bremen.'],
                ],
            ],
        ];

        foreach ($brands as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer la marque
            $brand = ProductBrand::create([
                'sort_order' => $data['sort_order'],
                'status' => $data['status'],
            ]);

            // Créer les traductions
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                ProductBrandTranslation::create([
                    'brand_id' => $brand->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
