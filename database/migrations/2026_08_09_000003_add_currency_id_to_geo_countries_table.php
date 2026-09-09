<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalisation des devises sur geo_countries :
     *  - ajoute une FK optionnelle `currency_id` vers `currencies` ;
     *  - backfill des pays existants depuis l'ancien `currency_code` dénormalisé ;
     *  - supprime les anciennes colonnes dénormalisées (currency_code, currency_symbol
     *    sur geo_countries et currency_name sur geo_country_translations).
     */
    public function up(): void
    {
        Schema::table('geo_countries', function (Blueprint $table) {
            if (! Schema::hasColumn('geo_countries', 'currency_id')) {
                $table->foreignId('currency_id')
                    ->nullable()
                    ->after('continent_id')
                    ->constrained('currencies')
                    ->cascadeOnUpdate()
                    ->nullOnDelete(); // Une devise supprimée ne bloque pas la suppression du pays
            }
        });

        // Backfill : relie les pays existants à la devise correspondante (code ISO 4217 alpha-3)
        if (Schema::hasColumn('geo_countries', 'currency_code') && Schema::hasTable('currencies')) {
            $codes = DB::table('geo_countries')
                ->select('currency_code', 'currency_symbol')
                ->whereNotNull('currency_code')
                ->distinct()
                ->get();

            foreach ($codes as $item) {
                $currency = DB::table('currencies')
                    ->where('code', $item->currency_code)
                    ->first();

                if (! $currency) {
                    // Crée la devise minimale si elle n'existe pas encore (le CurrencySeeder l'enrichira)
                    $currencyId = DB::table('currencies')->insertGetId([
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'code' => $item->currency_code,
                        'symbol' => $item->currency_symbol ?? $item->currency_code,
                        'decimal_places' => 2,
                        'sort_order' => DB::table('currencies')->count() + 1,
                        'is_default' => false,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $currencyId = $currency->id;
                }

                DB::table('geo_countries')
                    ->where('currency_code', $item->currency_code)
                    ->update(['currency_id' => $currencyId]);
            }
        }

        // Suppression des anciennes colonnes dénormalisées (protégé pour les bases fraîches)
        Schema::table('geo_countries', function (Blueprint $table) {
            if (Schema::hasColumn('geo_countries', 'currency_code')) {
                $table->dropColumn('currency_code');
            }
            if (Schema::hasColumn('geo_countries', 'currency_symbol')) {
                $table->dropColumn('currency_symbol');
            }
        });

        Schema::table('geo_country_translations', function (Blueprint $table) {
            if (Schema::hasColumn('geo_country_translations', 'currency_name')) {
                $table->dropColumn('currency_name');
            }
        });
    }

    /**
     * Reverse the migrations : restaure les anciennes colonnes dénormalisées.
     */
    public function down(): void
    {
        Schema::table('geo_countries', function (Blueprint $table) {
            if (! Schema::hasColumn('geo_countries', 'currency_code')) {
                $table->string('currency_code', 3)->nullable()->after('phone_code');
            }
            if (! Schema::hasColumn('geo_countries', 'currency_symbol')) {
                $table->string('currency_symbol', 10)->nullable()->after('currency_code');
            }
        });

        Schema::table('geo_country_translations', function (Blueprint $table) {
            if (! Schema::hasColumn('geo_country_translations', 'currency_name')) {
                $table->string('currency_name', 100)->nullable()->after('slug');
            }
        });

        Schema::table('geo_countries', function (Blueprint $table) {
            if (Schema::hasColumn('geo_countries', 'currency_id')) {
                $table->dropConstrainedForeignId('currency_id');
            }
        });
    }
};