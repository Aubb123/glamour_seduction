<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rend le code-barres unique lorsqu'il est renseigné.
     *
     * Les valeurs `NULL` restent multiples (comportement standard des index
     * uniques MySQL/SQLite) : une variante sans code-barres est toujours valide.
     * En cas de doublons existants en base, la migration échouera : il faut
     * alors les corriger avant de la relancer.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unique('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
        });
    }
};
