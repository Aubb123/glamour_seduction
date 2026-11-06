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
        // Table pivot: shop_warehouse, relie les boutiques aux entrepôts dont elles
        // s'approvisionnent (relation many-to-many). Une boutique est TOUJOURS liée
        // à au moins un entrepôt (règle métier appliquée dans ShopService, pas ici).
        // `is_primary` désigne l'entrepôt principal d'approvisionnement d'une boutique
        // (au plus un par boutique, garanti applicativement).
        //
        // NB : PAS de colonne `uuid` ici. Cette table est un PIVOT PUR piloté par la
        // relation Eloquent `belongsToMany` : `->sync()` effectue un INSERT direct en
        // base et ne déclenche AUCUN événement de modèle, donc le trait
        // HasModelBehaviors ne pourrait jamais générer le uuid (d'où l'erreur
        // « Field 'uuid' doesn't have a default value »). L'identité d'une ligne
        // est le couple unique (shop_id, warehouse_id).
        Schema::create('shop_warehouse', function (Blueprint $table) {
            $table->id();
            //
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('product_warehouses')->cascadeOnUpdate()->cascadeOnDelete();
            //
            $table->boolean('is_primary')->default(false); // Entrepôt principal d'approvisionnement de la boutique
            //
            $table->timestamps();
            //
            $table->unique(['shop_id', 'warehouse_id']); // Une seule ligne par couple (boutique, entrepôt)
            $table->index(['warehouse_id']); // Retrouve rapidement les boutiques d'un entrepôt
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_warehouse');
    }
};
