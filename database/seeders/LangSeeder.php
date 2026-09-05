<?php

namespace Database\Seeders;

use App\Models\Lang;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $langs = [
            [
                'code' => 'fr',
                'name' => 'Français',
                'native' => 'Français',
                'direction' => 'ltr',
                'file_path' => 'others/backend/assets/svg/langs/french.svg',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English',
                'direction' => 'ltr',
                'file_path' => 'others/backend/assets/svg/langs/english.svg',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($langs as $lang) {
            Lang::firstOrCreate(
                ['code' => $lang['code']], // Condition pour éviter les doublons (Ex: 'fr' existe déjà)
                $lang // Données à insérer si la langue n'existe pas déjà
            );
        }
    }
}
