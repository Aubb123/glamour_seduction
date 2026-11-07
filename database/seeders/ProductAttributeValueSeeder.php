<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use Illuminate\Database\Seeder;

class ProductAttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Le code HEX n'est renseigné que pour les valeurs de l'attribut « Couleur » (type = color).
        $valuesByAttribute = [
            'Couleur' => [
                ['value' => 'rouge', 'hex_color' => '#FF0000', 'sort_order' => 1, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'Rouge'], ['lang_code' => 'en', 'label' => 'Red']]],
                ['value' => 'noir',  'hex_color' => '#000000', 'sort_order' => 2, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'Noir'],  ['lang_code' => 'en', 'label' => 'Black']]],
                ['value' => 'blanc', 'hex_color' => '#FFFFFF', 'sort_order' => 3, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'Blanc'], ['lang_code' => 'en', 'label' => 'White']]],
            ],
            'Taille' => [
                ['value' => 's',  'hex_color' => null, 'sort_order' => 1, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'S'],  ['lang_code' => 'en', 'label' => 'S']]],
                ['value' => 'm',  'hex_color' => null, 'sort_order' => 2, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'M'],  ['lang_code' => 'en', 'label' => 'M']]],
                ['value' => 'xl', 'hex_color' => null, 'sort_order' => 3, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'XL'], ['lang_code' => 'en', 'label' => 'XL']]],
            ],
            'Matière' => [
                ['value' => 'silicone-medical', 'hex_color' => null, 'sort_order' => 1, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'Silicone médical'], ['lang_code' => 'en', 'label' => 'Medical silicone']]],
                ['value' => 'cuir',             'hex_color' => null, 'sort_order' => 2, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'Cuir'],             ['lang_code' => 'en', 'label' => 'Leather']]],
                ['value' => 'metal',            'hex_color' => null, 'sort_order' => 3, 'status' => 'active', 'translations' => [['lang_code' => 'fr', 'label' => 'Métal'],            ['lang_code' => 'en', 'label' => 'Metal']]],
            ],
        ];

        foreach ($valuesByAttribute as $attributeName => $values) {

            $attribute = ProductAttribute::whereHas('translations', function ($q) use ($attributeName) {
                $q->where('name', $attributeName);
            })->first();

            if (! $attribute) {
                $this->command->error("Attribut non trouvé pour le nom : {$attributeName}");

                continue;
            }

            foreach ($values as $data) {

                $translations = $data['translations'];
                unset($data['translations']);

                $value = ProductAttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => $data['value'],
                    'hex_color' => $data['hex_color'],
                    'sort_order' => $data['sort_order'],
                    'status' => $data['status'],
                ]);

                foreach ($translations as $translation) {

                    $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                    if (! $lang) {
                        $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']);

                        continue;
                    }

                    ProductAttributeValueTranslation::create([
                        'attribute_value_id' => $value->id,
                        'lang_id' => $lang->id,
                        'label' => $translation['label'],
                    ]);
                }
            }
        }
    }
}
