<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * « Entrepôt par défaut » est une notion PAR BOUTIQUE : elle est portée par le
     * pivot `shop_warehouse` via la colonne `is_primary` (entrepôt principal
     * d'approvisionnement d'une boutique), gérée par ShopService::syncWarehouses().
     *
     * La colonne globale `product_warehouses.is_default` (un seul entrepôt par défaut
     * pour toute la plateforme) était une abstraction erronée → on la retire.
     */
    public function up(): void
    {
        Schema::table('product_warehouses', function (Blueprint $table) {
            if (Schema::hasColumn('product_warehouses', 'is_default')) {
                $table->dropColumn('is_default');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_warehouses', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('status'); // Entrepôt de réservation par défaut (global — rétabli pour rollback)
        });
    }
};
