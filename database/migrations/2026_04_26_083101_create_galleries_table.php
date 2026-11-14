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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            //
            $table->integer('sort_order')->unique();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'sort_order', 'deleted_at']);
            $table->index(['user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
