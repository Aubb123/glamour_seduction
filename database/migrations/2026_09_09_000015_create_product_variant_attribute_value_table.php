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
        // Table: product_variant_attribute_value, enregistre la relation many-to-many entre les variantes de produits et les valeurs d'attributs
        // Une variante de produit peut avoir plusieurs valeurs d'attributs et une valeur d'attribut peut être associée à plusieurs variantes de produits
        // Exemple de relation : "T-shirt rouge, taille M" (variante) > "Rouge" (valeur d'attribut) et "M" (valeur d'attribut)
        Schema::create('product_variant_attribute_value', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            //
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_variants
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_attribute_values
            //
            $table->timestamps();
            //
            $table->unique(['product_variant_id', 'attribute_value_id'], 'variant_attr_value_unique'); // Évite les doublons (nom d'index demandé par le schéma)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_value');
    }
};