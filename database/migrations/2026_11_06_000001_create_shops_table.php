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
        // Table: shops, enregistre les boutiques / points de vente.
        // Une boutique est toujours liée à au moins un entrepôt (relation many-to-many
        // via la table pivot `shop_warehouse`) : une boutique peut tirer d'un ou
        // plusieurs entrepôts. Le rattachement obligatoire est garanti au niveau
        // applicatif (ShopService), pas au niveau de la base.
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->string('code')->unique(); // Code unique de la boutique (ex : COT-SHOP-01)
            //
            $table->string('email')->nullable();   // Email de contact de la boutique
            $table->string('phone')->nullable();    // Téléphone de contact de la boutique
            $table->string('address')->nullable();  // Adresse physique de la boutique
            $table->string('city')->nullable();     // Ville de la boutique
            //
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si la boutique est ouverte
            $table->boolean('is_default')->default(false); // Boutique de référence (une seule)
            $table->unsignedInteger('sort_order')->default(0); // Ordre d'affichage dans les listes
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'is_default', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
