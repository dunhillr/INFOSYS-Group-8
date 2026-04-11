<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'customer_name' => 'Metro Supermarket',
                'customer_contact' => '555-0101',
                'customer_address' => '123 Main Street, Downtown District',
                'customer_type' => 'wholesale',
                'notes' => 'Large wholesale chain, weekly orders',
            ],
            [
                'customer_name' => 'Tropical Fruits & Vegetables',
                'customer_contact' => '555-0102',
                'customer_address' => '456 Market Ave, Business District',
                'customer_type' => 'wholesale',
                'notes' => 'Restaurant supply partner',
            ],
            [
                'customer_name' => 'Sunshine Beach Resort',
                'customer_contact' => '555-0103',
                'customer_address' => '789 Beach Road, Coastal Area',
                'customer_type' => 'wholesale',
                'notes' => 'Resort, high volume seasonal orders',
            ],
            [
                'customer_name' => 'Quick Convenience Store',
                'customer_contact' => '555-0104',
                'customer_address' => '321 Oak Lane, Residential District',
                'customer_type' => 'walk-in',
                'notes' => 'Regular customer, small orders',
            ],
            [
                'customer_name' => 'Premium Catering Services',
                'customer_contact' => '555-0105',
                'customer_address' => '654 Park Street, Event Center',
                'customer_type' => 'wholesale',
                'notes' => 'Event catering partner, bulk orders',
            ],
            [
                'customer_name' => 'Happy Juice Bar',
                'customer_contact' => '555-0106',
                'customer_address' => '987 City Center, Shopping District',
                'customer_type' => 'walk-in',
                'notes' => 'Daily customer for cold beverages',
            ],
            [
                'customer_name' => 'Industrial Cooling Systems',
                'customer_contact' => '555-0107',
                'customer_address' => '159 Industrial Way, Factory Zone',
                'customer_type' => 'wholesale',
                'notes' => 'Industrial client, large orders',
            ],
            [
                'customer_name' => 'Dragon Restaurant Group',
                'customer_contact' => '555-0108',
                'customer_address' => '741 Food Court, Commercial Hub',
                'customer_type' => 'wholesale',
                'notes' => 'Multi-location restaurant chain',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['customer_name' => $customer['customer_name']], $customer);
        }
    }
}
