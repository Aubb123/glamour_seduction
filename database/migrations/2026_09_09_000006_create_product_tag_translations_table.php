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
        Schema::create('product_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('tag_id')->constrained('product_tags')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_tags
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name'); // Nom du tag dans la langue de traduction
            $table->string('slug'); // Slug SEO-friendly
            $table->text('description'); // Description du tag dans la langue de traduction
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['tag_id', 'lang_id']); // Une seule traduction par tag et par langue
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
        Schema::dropIfExists('product_tag_translations');
    }
};
