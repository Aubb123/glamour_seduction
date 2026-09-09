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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->string('code', 3)->unique(); // Code ISO 4217 alpha-3 (ex: EUR, USD, XOF)
            $table->string('symbol', 10); // Symbole de la devise (€, $, ₫...)
            $table->unsignedTinyInteger('decimal_places')->default(2); // Nombre de décimales (0 pour JPY/KRW, 3 pour KWD/BHD/TND)
            $table->string('numeric_code', 3)->nullable()->unique(); // Code ISO 4217 numérique (ex: 978, 840, 952)
            $table->integer('sort_order')->unique(); // Utile dans les selects/listes // Pour l'ordre d'affichage : 0 en premier
            $table->boolean('is_default')->default(false); // Devise par défaut de la plateforme (une seule doit être true)
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si la devise est active
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'is_default', 'deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};