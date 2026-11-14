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
        Schema::create('langs', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID pour une identification universelle unique
            //
            $table->string('code', 5)->unique();  // fr, en, ar, es...
            $table->string('name')->unique();     // Français, English, العربية...
            $table->string('native')->unique();   // Français, English, العربية (nom dans sa propre langue)
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr'); // LTR ou RTL (left-to-right ou right-to-left) Direction du texte
            $table->string('file_path');   // 🇫🇷 ou chemin vers image
            $table->boolean('is_default')->default(false); // Indique si c'est la langue par défaut
            $table->boolean('is_active')->default(true); // Indique si la langue est active
            $table->integer('sort_order')->unique(); // Utile dans les selects/listes // Pour l'ordre d'affichage : 0 en premier
            //
            $table->timestamps();
            $table->softDeletes(); // Crée automatiquement la colonne deleted_at
            // Index pour les filtres courants
            $table->index(['is_active', 'is_default', 'sort_order', 'deleted_at']); // Index pour les filtres courants

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langs');
    }
};
