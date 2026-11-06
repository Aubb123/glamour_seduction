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
        // Table: shop_users, affecte le personnel (utilisateurs) aux boutiques.
        // Un utilisateur peut être affecté à plusieurs boutiques (relation many-to-many
        // matérialisée par cette table pivot enrichie : poste + rôle de responsable).
        // `position_id` référence un poste partagé (context shop|warehouse|both).
        Schema::create('shop_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->cascadeOnUpdate()->nullOnDelete();
            //
            $table->boolean('is_manager')->default(false); // Responsable de la boutique
            //
            $table->timestamps();
            //
            $table->unique(['shop_id', 'user_id']); // Une seule affectation par couple (boutique, utilisateur)
            $table->index(['user_id']); // Retrouve rapidement les boutiques d'un utilisateur
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_users');
    }
};
