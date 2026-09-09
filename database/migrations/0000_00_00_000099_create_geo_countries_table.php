<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('geo_countries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('continent_id')->constrained('geo_continents')->cascadeOnUpdate()->cascadeOnDelete();
            // Nature de l'entité géographique
            $table->enum('type', [
                'country',              // État souverain reconnu (France, Japon...)
                'territory',            // Territoire/dépendance (Guam, Nouvelle-Calédonie...)
                'special_administrative_region', // Hong Kong, Macao
                'disputed',              // Reconnaissance limitée/contestée (Taïwan, Kosovo...)
            ])->default('country');
            // Pays de rattachement (rempli uniquement si type != 'country')
            $table->foreignId('sovereign_id')->nullable()->constrained('geo_countries')->cascadeOnUpdate()->cascadeOnDelete();
            // Code ISO 3166-1 alpha-3 (ex: FRA, USA, BEN) — nullable car certaines entités n'en ont pas (subdivisions GB-ENG...)
            $table->string('code', 3)->nullable()->unique();
            // Code ISO 3166-1 alpha-2 (ex: FR, US, BJ) — étendu aux subdivisions (GB-ENG...) donc longueur 10
            $table->string('code_2', 10)->unique();
            $table->string('phone_code', 10)->nullable(); // certains territoires partagent l'indicatif du pays parent ou n'en ont pas
            $table->string('file_path');
            $table->integer('sort_order')->unique(); // Utile dans les selects/listes // Pour l'ordre d'affichage : 0 en premier
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['continent_id', 'status', 'sort_order', 'deleted_at']);
            $table->index(['type']); // utile pour filtrer rapidement pays vs territoires
            $table->index(['sovereign_id']); // Index pour retrouver les territoires rattachés à un pays

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_countries');
    }
};
