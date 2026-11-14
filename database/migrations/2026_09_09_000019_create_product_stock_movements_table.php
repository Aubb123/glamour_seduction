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
        // Table: product_stock_movements, enregistre les mouvements de stock des variantes de produits dans les entrepôts
        // Un mouvement de stock appartient à un entrepôt et à une variante de produit (relation one-to-many)
        // Exemple de mouvement : "Entrepôt de Paris" (entrepôt) > "T-shirt rouge, taille M" (variante de produit) > "Quantité: 10" (mouvement d'entrée)
        Schema::create('product_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('warehouse_id')->constrained('product_warehouses')->cascadeOnUpdate()->restrictOnDelete(); // Clé étrangère vers product_warehouses (historique préservé)
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnUpdate()->restrictOnDelete(); // Clé étrangère vers product_variants (historique préservé)
            //
            $table->string('type'); // in | out | adjustment | transfer_in | transfer_out | return
            $table->unsignedInteger('quantity'); // Toujours > 0 : le sens (entrée/sortie) est donné par le type
            //
            $table->nullableMorphs('reference'); // Référence polymorphique optionnelle (commande, réassort, transfert…)
            $table->text('note')->nullable(); // Note libre du mouvement
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete(); // Utilisateur ayant enregistré le mouvement
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->index(['product_variant_id', 'warehouse_id']); // Historique d'une variante dans un entrepôt
            $table->index(['type']); // Filtre par type de mouvement
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_movements');
    }
};
