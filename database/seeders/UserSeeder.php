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
