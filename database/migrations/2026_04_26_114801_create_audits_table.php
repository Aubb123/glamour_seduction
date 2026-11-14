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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('auditable'); // crée déjà l'index ['auditable_type', 'auditable_id']
            //
            $table->enum('action', ['created', 'updated', 'deleted', 'restored', 'force_deleted']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            //
            $table->timestamps();
            // Index pour les filtres courants
            $table->index(['user_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
