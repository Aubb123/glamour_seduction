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
        Schema::create('blog_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('tag_id')->constrained('blog_tags')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('lang_id')->constrained('langs')->cascadeOnUpdate()->cascadeOnDelete();
            //
            $table->string('name');
            $table->string('slug');
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->unique(['tag_id', 'lang_id']);
            $table->unique(['name', 'lang_id']);
            $table->unique(['slug', 'lang_id']);
            // Index pour les filtres courants
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_tag_translations');
    }
};
