<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'vehicle_name' => 'Refrigerated Van 01',
                'plate_number' => 'RV-001-2024',
                'capacity' => 500.00,
                'status' => 'available',
            ],
            [
                'vehicle_name' => 'Refrigerated Van 02',
                'plate_number' => 'RV-002-2024',
                'capacity' => 500.00,
                'status' => 'available',
            ],
            [
                'vehicle_name' => 'Ice Truck 01',
                'plate_number' => 'IT-001-2024',
                'capacity' => 1000.00,
                'status' => 'available',
            ],
            [
                'vehicle_name' => 'Ice Truck 02',
                'plate_number' => 'IT-002-2024',
                'capacity' => 1000.00,
                'status' => 'in_use',
            ],
            [
                'vehicle_name' => 'Light Delivery Van',
                'plate_number' => 'LDV-001-2024',
                'capacity' => 300.00,
                'status' => 'available',
            ],
            [
                'vehicle_name' => 'Premium Cooler Truck',
                'plate_number' => 'PCT-001-2024',
                'capacity' => 1500.00,
                'status' => 'maintenance',
            ],
            [
                'vehicle_name' => 'Freight Truck',
                'plate_number' => 'FT-001-2024',
                'capacity' => 2000.00,
                'status' => 'available',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(['plate_number' => $vehicle['plate_number']], $vehicle);
        }
    }
}
