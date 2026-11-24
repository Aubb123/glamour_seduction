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
        // Table: product_warehouse_translations, enregistre les traductions des entrepôts
        // Un entrepôt peut avoir plusieurs traductions, une par langue et une traduction appartient à un seul entrepôt
        // Exemple de traduction : "Entrepôt principal" (fr) > "Main warehouse" (en)
        Schema::create('product_warehouse_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('warehouse_id')->constrained('product_warehouses')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_warehouses
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name', 150); // Nom de l'entrepôt dans la langue de traduction
            $table->string('slug'); // Slug SEO-friendly
            $table->text('description'); // Description de l'entrepôt dans la langue de traduction (obligatoire)
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité
            $table->unique(['warehouse_id', 'lang_id']); // Une seule traduction par entrepôt et par langue
            $table->unique(['name', 'lang_id']); // Nom unique par langue
            $table->unique(['slug', 'lang_id']); // Slug unique par langue
            // Index pour les filtres courants
            $table->index(['deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_translations');
    }
};
