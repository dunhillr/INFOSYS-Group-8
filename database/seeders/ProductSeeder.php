<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'product_name' => 'Tube Ice',
                'product_code' => 'ICE-TUBE-001',
                'description' => 'Premium quality tube ice, perfect for retail and food service',
                'default_price' => 25.00,
                'is_active' => true,
            ],
            [
                'product_name' => 'Block Ice',
                'product_code' => 'ICE-BLOCK-001',
                'description' => 'Large solid block ice for wholesale and industrial use',
                'default_price' => 100.00,
                'is_active' => true,
            ],
            [
                'product_name' => 'Crushed Ice',
                'product_code' => 'ICE-CRUSH-001',
                'description' => 'Fine crushed ice for beverages and food preservation',
                'default_price' => 15.00,
                'is_active' => true,
            ],
            [
                'product_name' => 'Flake Ice',
                'product_code' => 'ICE-FLAKE-001',
                'description' => 'Lightweight flake ice for cooling and preservation',
                'default_price' => 20.00,
                'is_active' => true,
            ],
            [
                'product_name' => 'Dry Ice',
                'product_code' => 'ICE-DRY-001',
                'description' => 'Dry ice for special cooling applications',
                'default_price' => 50.00,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['product_code' => $product['product_code']], $product);
        }
    }
}
