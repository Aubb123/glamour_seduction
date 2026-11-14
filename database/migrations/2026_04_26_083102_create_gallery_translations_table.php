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
        Schema::create('gallery_translations', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque réponse
            //
            $table->foreignId('gallery_id')->constrained('galleries')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table galleries
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs
            //
            $table->string('title'); // Titre de la galerie dans la langue spécifiée
            $table->string('slug'); // Slug SEO-friendly généré automatiquement
            $table->text('description'); // Description de la galerie dans la langue spécifiée
            //
            $table->timestamps();
            $table->softDeletes();
            // Unique constraints
            $table->unique(['gallery_id', 'lang_id']); // Assure qu'il n'y a qu'une traduction par galerie et par langue
            $table->unique(['title', 'lang_id']);
            $table->unique(['slug', 'lang_id']); // Slug unique par langue
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_translations');
    }
};
