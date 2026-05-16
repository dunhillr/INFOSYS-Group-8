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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('deliveries', 'driver_id')) {
                $table->dropForeign(['driver_id']);
                $table->dropColumn('driver_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['assigned_driver_id']);
            $table->dropColumn('assigned_driver_id');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
