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
        // Table: positions, enregistre les postes (fonctions) du personnel.
        // Une position est partagée entre les boutiques (shop_users) et les
        // entrepôts (warehouse_users) ; la colonne `context` restreint la portée :
        //   - shop      → poste réservé aux boutiques
        //   - warehouse → poste réservé aux entrepôts
        //   - both      → poste utilisable dans les deux
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->string('code')->unique(); // Code unique du poste (ex : SHOP-MANAGER, WH-KEEPER)
            $table->enum('context', ['shop', 'warehouse', 'both'])->default('both'); // Portée du poste
            //
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si le poste est utilisable
            $table->unsignedInteger('sort_order')->default(0); // Ordre d'affichage dans les listes
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['context', 'status', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
