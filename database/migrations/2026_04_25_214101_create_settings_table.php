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
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); // ID auto-incrementé
            $table->uuid('uuid')->unique(); // UUID unique pour chaque paramètre
            //
            $table->string('group')->default('general'); // Groupe de paramètres (Ex: generale, sécurité, etc.)
            $table->string('key')->unique(); // Clé unique pour chaque paramètre
            $table->text('value')->nullable(); // Valeur du paramètre (stockée en texte, peut être castée selon le type) Ex: "contact@site.com", "5000", "true"
            $table->string('type')->default('string'); // Type de données (string, integer, boolean, etc.)
            $table->boolean('is_public')->default(false); // Indique si le paramètre est accessible publiquement
            $table->boolean('is_translatable')->default(false); // Indique si le paramètre est traduisible
            $table->boolean('is_editable')->default(true); // Indique si le paramètre est modifiable
            //
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
