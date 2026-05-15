<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add parent_product_id to products (self-reference: e.g. Crushed Ice's parent is Block Ice)
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('parent_product_id')
                  ->nullable()
                  ->after('is_active')
                  ->constrained('products')
                  ->onDelete('set null');
        });

        // Add parent tracking fields to productions
        Schema::table('productions', function (Blueprint $table) {
            // Which raw material (parent product) was consumed
            $table->foreignId('parent_product_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('products')
                  ->onDelete('set null');

            // How many units of the parent were used
            $table->decimal('parent_quantity_used', 10, 2)
                  ->nullable()
                  ->after('parent_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign(['parent_product_id']);
            $table->dropColumn(['parent_product_id', 'parent_quantity_used']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['parent_product_id']);
            $table->dropColumn('parent_product_id');
        });
    }
};
