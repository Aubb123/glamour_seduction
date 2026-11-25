<?php

namespace Database\Seeders;

use App\Models\GeoDivisionType;
use App\Models\GeoDivisionTypeTranslation;
use App\Models\Lang;
use Illuminate\Database\Seeder;

class GeoDivisionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisionTypes = [
            [
                'code' => 'state',
                'level' => 1,
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'État',          'description' => 'Division administrative de premier niveau (ex. États-Unis, Inde).'],
                    ['lang_code' => 'en', 'name' => 'State',         'description' => 'First-level administrative division (e.g. United States, India).'],
                ],
            ],
            [
                'code' => 'region',
                'level' => 1,
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Région',        'description' => 'Premier niveau administratif dans plusieurs pays.'],
                    ['lang_code' => 'en', 'name' => 'Region',        'description' => 'First-level administrative division in several countries.'],
                ],
            ],
            [
                'code' => 'province',
                'level' => 1,
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Province',      'description' => 'Division administrative intermédiaire ou de premier niveau.'],
                    ['lang_code' => 'en', 'name' => 'Province',      'description' => 'Intermediate or first-level administrative division.'],
                ],
            ],
            [
                'code' => 'department',
                'level' => 1,
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Département',   'description' => 'Premier niveau administratif (ex. Bénin, France).'],
                    ['lang_code' => 'en', 'name' => 'Department',    'description' => 'First-level administrative division (e.g. Benin, France).'],
                ],
            ],
            [
                'code' => 'district',
                'level' => 2,
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'District',      'description' => 'Subdivision administrative de niveau intermédiaire.'],
                    ['lang_code' => 'en', 'name' => 'District',      'description' => 'Intermediate-level administrative subdivision.'],
                ],
            ],
            [
                'code' => 'prefecture',
                'level' => 2,
                'sort_order' => 6,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Préfecture',    'description' => 'Circonscription administrative dirigée par un préfet.'],
                    ['lang_code' => 'en', 'name' => 'Prefecture',    'description' => 'Administrative district headed by a prefect.'],
                ],
            ],
            [
                'code' => 'county',
                'level' => 2,
                'sort_order' => 7,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Comté',         'description' => 'Subdivision administrative de niveau intermédiaire.'],
                    ['lang_code' => 'en', 'name' => 'County',        'description' => 'Intermediate-level administrative subdivision.'],
                ],
            ],
            [
                'code' => 'commune',
                'level' => 2,
                'sort_order' => 8,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Commune',       'description' => 'Collectivité territoriale de base (ex. Bénin).'],
                    ['lang_code' => 'en', 'name' => 'Commune',       'description' => 'Basic local administrative unit (e.g. Benin).'],
                ],
            ],
            [
                'code' => 'municipality',
                'level' => 2,
                'sort_order' => 9,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Municipalité',  'description' => 'Entité administrative locale dotée d\'une municipalité.'],
                    ['lang_code' => 'en', 'name' => 'Municipality',  'description' => 'Local administrative entity with a municipal government.'],
                ],
            ],
            [
                'code' => 'arrondissement',
                'level' => 3,
                'sort_order' => 10,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Arrondissement',  'description' => 'Subdivision d\'une commune ou d\'une grande ville (ex. Bénin, France).'],
                    ['lang_code' => 'en', 'name' => 'Arrondissement',  'description' => 'Subdivision of a commune or large city (e.g. Benin, France).'],
                ],
            ],
            [
                'code' => 'city',
                'level' => 3,
                'sort_order' => 11,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Ville',         'description' => 'Agglomération urbaine.'],
                    ['lang_code' => 'en', 'name' => 'City',          'description' => 'Urban agglomeration.'],
                ],
            ],
            [
                'code' => 'ward',
                'level' => 3,
                'sort_order' => 12,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Quartier',      'description' => 'Subdivision urbaine locale.'],
                    ['lang_code' => 'en', 'name' => 'Ward',          'description' => 'Local urban subdivision.'],
                ],
            ],
            [
                'code' => 'sector',
                'level' => 3,
                'sort_order' => 13,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Secteur',       'description' => 'Division administrative locale.'],
                    ['lang_code' => 'en', 'name' => 'Sector',        'description' => 'Local administrative division.'],
                ],
            ],
            [
                'code' => 'village',
                'level' => 4,
                'sort_order' => 14,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Village',       'description' => 'Localité rurale de base.'],
                    ['lang_code' => 'en', 'name' => 'Village',       'description' => 'Basic rural settlement.'],
                ],
            ],
        ];

        foreach ($divisionTypes as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer le type de division géographique
            $divisionType = GeoDivisionType::create([
                'code' => $data['code'],
                'level' => $data['level'],
                'sort_order' => $data['sort_order'],
                'status' => $data['status'],
            ]);

            // Créer les traductions
            foreach ($translations as $translation) {

                // Récupérer l'ID de la langue à partir du code de langue, en s'assurant que la langue est active
                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();
                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue; // Passer à la traduction suivante si la langue n'est pas trouvée
                }

                GeoDivisionTypeTranslation::create([
                    'division_type_id' => $divisionType->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
