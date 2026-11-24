<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La description des traductions devient obligatoire (entrepôts ET emplacements).
        // On comble d'abord les éventuelles valeurs nulles existantes pour pouvoir
        // retirer le NULL sans échec sur les données déjà en base.
        DB::table('product_warehouse_translations')->whereNull('description')->update(['description' => '']);
        DB::table('product_warehouse_location_translations')->whereNull('description')->update(['description' => '']);

        Schema::table('product_warehouse_translations', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change(); // Description obligatoire
        });

        Schema::table('product_warehouse_location_translations', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change(); // Description obligatoire
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_warehouse_translations', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        Schema::table('product_warehouse_location_translations', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }
};
