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
        Schema::create('setting_translations', function (Blueprint $table) {
            $table->id(); // ID auto-incrementé
            $table->uuid('uuid')->unique(); // UUID unique pour chaque réponse
            //
            $table->foreignId('setting_id')->constrained('settings')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table settings
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name')->nullable(); // Nom du paramètre dans la langue spécifiée (ex: "Site Title" pour en, "Titre du site" pour fr, etc.)
            $table->text('value')->nullable(); // Valeur du paramètre dans la langue spécifiée (ex: "My Awesome Site" pour en, "Mon site génial" pour fr, etc.)
            $table->text('description')->nullable(); // Description du paramètre dans la langue spécifiée
            //
            $table->timestamps();
            $table->softDeletes();
            // Unique constraints
            $table->unique(['setting_id', 'lang_id']); // Assure qu'il n'y a qu'une traduction par paramètre et par langue
            $table->unique(['name', 'lang_id']); // Le nom d'un paramètre doit être unique par langue
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_translations');
    }
};
