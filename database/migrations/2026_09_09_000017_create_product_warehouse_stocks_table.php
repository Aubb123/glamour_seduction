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
        // Table: product_warehouse_stocks, enregistre les stocks de produits dans les entrepôts
        // Un entrepôt peut contenir plusieurs variantes de produits (relation one-to-many)
        // Une variante de produit peut être stockée dans plusieurs entrepôts (relation many-to-many via product_warehouse_stocks)
        // Exemple de relation : "Entrepôt de Paris" (entrepôt) > "T-shirt rouge, taille M" (variante de produit) > "Quantité: 100" (stock)     
        Schema::create('product_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('warehouse_id')->constrained('product_warehouses')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_warehouses
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_variants
            //
            $table->integer('quantity')->default(0); // Stock physique disponible dans l'entrepôt
            $table->integer('reserved_quantity')->default(0); // Quantité réservée par des commandes non expédiées
            $table->unsignedInteger('low_stock_threshold')->default(5); // Seuil d'alerte de stock bas
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->unique(['warehouse_id', 'product_variant_id'], 'warehouse_variant_unique'); // Une seule ligne par couple (entrepôt, variante)
            $table->index(['product_variant_id']); // Retrouve rapidement les stocks d'une variante
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_stocks');
    }
};