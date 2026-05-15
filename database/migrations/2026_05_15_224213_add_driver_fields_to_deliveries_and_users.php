<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add driver_id + proof_of_delivery to deliveries ──
        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('vehicle_id')->constrained('users')->nullOnDelete();
            $table->string('proof_of_delivery')->nullable()->after('notes');
        });

        // ── 2. Expand user_type enum to include 'driver' ──
        // SQLite does not support ALTER COLUMN. We recreate the table via raw SQL.
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // Disable foreign keys temporarily
            DB::statement('PRAGMA foreign_keys = OFF');

            // Rebuild users table with expanded check constraint
            DB::statement("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    username VARCHAR(255) NOT NULL UNIQUE,
                    email VARCHAR(255) UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    user_type VARCHAR(255) NOT NULL DEFAULT 'employee'
                        CHECK(user_type IN ('owner','employee','driver')),
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    remember_token VARCHAR(100),
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ");

            DB::statement('INSERT INTO users_new SELECT * FROM users');
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');

            DB::statement('PRAGMA foreign_keys = ON');
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('owner','employee','driver') NOT NULL DEFAULT 'employee'");
        }
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['driver_id', 'proof_of_delivery']);
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    username VARCHAR(255) NOT NULL UNIQUE,
                    email VARCHAR(255) UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    user_type VARCHAR(255) NOT NULL DEFAULT 'employee'
                        CHECK(user_type IN ('owner','employee')),
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    remember_token VARCHAR(100),
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ");

            DB::statement("INSERT INTO users_new SELECT * FROM users WHERE user_type IN ('owner','employee')");
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');

            DB::statement('PRAGMA foreign_keys = ON');
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('owner','employee') NOT NULL DEFAULT 'employee'");
        }
    }
};
