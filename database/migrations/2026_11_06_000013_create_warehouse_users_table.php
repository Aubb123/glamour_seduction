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
        // Table: warehouse_users, affecte le personnel (utilisateurs) aux entrepôts.
        // Même logique que shop_users : pivot enrichi (poste + responsable).
        Schema::create('warehouse_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('warehouse_id')->constrained('product_warehouses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->cascadeOnUpdate()->nullOnDelete();
            //
            $table->boolean('is_manager')->default(false); // Responsable de l'entrepôt
            //
            $table->timestamps();
            //
            $table->unique(['warehouse_id', 'user_id']); // Une seule affectation par couple (entrepôt, utilisateur)
            $table->index(['user_id']); // Retrouve rapidement les entrepôts d'un utilisateur
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_users');
    }
};
