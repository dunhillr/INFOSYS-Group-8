<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('yield_per_parent');
            // Weight per unit in kilograms (e.g., 150 for Block Ice, 25 for Crushed Ice bag)
            $table->decimal('weight_kg', 10, 2)->nullable()->after('parent_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight_kg');
            $table->decimal('yield_per_parent', 10, 2)->nullable()->after('parent_product_id');
        });
    }
};
