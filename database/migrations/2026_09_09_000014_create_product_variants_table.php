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
        // Table: product_variants, enregistre les variantes de produits
        // Une variante appartient à un seul produit (relation one-to-many)
        // Exemple de variante : "T-shirt rouge, taille M" (variante du produit "T-shirt rouge")
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers products (une variante appartient à UN produit)
            //
            $table->string('sku')->unique(); // Référence unique de la variante (Stock Keeping Unit)
            $table->string('barcode')->nullable(); // Code-barres optionnel (EAN-13, UPC…)
            //
            $table->decimal('price', 10, 2)->nullable(); // Si NULL → on utilise base_price du produit
            $table->enum('status', ['active', 'inactive'])->default('active'); // Indique si la variante est vendable
            $table->boolean('is_default')->default(false); // Variante « simple » auto-créée pour un produit sans variantes
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['product_id']); // Retrouve rapidement les variantes d'un produit
            $table->index(['status', 'deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
