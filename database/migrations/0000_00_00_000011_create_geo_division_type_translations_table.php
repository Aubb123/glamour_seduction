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
        // Table: geo_division_type_translations, traductions des types de division géographique
        // Exemple : department > « Département » (fr) / « Department » (en) ; commune > « Commune » (fr) / « Commune » (en)
        Schema::create('geo_division_type_translations', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque traduction
            //
            $table->foreignId('division_type_id')->constrained('geo_division_types')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers geo_division_types
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name', 100); // Libellé du type dans la langue de traduction (ex : Département)
            $table->text('description'); // Description obligatoire du type
            $table->string('slug'); // Slug SEO-friendly
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['slug', 'lang_id']); // Slug unique par langue
            $table->unique(['division_type_id', 'lang_id']); // Une seule traduction par type et par langue
            // Index pour les filtres courants
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_division_type_translations');
    }
};
