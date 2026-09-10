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
        Schema::create('product_attribute_value_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_attribute_values
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('label'); // Libellé affiché de la valeur dans la langue de traduction (ex : Rouge, XL, Silicone médical)
            $table->string('slug'); // Slug SEO-friendly
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['attribute_value_id', 'lang_id'], 'product_attribute_value_translations_attr_value_lang_unique'); // Une seule traduction par valeur et par langue (nom court explicite : limite MySQL 64 caractères)
            $table->unique(['label', 'lang_id']); // Libellé unique par langue ✅
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
        Schema::dropIfExists('product_attribute_value_translations');
    }
};