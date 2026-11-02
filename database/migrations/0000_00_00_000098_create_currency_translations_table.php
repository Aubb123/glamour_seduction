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
        Schema::create('currency_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers currencies
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name', 100); // Nom de la devise dans la langue de traduction (Euro, Dollar, Franc CFA...)
            $table->string('slug'); // Slug SEO-friendly
            $table->string('description')->nullable(); // Description optionnelle de la devise
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['currency_id', 'lang_id']); // Une seule traduction par devise et par langue
            $table->unique(['slug', 'lang_id']); // Un slug unique par langue
            $table->unique(['name', 'lang_id']); // Un nom unique par langue
            // Index pour les filtres courants
            $table->index(['deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_translations');
    }
};
