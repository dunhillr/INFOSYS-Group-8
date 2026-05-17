<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Bobong Owner',
                'username' => 'bobongowner',
                'email' => 'bobongowner@example.com',
                'password' => 'password123',
                'user_type' => 'owner',
                'is_active' => true,
            ],
            [
                'name' => 'Bea To-os',
                'username' => 'beatoos',
                'email' => 'beatoos@example.com',
                'password' => 'password123',
                'user_type' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Vengie Cabanilla',
                'username' => 'vengiecabanilla',
                'email' => 'vengiecabanilla@example.com',
                'password' => 'password123',
                'user_type' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Robert Osquiza',
                'username' => 'robertosquiza',
                'email' => 'robertosquiza@example.com',
                'password' => 'password123',
                'user_type' => 'driver',
                'is_active' => true,
            ],
            [
                'name' => 'Dunhill Bendoy',
                'username' => 'dunhillbendoy',
                'email' => 'dunhillbendoy@example.com',
                'password' => 'password123',
                'user_type' => 'driver',
                'is_active' => true,
            ],
            [
                'name' => 'Ivan Mailem',
                'username' => 'ivanmailem',
                'email' => 'ivanmailem@example.com',
                'password' => 'password123',
                'user_type' => 'driver',
                'is_active' => true,
            ],
            [
                'name' => 'CJ Tacis',
                'username' => 'cjtacis',
                'email' => 'cjtacis@example.com',
                'password' => 'password123',
                'user_type' => 'driver',
                'is_active' => true,
            ],
            [
                'name' => 'John Manager',
                'username' => 'john.manager',
                'email' => 'john.manager@example.com',
                'password' => 'password123',
                'user_type' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Maria Sales',
                'username' => 'maria.sales',
                'email' => 'maria.sales@example.com',
                'password' => 'password123',
                'user_type' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Carlos Delivery',
                'username' => 'carlos.delivery',
                'email' => 'carlos.delivery@example.com',
                'password' => 'password123',
                'user_type' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Ana Production',
                'username' => 'ana.production',
                'email' => 'ana.production@example.com',
                'password' => 'password123',
                'user_type' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Inactive User',
                'username' => 'inactive.user',
                'email' => 'inactive@example.com',
                'password' => 'password123',
                'user_type' => 'employee',
                'is_active' => false,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['username' => $user['username']], [
                ...$user,
                'password' => Hash::make($user['password']),
            ]);
        }
    }
}
