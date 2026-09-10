<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Lang;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Arborescence de catégories de produits : racines avec sous-catégories
        $categories = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Lingerie',     'slug' => 'lingerie',     'description' => 'Lingerie fine, confortable et élégante pour toutes les occasions.'],
                    ['lang_code' => 'en', 'name' => 'Lingerie',     'slug' => 'lingerie-en',  'description' => 'Fine, comfortable and elegant lingerie for all occasions.'],
                ],
                'children' => [
                    [
                        'sort_order' => 11,
                        'status' => 'active',
                        'translations' => [
                            ['lang_code' => 'fr', 'name' => 'Lingerie de jour',   'slug' => 'lingerie-de-jour',   'description' => 'Lingerie quotidienne alliant confort et discrétion.'],
                            ['lang_code' => 'en', 'name' => 'Everyday Lingerie',  'slug' => 'everyday-lingerie',  'description' => 'Everyday lingerie combining comfort and discretion.'],
                        ],
                    ],
                    [
                        'sort_order' => 12,
                        'status' => 'active',
                        'translations' => [
                            ['lang_code' => 'fr', 'name' => 'Lingerie de nuit', 'slug' => 'lingerie-de-nuit', 'description' => 'Nuisettes, pyjamas et peignoirs pour des nuits douces.'],
                            ['lang_code' => 'en', 'name' => 'Nightwear',        'slug' => 'nightwear',        'description' => 'Nightdresses, pyjamas and robes for gentle nights.'],
                        ],
                    ],
                ],
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Accessoires',  'slug' => 'accessoires',  'description' => 'Accessoires de charme et de séduction pour sublimer chaque instant.'],
                    ['lang_code' => 'en', 'name' => 'Accessories',  'slug' => 'accessories',  'description' => 'Charm and seduction accessories to enhance every moment.'],
                ],
                'children' => [
                    [
                        'sort_order' => 21,
                        'status' => 'active',
                        'translations' => [
                            ['lang_code' => 'fr', 'name' => 'Collants & Bas', 'slug' => 'collants-bas', 'description' => 'Collants, bas et mitaines pour un look raffiné.'],
                            ['lang_code' => 'en', 'name' => 'Tights & Stockings', 'slug' => 'tights-stockings', 'description' => 'Tights, stockings and gloves for a refined look.'],
                        ],
                    ],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bien-être & Beauté', 'slug' => 'bien-etre-beaute', 'description' => 'Produits de soin et de bien-être pour prendre soin de soi.'],
                    ['lang_code' => 'en', 'name' => 'Wellness & Beauty',  'slug' => 'wellness-beauty',  'description' => 'Care and wellness products to take care of yourself.'],
                ],
                'children' => null,
            ],
        ];

        foreach ($categories as $data) {
            $this->createCategory($data);
        }
    }

    /**
     * Crée une catégorie (récursivement avec ses sous-catégories) et ses traductions.
     */
    private function createCategory(array $data, ?int $parentId = null): ProductCategory
    {
        $children = $data['children'] ?? null;
        unset($data['children']);

        $category = ProductCategory::create([
            'parent_id' => $parentId,
            'sort_order' => $data['sort_order'],
            'status' => $data['status'],
        ]);

        foreach ($data['translations'] as $translation) {

            $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

            if (! $lang) {
                $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                continue;
            }

            ProductCategoryTranslation::create([
                'category_id' => $category->id,
                'lang_id' => $lang->id,
                'name' => $translation['name'],
                'slug' => $translation['slug'],
                'description' => $translation['description'],
            ]);
        }

        if (is_array($children)) {
            foreach ($children as $child) {
                $this->createCategory($child, $category->id);
            }
        }

        return $category;
    }
}
