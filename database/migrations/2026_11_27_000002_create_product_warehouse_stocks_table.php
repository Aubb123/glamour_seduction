<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('warehouse_id')->constrained('product_warehouses')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnUpdate()->restrictOnDelete();

            $table->integer('quantity')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['warehouse_id', 'product_variant_id'],
                'pws_warehouse_variant_unique'
            );

            $table->index('product_variant_id', 'pws_variant_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_stocks');
    }
};
