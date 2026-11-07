<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table : product_warehouses — entrepôts produits.
     *
     * Ne conserve QUE les données non traduites (code, status) :
     *  - name / address / city / phone sont portés par product_warehouse_translations
     *    (migration 2026_11_06_000020_create_product_warehouse_translations_table) ;
     *  - « entrepôt par défaut » n'est PAS une propriété globale de l'entrepôt : c'est
     *    une notion PAR BOUTIQUE, portée par le pivot shop_warehouse.is_primary
     *    (voir ShopService::syncWarehouses()).
     *
     * NB : les ex-migrations 2026_11_06_000021 (drop des colonnes traduisibles) et
     * 2026_11_06_000022 (drop de is_default) ont été fusionnées ici : leurs colonnes
     * n'existent plus dès la création, ce qui évite des colonnes créées puis aussitôt
     * supprimées lors d'un migrate:fresh.
     */
    public function up(): void
    {
        Schema::create('product_warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->string('code')->unique(); // Code unique de l'entrepôt (ex : COT-01, PKO-01)
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si l'entrepôt est utilisable
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouses');
    }
};
