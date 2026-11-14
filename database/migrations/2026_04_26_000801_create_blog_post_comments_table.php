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
        Schema::create('blog_post_comments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            //
            $table->text('content');
            $table->boolean('is_visible')->default(false);
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['post_id', 'is_visible', 'deleted_at']);
            $table->index(['user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_comments');
    }
};
