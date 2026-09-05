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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->foreignId('category_id')->constrained('blog_categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            //
            $table->unsignedInteger('views')->default(0);
            $table->unsignedSmallInteger('time_read')->default(1);
            $table->enum('status', ['draft', 'published', 'archived', 'scheduled'])->default('draft'); // Status de l'article brouillon, publié, archivé ou programmé
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['category_id', 'status', 'deleted_at']);
            $table->index(['user_id', 'deleted_at']);
            $table->index(['status', 'published_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
