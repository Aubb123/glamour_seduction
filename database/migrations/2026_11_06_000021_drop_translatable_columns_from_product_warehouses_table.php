<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Les champs traduisibles (name, address, city, phone) sont désormais portés par
     * la table product_warehouse_translations. On les retire de la table principale,
     * qui ne conserve que les données non traduites (code, status).
     *
     * NB : la colonne globale is_default est retirée par la migration 000022
     * (« entrepôt par défaut » est une notion PAR BOUTIQUE → pivot shop_warehouse.is_primary).
     */
    public function up(): void
    {
        Schema::table('product_warehouses', function (Blueprint $table) {
            // Suppression des colonnes traduisibles désormais gérées par product_warehouse_translations
            $columns = array_filter([
                Schema::hasColumn('product_warehouses', 'name') ? 'name' : null,
                Schema::hasColumn('product_warehouses', 'address') ? 'address' : null,
                Schema::hasColumn('product_warehouses', 'city') ? 'city' : null,
                Schema::hasColumn('product_warehouses', 'phone') ? 'phone' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_warehouses', function (Blueprint $table) {
            $table->string('name')->nullable()->after('code'); // Nom de l'entrepôt (donnée de gestion interne, non traduite)
            $table->string('address')->nullable(); // Adresse physique de l'entrepôt
            $table->string('city')->nullable(); // Ville de l'entrepôt
            $table->string('phone')->nullable(); // Numéro de téléphone de l'entrepôt
        });
    }
};
