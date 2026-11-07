<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Lang;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'type' => 'color',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Couleur', 'description' => 'Couleur du produit (ex : rouge, noir, blanc).'],
                    ['lang_code' => 'en', 'name' => 'Colour',  'description' => 'Product colour (e.g. red, black, white).'],
                ],
                // Les valeurs de cet attribut (avec leur code HEX) sont seedées par ProductAttributeValueSeeder.
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'type' => 'text',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Taille', 'description' => 'Taille du produit (ex : S, M, L, XL).'],
                    ['lang_code' => 'en', 'name' => 'Size',   'description' => 'Product size (e.g. S, M, L, XL).'],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'type' => 'text',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Matière',  'description' => 'Matière de fabrication du produit (ex : silicone médical, cuir, métal).'],
                    ['lang_code' => 'en', 'name' => 'Material', 'description' => 'Product manufacturing material (e.g. medical silicone, leather, metal).'],
                ],
            ],
        ];

        foreach ($attributes as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer l'attribut
            $attribute = ProductAttribute::create([
                'sort_order' => $data['sort_order'],
                'status' => $data['status'],
                'type' => $data['type'],
            ]);

            // Créer les traductions de l'attribut
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                ProductAttributeTranslation::create([
                    'attribute_id' => $attribute->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }

        }
    }
}
