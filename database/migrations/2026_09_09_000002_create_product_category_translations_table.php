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
        // Table: product_category_translations, enregistre les traductions des catégories de produits
        // Une catégorie peut avoir plusieurs traductions, une par langue et une traduction appartient à une seule catégorie
        // Exemple de traduction : "Vêtements" (fr) > "Clothes" (en) > "Ropa" (es)
        Schema::create('product_category_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_categories
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name'); // Nom de la catégorie dans la langue de traduction
            $table->string('slug'); // Slug SEO-friendly
            $table->text('description'); // Description de la catégorie dans la langue de traduction
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['category_id', 'lang_id']); // Une seule traduction par catégorie et par langue
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
        Schema::dropIfExists('product_category_translations');
    }
};
