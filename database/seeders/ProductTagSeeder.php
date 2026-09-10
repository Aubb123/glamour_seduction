<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Lang;
use App\Models\ProductTag;
use App\Models\ProductTagTranslation;
use Illuminate\Database\Seeder;

class ProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nouveau',  'slug' => 'nouveau',  'description' => 'Tag attribué aux derniers produits ajoutés au catalogue.'],
                    ['lang_code' => 'en', 'name' => 'New',      'slug' => 'new-en',   'description' => 'Tag assigned to the latest products added to the catalog.'],
                ],
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Promotion', 'slug' => 'promotion', 'description' => 'Produits actuellement en promotion ou à prix réduit.'],
                    ['lang_code' => 'en', 'name' => 'Sale',       'slug' => 'sale-en',   'description' => 'Products currently on sale or at a reduced price.'],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Best-seller', 'slug' => 'best-seller', 'description' => 'Les produits les plus vendus et plébiscités par nos clients.'],
                    ['lang_code' => 'en', 'name' => 'Best-seller', 'slug' => 'best-seller-en', 'description' => 'The best-selling and most popular products with our customers.'],
                ],
            ],
            [
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Idée cadeau', 'slug' => 'idee-cadeau', 'description' => 'Sélection de produits parfaits à offrir pour toutes les occasions.'],
                    ['lang_code' => 'en', 'name' => 'Gift idea',   'slug' => 'gift-idea-en', 'description' => 'A selection of perfect products to offer for any occasion.'],
                ],
            ],
            [
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Édition limitée', 'slug' => 'edition-limitee', 'description' => 'Produits disponibles en quantité limitée, une fois épuisés ils ne reviendront pas.'],
                    ['lang_code' => 'en', 'name' => 'Limited edition', 'slug' => 'limited-edition-en', 'description' => 'Products available in limited quantity, once sold out they will not return.'],
                ],
            ],
        ];

        foreach ($tags as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer le tag
            $tag = ProductTag::create([
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

                ProductTagTranslation::create([
                    'tag_id' => $tag->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'slug' => $translation['slug'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
