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
        Schema::create('subscribe_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('subscribe_id')->constrained('subscribes')->cascadeOnUpdate()->cascadeOnDelete();
            //
            $table->enum('action', ['subscribed', 'unsubscribed', 'resubscribed']); // Enum des actions possibles(Ex: 'abonné', 'désabonné', 'réabonné')
            $table->timestamp('acted_at');
            $table->string('ip_address')->nullable();
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['subscribe_id', 'acted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribe_histories');
    }
};
