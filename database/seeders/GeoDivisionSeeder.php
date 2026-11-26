<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\GeoCountry;
use App\Models\GeoDivision;
use App\Models\GeoDivisionTranslation;
use App\Models\GeoDivisionType;
use App\Models\Lang;
use Illuminate\Database\Seeder;

class GeoDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupère le Bénin (code ISO alpha-3 « BEN »)
        $benin = GeoCountry::where('code', 'BEN')->first();

        if (! $benin) {
            $this->command->error('Pays Bénin (code BEN) introuvable.');

            return;
        }

        // Récupère les types de division par leur code technique
        $departmentType = GeoDivisionType::where('code', 'department')->first();
        $communeType = GeoDivisionType::where('code', 'commune')->first();

        if (! $departmentType || ! $communeType) {
            $this->command->error('Types de division « department » et/ou « commune » introuvables.');

            return;
        }

        // Structure imbriquée : chaque département porte ses champs, ses traductions + ses communes
        $divisions = [
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Alibori', 'slug' => 'alibori', 'description' => 'Département du nord-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Alibori', 'slug' => 'alibori', 'description' => 'Department in north-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Banikoara', 'slug' => 'banikoara', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Banikoara', 'slug' => 'banikoara', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Gogounou', 'slug' => 'gogounou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Gogounou', 'slug' => 'gogounou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kandi', 'slug' => 'kandi', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kandi', 'slug' => 'kandi', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Karimama', 'slug' => 'karimama', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Karimama', 'slug' => 'karimama', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Malanville', 'slug' => 'malanville', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Malanville', 'slug' => 'malanville', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ségbana', 'slug' => 'segbana', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ségbana', 'slug' => 'segbana', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Atacora', 'slug' => 'atacora', 'description' => 'Département du nord-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Atacora', 'slug' => 'atacora', 'description' => 'Department in north-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Boukoumbé', 'slug' => 'boukoumbe', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Boukoumbé', 'slug' => 'boukoumbe', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Cobly', 'slug' => 'cobly', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Cobly', 'slug' => 'cobly', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kérou', 'slug' => 'kerou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kérou', 'slug' => 'kerou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kouandé', 'slug' => 'kouande', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kouandé', 'slug' => 'kouande', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Matéri', 'slug' => 'materi', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Matéri', 'slug' => 'materi', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Natitingou', 'slug' => 'natitingou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Natitingou', 'slug' => 'natitingou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Péhunco', 'slug' => 'pehunco', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Péhunco', 'slug' => 'pehunco', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Tanguiéta', 'slug' => 'tanguieta', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Tanguiéta', 'slug' => 'tanguieta', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Toucountouna', 'slug' => 'toucountouna', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Toucountouna', 'slug' => 'toucountouna', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Atlantique', 'slug' => 'atlantique', 'description' => 'Département du sud-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Atlantique', 'slug' => 'atlantique', 'description' => 'Department in south-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Abomey-Calavi', 'slug' => 'abomey-calavi', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Abomey-Calavi', 'slug' => 'abomey-calavi', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Allada', 'slug' => 'allada', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Allada', 'slug' => 'allada', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kpomassè', 'slug' => 'kpomasse', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kpomassè', 'slug' => 'kpomasse', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouidah', 'slug' => 'ouidah', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouidah', 'slug' => 'ouidah', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sô-Ava', 'slug' => 'so-ava', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sô-Ava', 'slug' => 'so-ava', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Toffo', 'slug' => 'toffo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Toffo', 'slug' => 'toffo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Tori-Bossito', 'slug' => 'tori-bossito', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Tori-Bossito', 'slug' => 'tori-bossito', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Zè', 'slug' => 'ze', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Zè', 'slug' => 'ze', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Borgou', 'slug' => 'borgou', 'description' => 'Département du centre-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Borgou', 'slug' => 'borgou', 'description' => 'Department in central-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bembèrèkè', 'slug' => 'bembereke', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bembèrèkè', 'slug' => 'bembereke', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kalalé', 'slug' => 'kalale', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kalalé', 'slug' => 'kalale', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => "N'Dali", 'slug' => 'ndali', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => "N'Dali", 'slug' => 'ndali', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Nikki', 'slug' => 'nikki', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Nikki', 'slug' => 'nikki', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Parakou', 'slug' => 'parakou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Parakou', 'slug' => 'parakou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Pèrèrè', 'slug' => 'perere', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Pèrèrè', 'slug' => 'perere', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sinendé', 'slug' => 'sinende', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sinendé', 'slug' => 'sinende', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Tchaourou', 'slug' => 'tchaourou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Tchaourou', 'slug' => 'tchaourou', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Collines', 'slug' => 'collines', 'description' => 'Département du centre du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Collines', 'slug' => 'collines', 'description' => 'Department in central Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bantè', 'slug' => 'bante', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bantè', 'slug' => 'bante', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Dassa-Zoumè', 'slug' => 'dassa-zoume', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Dassa-Zoumè', 'slug' => 'dassa-zoume', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Glazoué', 'slug' => 'glazoue', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Glazoué', 'slug' => 'glazoue', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouèssè', 'slug' => 'ouesse', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouèssè', 'slug' => 'ouesse', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Savalou', 'slug' => 'savalou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Savalou', 'slug' => 'savalou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Savè', 'slug' => 'save', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Savè', 'slug' => 'save', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Couffo', 'slug' => 'couffo', 'description' => 'Département du sud-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Couffo', 'slug' => 'couffo', 'description' => 'Department in south-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Aplahoué', 'slug' => 'aplahoue', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Aplahoué', 'slug' => 'aplahoue', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Djakotomey', 'slug' => 'djakotomey', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Djakotomey', 'slug' => 'djakotomey', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Dogbo', 'slug' => 'dogbo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Dogbo', 'slug' => 'dogbo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Klouékanmè', 'slug' => 'klouekanme', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Klouékanmè', 'slug' => 'klouekanme', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Lalo', 'slug' => 'lalo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Lalo', 'slug' => 'lalo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Toviklin', 'slug' => 'toviklin', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Toviklin', 'slug' => 'toviklin', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Donga', 'slug' => 'donga', 'description' => 'Département du nord-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Donga', 'slug' => 'donga', 'description' => 'Department in north-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bassila', 'slug' => 'bassila', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bassila', 'slug' => 'bassila', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Copargo', 'slug' => 'copargo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Copargo', 'slug' => 'copargo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Djougou', 'slug' => 'djougou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Djougou', 'slug' => 'djougou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouaké', 'slug' => 'ouake', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouaké', 'slug' => 'ouake', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Littoral', 'slug' => 'littoral', 'description' => 'Département du littoral du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Littoral', 'slug' => 'littoral', 'description' => 'Department on the coast of Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Cotonou', 'slug' => 'cotonou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Cotonou', 'slug' => 'cotonou', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mono', 'slug' => 'mono', 'description' => 'Département du sud-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Mono', 'slug' => 'mono', 'description' => 'Department in south-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Athiémé', 'slug' => 'athieme', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Athiémé', 'slug' => 'athieme', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bopa', 'slug' => 'bopa', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bopa', 'slug' => 'bopa', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Comè', 'slug' => 'come', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Comè', 'slug' => 'come', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Grand-Popo', 'slug' => 'grand-popo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Grand-Popo', 'slug' => 'grand-popo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Houéyogbé', 'slug' => 'houeyogbe', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Houéyogbé', 'slug' => 'houeyogbe', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Lokossa', 'slug' => 'lokossa', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Lokossa', 'slug' => 'lokossa', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Ouémé', 'slug' => 'oueme', 'description' => 'Département du sud-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Ouémé', 'slug' => 'oueme', 'description' => 'Department in south-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Adjarra', 'slug' => 'adjarra', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Adjarra', 'slug' => 'adjarra', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Adjohoun', 'slug' => 'adjohoun', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Adjohoun', 'slug' => 'adjohoun', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Aguégués', 'slug' => 'aguegues', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Aguégués', 'slug' => 'aguegues', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Akpro-Missérété', 'slug' => 'akpro-misserete', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Akpro-Missérété', 'slug' => 'akpro-misserete', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Avrankou', 'slug' => 'avrankou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Avrankou', 'slug' => 'avrankou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bonou', 'slug' => 'bonou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bonou', 'slug' => 'bonou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Dangbo', 'slug' => 'dangbo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Dangbo', 'slug' => 'dangbo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Porto-Novo', 'slug' => 'porto-novo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Porto-Novo', 'slug' => 'porto-novo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sèmè-Podji', 'slug' => 'seme-podji', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sèmè-Podji', 'slug' => 'seme-podji', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Plateau', 'slug' => 'plateau', 'description' => 'Département du sud-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Plateau', 'slug' => 'plateau', 'description' => 'Department in south-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Adja-Ouèrè', 'slug' => 'adja-ouere', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Adja-Ouèrè', 'slug' => 'adja-ouere', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ifangni', 'slug' => 'ifangni', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ifangni', 'slug' => 'ifangni', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kétou', 'slug' => 'ketou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kétou', 'slug' => 'ketou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Pobè', 'slug' => 'pobe', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Pobè', 'slug' => 'pobe', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sakété', 'slug' => 'sakete', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sakété', 'slug' => 'sakete', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zou', 'slug' => 'zou', 'description' => 'Département du centre-sud du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Zou', 'slug' => 'zou', 'description' => 'Department in central-southern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Abomey', 'slug' => 'abomey', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Abomey', 'slug' => 'abomey', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Agbangnizoun', 'slug' => 'agbangnizoun', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Agbangnizoun', 'slug' => 'agbangnizoun', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bohicon', 'slug' => 'bohicon', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bohicon', 'slug' => 'bohicon', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Cové', 'slug' => 'cove', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Cové', 'slug' => 'cove', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Djidja', 'slug' => 'djidja', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Djidja', 'slug' => 'djidja', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouinhi', 'slug' => 'ouinhi', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouinhi', 'slug' => 'ouinhi', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Za-Kpota', 'slug' => 'za-kpota', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Za-Kpota', 'slug' => 'za-kpota', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Zagnanado', 'slug' => 'zagnanado', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Zagnanado', 'slug' => 'zagnanado', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Zogbodomey', 'slug' => 'zogbodomey', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Zogbodomey', 'slug' => 'zogbodomey', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
        ];

        foreach ($divisions as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            $communes = $data['communes'];
            unset($data['communes']);

            // Créer le département (niveau 1, racine)
            $department = GeoDivision::create([
                'country_id' => $benin->id,
                'parent_id' => null,
                'division_type_id' => $departmentType->id,
                'level' => $data['level'],
                'status' => $data['status'],
            ]);

            // Créer les traductions du département
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                GeoDivisionTranslation::create([
                    'division_id' => $department->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'slug' => $translation['slug'],
                    'description' => $translation['description'],
                ]);
            }

            // Créer les communes rattachées (niveau 2)
            foreach ($communes as $commune) {

                $communeTranslations = $commune['translations'];
                unset($commune['translations']);

                $division = GeoDivision::create([
                    'country_id' => $benin->id,
                    'parent_id' => $department->id,
                    'division_type_id' => $communeType->id,
                    'level' => $commune['level'],
                    'status' => $commune['status'],
                ]);

                // Créer les traductions de la commune
                foreach ($communeTranslations as $translation) {

                    $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                    if (! $lang) {
                        $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                        continue;
                    }

                    GeoDivisionTranslation::create([
                        'division_id' => $division->id,
                        'lang_id' => $lang->id,
                        'name' => $translation['name'],
                        'slug' => $translation['slug'],
                        'description' => $translation['description'],
                    ]);
                }
            }
        }
    }
}
