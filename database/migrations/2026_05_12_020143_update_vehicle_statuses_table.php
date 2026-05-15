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
        // For SQLite, changing enum is difficult. We'll switch to string for better compatibility.
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('status')->default('available')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('status', ['available', 'in_use', 'maintenance'])->default('available')->change();
        });
    }
};
