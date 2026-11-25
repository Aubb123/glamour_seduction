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
        // Table: geo_division_types, référentiel des types de division géographique
        // Exemples : state (État), region (région), province, department (département),
        // district, prefecture, county, commune, arrondissement, municipality, city, ward, sector, village.
        // Le libellé affiché vient des traductions (geo_division_type_translations), jamais d'un enum figé :
        // la liste des types peut donc s'élargir sans migration.
        Schema::create('geo_division_types', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID pour l'identification universelle et le model binding
            //
            $table->string('code', 50)->unique(); // Clé technique stable utilisée dans le code (ex : department, commune)
            $table->unsignedTinyInteger('level')->default(1); // Niveau par défaut suggéré (1 = plus haut niveau du pays)
            $table->unsignedInteger('sort_order')->default(0); // Ordre d'affichage du référentiel : 0 en premier (non unique)
            //
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si le type est utilisable
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'level', 'sort_order', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_division_types');
    }
};
