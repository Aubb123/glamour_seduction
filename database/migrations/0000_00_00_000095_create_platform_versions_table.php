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
        Schema::create('platform_versions', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID unique pour chaque réponse
            //
            $table->string('code')->unique(); // "v1", "v2"
            //
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // V2 existe mais pas encore active (accessible en front)
            $table->boolean('is_published')->default(false); // V2 existe mais pas encore publiée
            $table->boolean('is_default')->default(false); // Version par défaut de la plateforme
            $table->timestamp('released_at')->nullable(); // Date de lancement
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'is_published', 'released_at', 'deleted_at'], 'platform_versions_filters_index'); // Index pour les filtres courants
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_versions');
    }
};
