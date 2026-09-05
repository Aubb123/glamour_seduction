<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\Lang;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Technologie',      'slug' => 'technologie',      'description' => 'Articles sur les dernières innovations technologiques et le numérique.'],
                    ['lang_code' => 'en', 'name' => 'Technology',        'slug' => 'technology',       'description' => 'Articles about the latest technological innovations and digital trends.'],
                ],
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Santé & Bien-être', 'slug' => 'sante-bien-etre',  'description' => 'Conseils et actualités sur la santé, le sport et le bien-être au quotidien.'],
                    ['lang_code' => 'en', 'name' => 'Health & Wellness', 'slug' => 'health-wellness',  'description' => 'Tips and news on health, sports and everyday well-being.'],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Voyage',            'slug' => 'voyage',            'description' => 'Découvrez des destinations, des conseils de voyage et des récits d\'aventure.'],
                    ['lang_code' => 'en', 'name' => 'Travel',            'slug' => 'travel',            'description' => 'Discover destinations, travel tips and adventure stories from around the world.'],
                ],
            ],
            [
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Éducation',         'slug' => 'education',         'description' => 'Ressources, guides et actualités sur l\'éducation et la formation professionnelle.'],
                    ['lang_code' => 'en', 'name' => 'Education',         'slug' => 'education',         'description' => 'Resources, guides and news on education and professional training.'],
                ],
            ],
            [
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Business',          'slug' => 'business',          'description' => 'Actualités économiques, conseils entrepreneuriaux et stratégies de croissance.'],
                    ['lang_code' => 'en', 'name' => 'Business',          'slug' => 'business',          'description' => 'Economic news, entrepreneurial advice and growth strategies for professionals.'],
                ],
            ],
        ];

        foreach ($categories as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer la catégorie
            $category = BlogCategory::create([
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

                BlogCategoryTranslation::create([
                    'category_id' => $category->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'slug' => $translation['slug'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
