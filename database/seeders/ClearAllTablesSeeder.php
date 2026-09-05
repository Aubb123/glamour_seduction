<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearAllTablesSeeder extends Seeder
{
    public function run(): void
    {
        // Désactive les contraintes de clés étrangères pour éviter les erreurs lors du truncate
        Schema::disableForeignKeyConstraints();

        // Récupère tous les noms de tables de la base de données
        $tables = DB::select('SHOW TABLES');

        // Parcourt chaque table et la vide (sauf la table des migrations)
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];

            if ($tableName === 'migrations') {
                continue;
            }

            DB::table($tableName)->truncate();
            $this->command->info("Table '{$tableName}' vidée ✅");
        }

        // Réactive les contraintes de clés étrangères après le truncate
        Schema::enableForeignKeyConstraints();

        // ✅ Réinitialise le boot de tous les modèles Eloquent
        // pour que les traits soient bien rechargés après le truncate
        DB::reconnect();

        $this->command->info('Toutes les tables ont été vidées ! 🎉');
    }
}
