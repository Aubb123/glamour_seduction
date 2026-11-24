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
        // Table: product_warehouse_location_translations, enregistre les traductions des emplacements
        // Un emplacement peut avoir plusieurs traductions, une par langue
        // Exemple de traduction : "Étagère A1" (fr) > "Shelf A1" (en)
        // NB : pas de colonne slug. Le nom est unique PAR ENTREPÔT ET PAR LANGUE (règle applicative),
        // cette unicité ne peut pas être une contrainte SQL car la table n'a pas warehouse_id.
        Schema::create('product_warehouse_location_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('location_id')->constrained('product_warehouse_locations')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_warehouse_locations
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table langs fr, en, ar, es, pt, zh, de
            //
            $table->string('name', 150); // Nom de l'emplacement dans la langue de traduction
            $table->text('description'); // Description de l'emplacement dans la langue de traduction (obligatoire)
            //
            $table->timestamps();
            $table->softDeletes();
            // Contraintes d'unicité (nom explicite : limite MySQL de 64 caractères)
            $table->unique(['location_id', 'lang_id'], 'pwl_translations_location_lang_unique'); // Une seule traduction par emplacement et par langue
            // Index pour les filtres courants
            $table->index(['deleted_at']); // Index pour les filtres courants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_location_translations');
    }
};
