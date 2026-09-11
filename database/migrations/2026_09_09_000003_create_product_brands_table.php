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
        // Table: product_brands, enregistre les marques de produits
        // Une marque peut avoir plusieurs produits (relation one-to-many)
        // Exemple de marques : "Nike", "Adidas", "Puma"
        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->integer('sort_order')->unique(); // Utile dans les selects/listes // Pour l'ordre d'affichage : 0 en premier
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si la marque est visible
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'sort_order', 'deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_brands');
    }
};
