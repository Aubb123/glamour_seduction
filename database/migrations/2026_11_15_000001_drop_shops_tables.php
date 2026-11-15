<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop des tables du module Boutiques (Shops) retiré.
     *
     * Les tables sont dropées dans l'ordre inverse des dépendances de clés
     * étrangères pour éviter les erreurs de contrainte.
     * `warehouse_users` (module Entrepôts) est PRÉSERVÉ : il n'est pas dropé.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('shop_users');
        Schema::dropIfExists('shop_warehouse');
        Schema::dropIfExists('shop_translations');
        Schema::dropIfExists('shops');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Inverse de la migration : rien à recréer (le module Shops est supprimé).
     */
    public function down(): void
    {
        // Le module Shops a été retiré définitivement : aucune recréation.
    }
};