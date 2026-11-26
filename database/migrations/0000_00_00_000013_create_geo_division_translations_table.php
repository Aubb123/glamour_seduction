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
        // Table: geo_division_translations, traductions des divisions géographiques
        // Exemple : department > « Département » (fr) / « Department » (en) ; commune > « Commune » (fr) / « Commune » (en)
        // Le slug est généré automatiquement par le trait HasSlug (slug unique par langue).
        Schema::create('geo_division_translations', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque traduction
            //
            $table->foreignId('division_id')->constrained('geo_divisions')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers geo_divisions
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name'); // Nom de la division dans la langue de traduction (ex : Département)
            $table->string('slug'); // Slug SEO-friendly généré automatiquement
            $table->text('description'); // Description de la division (obligatoire)
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['slug', 'lang_id']); // Slug unique par langue
            $table->unique(['division_id', 'lang_id']); // Une seule traduction par division et par langue
            // Index pour les filtres courants
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_division_translations');
    }
};
