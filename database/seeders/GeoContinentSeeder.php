<?php

namespace Database\Seeders;

use App\Models\GeoContinent;
use App\Models\GeoContinentTranslation;
use App\Models\Lang;
use Illuminate\Database\Seeder;

class GeoContinentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $continents = [
            [
                'code' => 'AF',
                'population' => 1460000000,
                'area_km2' => 30370000.00,
                'file_path' => 'others/all/geo/continents/images/af.png',
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Afrique',          'slug' => 'afrique',         'description' => 'Le deuxième plus grand continent du monde.'],
                    ['lang_code' => 'en', 'name' => 'Africa',           'slug' => 'africa',           'description' => 'The second largest continent in the world.'],
                ],
            ],
            [
                'code' => 'AN',
                'population' => 0,
                'area_km2' => 14000000.00,
                'file_path' => 'others/all/geo/continents/images/an.png',
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Antarctique',      'slug' => 'antarctique',      'description' => 'Le continent le plus austral, recouvert de glace.'],
                    ['lang_code' => 'en', 'name' => 'Antarctica',       'slug' => 'antarctica',       'description' => 'The southernmost continent, covered in ice.'],
                ],
            ],
            [
                'code' => 'AS',
                'population' => 4700000000,
                'area_km2' => 44579000.00,
                'file_path' => 'others/all/geo/continents/images/as.png',
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Asie',             'slug' => 'asie',             'description' => 'Le plus grand et le plus peuplé des continents.'],
                    ['lang_code' => 'en', 'name' => 'Asia',             'slug' => 'asia',             'description' => 'The largest and most populous continent.'],
                ],
            ],
            [
                'code' => 'EU',
                'population' => 748000000,
                'area_km2' => 10530000.00,
                'file_path' => 'others/all/geo/continents/images/eu.png',
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Europe',           'slug' => 'europe',           'description' => 'Un continent riche en histoire et en diversité culturelle.'],
                    ['lang_code' => 'en', 'name' => 'Europe',           'slug' => 'europe',           'description' => 'A continent rich in history and cultural diversity.'],
                ],
            ],
            [
                'code' => 'NA',
                'population' => 600000000,
                'area_km2' => 24709000.00,
                'file_path' => 'others/all/geo/continents/images/na.png',
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Amérique du Nord', 'slug' => 'amerique-du-nord', 'description' => 'Le troisième plus grand continent, au nord de l\'Amérique.'],
                    ['lang_code' => 'en', 'name' => 'North America',   'slug' => 'north-america',    'description' => 'The third largest continent, in the northern hemisphere.'],
                ],
            ],
            [
                'code' => 'OC',
                'population' => 45000000,
                'area_km2' => 8526000.00,
                'file_path' => 'others/all/geo/continents/images/oc.png',
                'sort_order' => 6,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Océanie',          'slug' => 'oceanie',          'description' => 'Une région composée d\'îles et de l\'Australie.'],
                    ['lang_code' => 'en', 'name' => 'Oceania',          'slug' => 'oceania',          'description' => 'A region made up of islands and Australia.'],
                ],
            ],
            [
                'code' => 'SA',
                'population' => 440000000,
                'area_km2' => 17840000.00,
                'file_path' => 'others/all/geo/continents/images/sa.png',
                'sort_order' => 7,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Amérique du Sud',  'slug' => 'amerique-du-sud',  'description' => 'Le quatrième plus grand continent, riche en biodiversité.'],
                    ['lang_code' => 'en', 'name' => 'South America',   'slug' => 'south-america',    'description' => 'The fourth largest continent, rich in biodiversity.'],
                ],
            ],
        ];

        foreach ($continents as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer le continent
            $continent = GeoContinent::create([
                'code' => $data['code'],
                'population' => $data['population'],
                'area_km2' => $data['area_km2'],
                'sort_order' => $data['sort_order'],
                'status' => $data['status'],
                'file_path' => $data['file_path'],
            ]);

            // Créer les traductions
            foreach ($translations as $translation) {

                // Récupérer l'ID de la langue à partir du code de langue, en s'assurant que la langue est active
                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();
                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue; // Passer à la traduction suivante si la langue n'est pas trouvée
                }

                GeoContinentTranslation::create([
                    'continent_id' => $continent->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'slug' => $translation['slug'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
