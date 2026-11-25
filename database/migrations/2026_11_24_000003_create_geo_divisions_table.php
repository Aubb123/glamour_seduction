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
        // Table: geo_divisions, divisions administratives rattachées à un pays
        // Exemples : départements, communes, régions, États, provinces, districts...
        // Le niveau (level) est calculé automatiquement par le service (parent.level + 1, ou 1 à la racine).
        Schema::create('geo_divisions', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID pour l'identification universelle et le model binding
            //
            $table->foreignId('country_id')->constrained('geo_countries')->cascadeOnUpdate()->cascadeOnDelete(); // Pays de rattachement
            $table->foreignId('parent_id')->nullable()->constrained('geo_divisions')->cascadeOnUpdate()->cascadeOnDelete(); // Division parente (auto-référencé)
            $table->foreignId('division_type_id')->constrained('geo_division_types')->cascadeOnUpdate()->cascadeOnDelete(); // Type de division
            $table->unsignedTinyInteger('level')->default(1); // Niveau hiérarchique (calculé par le service)
            $table->text('notes')->nullable(); // Notes internes optionnelles
            //
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si la division est active
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['country_id', 'division_type_id', 'parent_id', 'status', 'deleted_at'], 'geo_divisions_filter_index');
            $table->index(['parent_id']);
            $table->index(['division_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_divisions');
    }
};