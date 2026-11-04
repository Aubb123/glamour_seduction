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
        // Table: product_warehouses, enregistre les entrepôts de produits
        // Un entrepôt peut contenir plusieurs produits (relation one-to-many)
        // Exemple d'entrepôts : "Entrepôt de Paris", "Entrepôt de Lyon", "Entrepôt de Marseille"
        Schema::create('product_warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->string('code')->unique(); // Code unique de l'entrepôt (ex : COT-01, PKO-01)
            $table->string('name'); // Nom de l'entrepôt (donnée de gestion interne, non traduite)
            //
            $table->string('address')->nullable(); // Adresse physique de l'entrepôt
            $table->string('city')->nullable(); // Ville de l'entrepôt
            $table->string('phone')->nullable(); // Numéro de téléphone de l'entrepôt
            //
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si l'entrepôt est utilisable
            $table->boolean('is_default')->default(false); // Entrepôt de réservation par défaut
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouses');
    }
};
