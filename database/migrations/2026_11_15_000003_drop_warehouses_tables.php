<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop des tables du module Entrepôts retiré.
     *
     * Les tables sont dropées dans l'ordre inverse des dépendances de clés
     * étrangères pour éviter les erreurs de contrainte.
     * `product_warehouse_stocks` et `product_stock_movements` (module Stocks déjà retiré)
     * ne sont pas touchés ici (déjà dropés par la migration précédente).
     * `warehouse_users` est dropé car il appartient au module Entrepôts.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('warehouse_users');
        Schema::dropIfExists('product_warehouse_translations');
        Schema::dropIfExists('product_warehouses');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Inverse de la migration : rien à recréer (le module Entrepôts est supprimé).
     */
    public function down(): void
    {
        // Le module Entrepôts a été retiré définitivement : aucune recréation.
    }
};