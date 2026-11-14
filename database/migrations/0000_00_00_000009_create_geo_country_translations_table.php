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
        Schema::create('geo_country_translations', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque réponse
            //
            $table->foreignId('country_id')->constrained('geo_countries')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers geo_countries
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name'); // Nom du pays dans la langue de traduction
            $table->string('slug'); // Slug SEO-friendly
            $table->text('description'); // Description optionnelle du pays
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['country_id', 'lang_id']); // Une seule traduction par pays et par langue
            $table->unique(['name', 'lang_id']);
            $table->unique(['slug', 'lang_id']); // Un slug unique par langue
            // Index pour les filtres courants
            $table->index(['deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_country_translations');
    }
};
