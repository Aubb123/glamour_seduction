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
        // Table: product_warehouses, enregistre les entrepôts de produits
        // Un entrepôt possède un code unique, un type (stockage, retours, transit) et peut être l'entrepôt par défaut
        // Exemple d'entrepôt : "COT-01" (Cotonou, stockage, actif, par défaut)
        Schema::create('product_warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->integer('sort_order')->unique(); // Ordre d'affichage de l'entrepôt dans les listes : 0 en premier
            $table->string('code', 30)->unique(); // Référence interne unique de l'entrepôt (ex : COT-01)
            $table->enum('type', ['storage', 'returns', 'transit'])->default('storage'); // Type d'entrepôt : stockage, retours ou transit
            //
            $table->foreignId('division_id')->constrained('geo_divisions')->cascadeOnUpdate()->cascadeOnDelete(); // Division de rattachement de l'entrepôt (obligatoire)
            $table->string('city')->nullable(); // Ville de l'entrepôt
            $table->string('address')->nullable(); // Adresse complète de l'entrepôt
            $table->decimal('latitude', 10, 7)->nullable(); // Latitude GPS (entre -90 et 90)
            $table->decimal('longitude', 10, 7)->nullable(); // Longitude GPS (entre -180 et 180)
            //
            $table->string('phone')->nullable(); // Téléphone du contact
            $table->string('email')->nullable(); // Email du contact
            $table->text('notes')->nullable(); // Notes internes libres
            $table->json('additional_info')->nullable(); // Informations supplémentaires (JSON, réservé pour un usage futur)
            //
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Indique si l'entrepôt est actif
            $table->boolean('is_default')->default(false); // Entrepôt de réservation par défaut (un seul autorisé)
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'type', 'deleted_at']); // Index pour les filtres courants
            $table->index(['division_id', 'city']); // Retrouve rapidement les entrepôts d'une division ou d'une ville
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
