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
        Schema::create('faq_translations', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque réponse
            //
            $table->foreignId('faq_id')->constrained('faqs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table faqs
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs
            //
            $table->string('question'); // Question de la FAQ dans la langue spécifiée
            $table->string('slug'); // Slug SEO-friendly généré automatiquement
            $table->text('answer'); // Réponse de la FAQ dans la langue spécifiée
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->unique(['faq_id', 'lang_id']); // Assure qu'il n'y a qu'une traduction par FAQ et par langue
            $table->unique(['question', 'lang_id']);
            $table->unique(['slug', 'lang_id']); // Slug unique par langue
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_translations');
    }
};
