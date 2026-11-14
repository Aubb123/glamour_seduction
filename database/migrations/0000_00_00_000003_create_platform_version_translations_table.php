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
        Schema::create('platform_version_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // UUID unique pour chaque réponse
            //
            $table->foreignId('platform_version_id')->constrained('platform_versions')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table platform_versions
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs
            //
            $table->string('name'); // "Version 1", "Version 2"
            $table->string('slug'); // Slug SEO de la version
            $table->text('description'); // Description de la version
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['platform_version_id', 'lang_id']); // Assure qu'il n'y a qu'une traduction par version de plateforme et par langue
            $table->unique(['name', 'lang_id']); // Le nom d'une version de plateforme doit être unique par langue
            $table->unique(['slug', 'lang_id']); // Le slug doit être unique par langue
            // Index pour les filtres courants
            $table->index(['deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_version_translations');
    }
};
