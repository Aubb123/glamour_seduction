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
        Schema::create('geo_continent_translations', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque traduction
            //
            $table->foreignId('continent_id')->constrained('geo_continents')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers continents, avec cascade on update et delete
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name'); // Nom du continent : Afrique, Europe...
            $table->string('slug'); // Slug pour les URLs SEO-friendly
            $table->text('description'); // Description du continent dans la langue de traduction (jamais vide ni NULL)
            //
            $table->timestamps(); // created_at et updated_at
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['continent_id', 'lang_id']);
            $table->unique(['name', 'lang_id']); // Le nom d'un continent doit être unique par langue, au même titre que le slug.
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
        Schema::dropIfExists('geo_continent_translations');
    }
};
