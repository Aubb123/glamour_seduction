<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop des tables du module Stocks & Mouvements de stock retiré.
     *
     * Les tables sont dropées dans l'ordre inverse des dépendances de clés
     * étrangères pour éviter les erreurs de contrainte.
     * `product_warehouses` et `warehouse_users` (modules Entrepôts / Produits)
     * sont PRÉSERVÉS : ils ne sont pas dropés.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('product_stock_movements');
        Schema::dropIfExists('product_warehouse_stocks');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Inverse de la migration : rien à recréer (le module Stocks est supprimé).
     */
    public function down(): void
    {
        // Le module Stocks & Mouvements a été retiré définitivement : aucune recréation.
    }
};