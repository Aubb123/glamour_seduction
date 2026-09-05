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
        Schema::create('cloud_folders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('cloud_platform_id')->constrained('cloud_platforms')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('cloud_folder_id')->nullable()->constrained('cloud_folders')->cascadeOnUpdate()->nullOnDelete();
            //
            $table->string('folder_name');
            $table->string('folder_id')->nullable();
            $table->string('path')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            //
            $table->string('entity_table')->nullable(); // Ajout du champ "emplacement" pour stocker le chemin relatif du dossier
            $table->json('content_type')->nullable(); // Ajout du champ "content_type" pour indiquer le type de contenu attendu dans le dossier (ex: images, videos, documents, etc.)
            //
            $table->timestamps();
            $table->softDeletes();
            //
            // NB : en MySQL, une index unique autorise plusieurs lignes avec
            // `cloud_folder_id` NULL. Cela signifie que des dossiers "racine"
            // de même nom sur une plateforme peuvent se dupliquer à ce niveau.
            // La logique d'architecture repose sur CloudFolder::firstOrCreate()
            // pour absorber ces collisions, mais le garde-fou n'est pas absolu.
            $table->unique(['cloud_platform_id', 'cloud_folder_id', 'folder_name'], 'cloud_folders_platform_folder_name_unique');
            // Index pour les filtres courants
            $table->index('cloud_folder_id');
            $table->index('entity_table', 'cloud_folders_entity_table_index');
            $table->index('status', 'cloud_folders_status_index');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_folders');
    }
};
