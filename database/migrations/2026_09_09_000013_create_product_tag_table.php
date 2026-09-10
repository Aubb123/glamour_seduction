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
        Schema::create('product_tag', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            //
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers products
            $table->foreignId('tag_id')->constrained('product_tags')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers product_tags
            //
            $table->timestamps();
            $table->softDeletes();
            //
            $table->unique(['product_id', 'tag_id']); // Évite les doublons
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tag');
    }
};