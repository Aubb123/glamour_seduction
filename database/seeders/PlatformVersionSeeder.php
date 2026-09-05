<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use Illuminate\Database\Seeder;

class PlatformVersionSeeder extends Seeder
{
    public function run(): void
    {
        $versions = [

            // Version N°1
            [
                'code' => 'v1',
                'status' => 'active',
                'is_published' => true,
                'is_default' => true,
                'released_at' => getDateTime(),
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Version 1', 'description' => 'Première version de la plateforme.'],
                    ['lang_code' => 'en', 'name' => 'Version 1', 'description' => 'First version of the platform.'],
                ],
            ],

            // Version N°2
            [
                'code' => 'v2',
                'status' => 'inactive',
                'is_published' => false,
                'is_default' => false,
                'released_at' => null,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Version 2', 'description' => 'Deuxième version de la plateforme, en cours de développement.'],
                    ['lang_code' => 'en', 'name' => 'Version 2', 'description' => 'Second version of the platform, currently in development.'],
                ],
            ],

        ];

        foreach ($versions as $index => $data) {

            $version = PlatformVersion::create([
                'code' => $data['code'],
                'status' => $data['status'],
                'is_published' => $data['is_published'],
                'is_default' => $data['is_default'] ?? false,
                'released_at' => $data['released_at'],
            ]);

            foreach ($data['translations'] as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->first();

                if (! $lang) {
                    $this->command->error("❌ Langue '{$translation['lang_code']}' non trouvée pour la version N°".($index + 1).'. Traduction ignorée.');

                    continue;
                }

                PlatformVersionTranslation::create([
                    'platform_version_id' => $version->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
