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
        // Table: product_warehouse_locations, enregistre les emplacements physiques d'un entrepôt
        // Un emplacement appartient à un entrepôt, peut avoir un emplacement parent (hiérarchie libre)
        // et porte un code unique par entrepôt (ex : COT-01-Z1)
        Schema::create('product_warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('warehouse_id')->constrained('product_warehouses')->restrictOnDelete()->cascadeOnUpdate(); // Entrepôt de rattachement (obligatoire)
            $table->foreignId('parent_id')->nullable()->constrained('product_warehouse_locations')->restrictOnDelete()->cascadeOnUpdate(); // Emplacement parent (hiérarchie libre, null = racine)
            //
            $table->enum('type', ['zone', 'aisle', 'shelf', 'bin'])->default('shelf'); // Type d'emplacement : zone, allée, étagère ou bac
            $table->string('code', 30); // Référence interne unique PAR ENTREPÔT (ex : COT-01-Z1)
            $table->unsignedInteger('sort_order')->default(0); // Ordre d'affichage DANS l'entrepôt : 0 en premier
            $table->text('notes')->nullable(); // Notes internes libres
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si l'emplacement est utilisable
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->unique(['warehouse_id', 'code']); // Code unique par entrepôt (corbeille incluse)
            $table->unique(['warehouse_id', 'sort_order']); // Ordre d'affichage unique par entrepôt (corbeille incluse)
            // Index pour les filtres courants (noms explicites : limite MySQL de 64 caractères)
            $table->index(['warehouse_id', 'parent_id', 'status', 'deleted_at'], 'pwl_warehouse_parent_status_deleted_index'); // Filtres entrepôt / parent / statut
            $table->index(['type'], 'pwl_type_index'); // Filtre par type d'emplacement
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_locations');
    }
};
