<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Gallery;
use App\Models\GalleryTranslation;
use App\Models\Lang;
use App\Models\User;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'title' => 'Paysages de montagne',    'description' => 'Une collection de photos majestueuses des plus beaux sommets du monde.'],
                    ['lang_code' => 'en', 'title' => 'Mountain Landscapes',     'description' => 'A collection of majestic photos of the most beautiful mountain peaks in the world.'],
                ],
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'title' => 'Faune sauvage d\'Afrique', 'description' => 'Découvrez la richesse de la faune africaine à travers cette galerie captivante.'],
                    ['lang_code' => 'en', 'title' => 'African Wildlife',         'description' => 'Discover the richness of African wildlife through this captivating gallery.'],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'title' => 'Architecture urbaine',    'description' => 'Les plus belles constructions modernes et historiques des grandes villes.'],
                    ['lang_code' => 'en', 'title' => 'Urban Architecture',      'description' => 'The most beautiful modern and historical buildings of major cities.'],
                ],
            ],
            [
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'title' => 'Océans et plages',        'description' => 'Des eaux turquoise et des plages paradisiaques à couper le souffle.'],
                    ['lang_code' => 'en', 'title' => 'Oceans and Beaches',      'description' => 'Turquoise waters and breathtaking paradise beaches from around the world.'],
                ],
            ],
            [
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'title' => 'Cultures du monde',       'description' => 'Un voyage visuel à travers les traditions et cultures des peuples du monde.'],
                    ['lang_code' => 'en', 'title' => 'Cultures of the World',   'description' => 'A visual journey through the traditions and cultures of the world\'s peoples.'],
                ],
            ],
        ];

        // Récupérer le premier user existant (ou un user spécifique)
        $user = User::first();

        if (! $user) {
            $this->command->error("Aucun utilisateur trouvé. Veuillez d'abord créer un utilisateur.\n");

            return;
        }

        foreach ($galleries as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer la galerie
            $gallery = Gallery::create([
                'user_id' => $user->id,
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

                GalleryTranslation::create([
                    'gallery_id' => $gallery->id,
                    'lang_id' => $lang->id,
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
