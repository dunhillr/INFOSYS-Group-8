<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create default users
        User::updateOrCreate(['username' => 'owner'], [
            'name' => 'System Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'owner',
            'is_active' => true,
        ]);

        User::updateOrCreate(['username' => 'employee'], [
            'name' => 'System Employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'employee',
            'is_active' => true,
        ]);

        // 2. Create additional users
        $this->call(UserSeeder::class);

        // 3. Initialize inventory
        Inventory::firstOrCreate([], [
            'current_stock' => 0,
            'low_stock_threshold' => 100,
        ]);

        // 4. Seed products, customers, and vehicles
        $this->call(ProductSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(VehicleSeeder::class);

        // 5. Seed operations (production, sales, deliveries)
        $this->call(ProductionSeeder::class);
        $this->call(SaleSeeder::class);
        $this->call(DeliverySeeder::class);
    }
}