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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('or_number')->nullable()->after('sale_number');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('total_amount');
            // We can keep product_id, quantity, unit_price in sales table as nullable for backward compatibility, or drop them. 
            // It's safer to just make them nullable, but wait, they are already:
            // product_id is nullable. quantity and unit_price are NOT nullable.
            // Let's make quantity and unit_price nullable since we use sale_items now.
            $table->decimal('quantity', 10, 2)->nullable()->change();
            $table->decimal('unit_price', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['or_number', 'delivery_fee']);
            $table->decimal('quantity', 10, 2)->nullable(false)->change();
            $table->decimal('unit_price', 10, 2)->nullable(false)->change();
        });
    }
};
