<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Services\V1\Controllers\BlogTagService;
use Illuminate\Database\Seeder;

class BlogTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Récupère les données des tags (avec leurs traductions) et les crée via
     * le service BlogTagService::store(), afin de centraliser la logique de
     * création (tag + traductions) au même endroit que le code métier réel.
     */
    public function run(): void
    {
        $service = app(BlogTagService::class);

        $tags = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Intelligence Artificielle'],
                    ['lang_code' => 'en', 'name' => 'Artificial Intelligence'],
                ],
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Développement Web'],
                    ['lang_code' => 'en', 'name' => 'Web Development'],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Entrepreneuriat'],
                    ['lang_code' => 'en', 'name' => 'Entrepreneurship'],
                ],
            ],
            [
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Développement Personnel'],
                    ['lang_code' => 'en', 'name' => 'Personal Development'],
                ],
            ],
            [
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Environnement'],
                    ['lang_code' => 'en', 'name' => 'Environment'],
                ],
            ],
        ];

        foreach ($tags as $data) {
            $service->store($this->prepareData($data));
        }
    }

    /**
     * Résout les lang_code en lang_id pour le service.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareData(array $data): array
    {
        $translations = [];

        foreach ($data['translations'] as $translation) {
            $lang = Lang::where('code', $translation['lang_code'])
                ->where('is_active', true)
                ->first();

            if (! $lang) {
                $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                continue;
            }

            $translations[] = [
                'lang_id' => $lang->id,
                'name' => $translation['name'],
            ];
        }

        return [
            'sort_order' => $data['sort_order'],
            'status' => $data['status'],
            'translations' => $translations,
        ];
    }
}
