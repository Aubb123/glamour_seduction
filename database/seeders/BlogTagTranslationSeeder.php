<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BlogTagTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Les traductions des tags sont créées par BlogTagSeeder via le service
     * BlogTagService::store() (qui centralise la création tag + traductions).
     * Ce seeder est conservé pour la cohérence du pipeline de seending,
     * mais n'a pas de logique propre : aucune table dédiée à peupler en plus.
     */
    public function run(): void
    {
        // Rien à faire : géré par BlogTagSeeder.
    }
}
