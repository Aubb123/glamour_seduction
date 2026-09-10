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
        Schema::create('product_attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_attributes
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name'); // Nom de l'attribut dans la langue de tradution (ex : Couleur, Taille)
            $table->string('slug'); // Slug SEO-friendly
            $table->text('description'); // Description de l'attribut dans la langue de traduction
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['attribute_id', 'lang_id']); // Une seule traduction par attribut et par langue
            $table->unique(['name', 'lang_id']); // Nom unique par langue ✅
            $table->unique(['slug', 'lang_id']); // Slug unique par langue ✅
            // Index pour les filtres courants
            $table->index(['deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_translations');
    }
};