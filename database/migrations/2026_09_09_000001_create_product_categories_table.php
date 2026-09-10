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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            // Catégorie parente (hiérarchie) — null = catégorie racine
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->cascadeOnUpdate()->nullOnDelete();
            //
            $table->integer('sort_order')->unique(); // Utile dans les selects/listes // Pour l'ordre d'affichage : 0 en premier
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si la catégorie est visible
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['parent_id']); // Retrouve rapidement les sous-catégories d'une catégorie
            $table->index(['status', 'sort_order', 'deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
