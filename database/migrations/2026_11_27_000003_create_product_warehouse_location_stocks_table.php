<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_warehouse_location_stocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('location_id')->constrained('product_warehouse_locations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnUpdate()->cascadeOnDelete();

            $table->integer('quantity')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['location_id', 'product_variant_id'], 'pwls_location_variant_unique');
            $table->index('product_variant_id', 'pwls_variant_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_location_stocks');
    }
};
