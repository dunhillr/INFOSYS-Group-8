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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // Migrate existing sales to sale_items
        $sales = DB::table('sales')->get();
        foreach ($sales as $sale) {
            if ($sale->product_id && $sale->quantity && $sale->unit_price) {
                DB::table('sale_items')->insert([
                    'sale_id' => $sale->id,
                    'product_id' => $sale->product_id,
                    'quantity' => $sale->quantity,
                    'unit_price' => $sale->unit_price,
                    'subtotal' => $sale->quantity * $sale->unit_price,
                    'created_at' => $sale->created_at,
                    'updated_at' => $sale->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
