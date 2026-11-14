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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('loggable'); // crée déjà l’index ['loggable_type' => 'App\Models\GalleryTranslation', 'loggable_id' => 1]
            //
            $table->string('type');
            $table->string('action');
            $table->string('message')->nullable();
            $table->json('context')->nullable();
            $table->string('activity')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_success')->nullable(); // null = non défini (N/A), true = succès, false = échec
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index('action');
            $table->index('created_at');
            $table->index(['user_id', 'deleted_at']);
            $table->index(['type', 'is_success', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
