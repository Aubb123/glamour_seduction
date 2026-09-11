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
        // Table: product_attribute_values, enregistre les valeurs des attributs de produits
        // Une valeur appartient à un seul attribut (relation one-to-many)
        // Exemple de valeurs : "Rouge", "XL", "Medical Silicone"
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_attributes (une valeur appartient à UN seul attribut)
            //
            $table->string('value'); // Clé machine de la valeur (ex : rouge, xl, medical-silicone), utilisée comme référence technique
            $table->integer('sort_order')->default(0); // Ordre d'affichage de la valeur dans les selects/listes
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si la valeur est utilisable
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['attribute_id', 'value']); // Une même clé machine ne peut pas exister deux fois pour un même attribut
            // Index pour les filtres courants
            $table->index(['attribute_id', 'status', 'sort_order', 'deleted_at'], 'product_attribute_values_filters_index'); // Index pour les filtres courants (nom court explicite : limite MySQL 64 caractères)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};