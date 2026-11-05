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
        // Table: products, enregistre les produits
        // Un produit appartient à une catégorie et peut appartenir à une marque (relation one-to-many)
        // Un produit peut avoir plusieurs tags et attributs (relation many-to-many via product_tag_product et product_attribute_product)
        // Exemple de produit : "T-shirt rouge Nike" (catégorie: "T-shirts", marque: "Nike", tags: "Nouveauté", attribut
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnUpdate()->restrictOnDelete(); // Une catégorie à produits ne s'efface pas si elle a des produits
            $table->foreignId('brand_id')->nullable()->constrained('product_brands')->cascadeOnUpdate()->nullOnDelete(); // Marque optionnelle
            //
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // Statut de publication du produit
            $table->boolean('is_featured')->default(false); // Produit mis en avant sur la boutique
            $table->unsignedInteger('views_count')->default(0); // Compteur de vues
            $table->decimal('weight', 8, 2)->nullable(); // Poids du produit en kg (logistique)
            //
            $table->decimal('base_price', 10, 2); // Prix de base (saisi en devise par défaut de la boutique)
            $table->decimal('compare_at_price', 10, 2)->nullable(); // Prix barré optionnel (doit être > base_price)
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['category_id']); // Retrouve rapidement les produits d'une catégorie
            $table->index(['brand_id']); // Retrouve rapidement les produits d'une marque
            $table->index(['status', 'deleted_at']); // Index pour les filtres courants
            $table->index(['is_featured', 'deleted_at']); // Index pour les mises en avant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
