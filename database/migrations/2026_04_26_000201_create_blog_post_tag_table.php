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
        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            //
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers blog_posts
            $table->foreignId('tag_id')->constrained('blog_tags')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers blog_tags
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->unique(['post_id', 'tag_id']); // Évite les doublons
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_tag');
    }
};
