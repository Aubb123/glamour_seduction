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
        Schema::create('shop_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete();
            //
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->unique(['shop_id', 'lang_id']);
            $table->unique(['name', 'lang_id']);   // Nom unique par langue ✅
            $table->unique(['slug', 'lang_id']);   // Slug unique par langue ✅
            // Index pour les filtres courants
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_translations');
    }
};
