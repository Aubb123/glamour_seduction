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
        Schema::create('geo_continents', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID pour une identification universelle unique
            //
            $table->string('code', 2)->unique(); // Code abrégé : AF, EU, AS...
            $table->unsignedBigInteger('population'); // Population totale du continent, pas de population négative
            $table->decimal('area_km2', 10, 2)->unsigned(); // La superficie ne peut pas être négative // Tu peux le préciser via unsigned() ou via une validation dans le modèle
            //
            $table->string('file_path'); // Chemin vers une image représentant le continent
            //
            $table->integer('sort_order')->unique(); // Utile dans les selects/listes // Pour l'ordre d'affichage : 0 en premier
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si le continent est actif
            //
            $table->timestamps(); // created_at et updated_at
            $table->softDeletes(); // Crée automatiquement la colonne deleted_at
            // Index pour les filtres courants
            $table->index(['status', 'sort_order', 'deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_continents');
    }
};
