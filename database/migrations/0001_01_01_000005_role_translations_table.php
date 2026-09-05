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
        Schema::create('role_translations', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque réponse
            //
            $table->foreignId('role_id')->constrained('roles')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table roles
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs
            //
            $table->string('label'); // Étiquette du rôle dans la langue spécifiée
            $table->string('slug'); // Slug SEO-friendly
            $table->text('description'); // Description du rôle dans la langue spécifiée
            //
            $table->timestamps();
            $table->softDeletes();
            // ⚠️ Contrainte unique (role_id, lang_id) et soft deletes :
            // une traduction soft-deletée CONSERVE la contrainte. Tant qu'elle existe en
            // corbeille, il est impossible d'en recréer une pour le même couple
            // (rôle, langue). RoleService::syncTranslations() gère ce cas en restaurant
            // automatiquement la traduction trashed existante avant de la mettre à jour.
            $table->unique(['role_id', 'lang_id']); // Assure qu'il n'y a qu'une traduction par rôle et par langue
            $table->unique(['label', 'lang_id']);
            $table->unique(['slug', 'lang_id']);
            // Index pour les filtres courants
            $table->index(['deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_translations');
    }
};
