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
                    ['lang_code' => 'fr', 'name' => 'Alibori', 'description' => 'Département du nord-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Alibori', 'description' => 'Department in north-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Banikoara', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Banikoara', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Gogounou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Gogounou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kandi', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kandi', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Karimama', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Karimama', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Malanville', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Malanville', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ségbana', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ségbana', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Atacora', 'description' => 'Département du nord-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Atacora', 'description' => 'Department in north-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Boukoumbé', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Boukoumbé', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Cobly', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Cobly', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kérou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kérou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kouandé', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kouandé', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Matéri', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Matéri', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Natitingou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Natitingou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Péhunco', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Péhunco', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Tanguiéta', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Tanguiéta', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Toucountouna', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Toucountouna', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Atlantique', 'description' => 'Département du sud-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Atlantique', 'description' => 'Department in south-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Abomey-Calavi', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Abomey-Calavi', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Allada', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Allada', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kpomassè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kpomassè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouidah', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouidah', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sô-Ava', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sô-Ava', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Toffo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Toffo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Tori-Bossito', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Tori-Bossito', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Zè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Zè', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Borgou', 'description' => 'Département du centre-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Borgou', 'description' => 'Department in central-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bembèrèkè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bembèrèkè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kalalé', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kalalé', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => "N'Dali", 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => "N'Dali", 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Nikki', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Nikki', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Parakou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Parakou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Pèrèrè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Pèrèrè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sinendé', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sinendé', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Tchaourou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Tchaourou', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Collines', 'description' => 'Département du centre du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Collines', 'description' => 'Department in central Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bantè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bantè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Dassa-Zoumè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Dassa-Zoumè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Glazoué', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Glazoué', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouèssè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouèssè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Savalou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Savalou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Savè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Savè', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Couffo', 'description' => 'Département du sud-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Couffo', 'description' => 'Department in south-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Aplahoué', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Aplahoué', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Djakotomey', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Djakotomey', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Dogbo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Dogbo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Klouékanmè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Klouékanmè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Lalo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Lalo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Toviklin', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Toviklin', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Donga', 'description' => 'Département du nord-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Donga', 'description' => 'Department in north-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bassila', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bassila', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Copargo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Copargo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Djougou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Djougou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouaké', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouaké', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Littoral', 'description' => 'Département du littoral du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Littoral', 'description' => 'Department on the coast of Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Cotonou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Cotonou', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mono', 'description' => 'Département du sud-ouest du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Mono', 'description' => 'Department in south-western Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Athiémé', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Athiémé', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bopa', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bopa', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Comè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Comè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Grand-Popo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Grand-Popo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Houéyogbé', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Houéyogbé', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Lokossa', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Lokossa', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Ouémé', 'description' => 'Département du sud-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Ouémé', 'description' => 'Department in south-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Adjarra', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Adjarra', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Adjohoun', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Adjohoun', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Aguégués', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Aguégués', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Akpro-Missérété', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Akpro-Missérété', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Avrankou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Avrankou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bonou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bonou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Dangbo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Dangbo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Porto-Novo', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Porto-Novo', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sèmè-Podji', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sèmè-Podji', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Plateau', 'description' => 'Département du sud-est du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Plateau', 'description' => 'Department in south-eastern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Adja-Ouèrè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Adja-Ouèrè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ifangni', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ifangni', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Kétou', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Kétou', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Pobè', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Pobè', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Sakété', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Sakété', 'description' => 'Commune of Benin.'],
                    ]],
                ],
            ],
            [
                'level' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zou', 'description' => 'Département du centre-sud du Bénin.'],
                    ['lang_code' => 'en', 'name' => 'Zou', 'description' => 'Department in central-southern Benin.'],
                ],
                'communes' => [
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Abomey', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Abomey', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Agbangnizoun', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Agbangnizoun', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Bohicon', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Bohicon', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Cové', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Cové', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Djidja', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Djidja', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Ouinhi', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Ouinhi', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Za-Kpota', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Za-Kpota', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Zagnanado', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Zagnanado', 'description' => 'Commune of Benin.'],
                    ]],
                    ['level' => 2, 'status' => 'active', 'translations' => [
                        ['lang_code' => 'fr', 'name' => 'Zogbodomey', 'description' => 'Commune du Bénin.'],
                        ['lang_code' => 'en', 'name' => 'Zogbodomey', 'description' => 'Commune of Benin.'],
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
                        'description' => $translation['description'],
                    ]);
                }
            }
        }
    }
}
