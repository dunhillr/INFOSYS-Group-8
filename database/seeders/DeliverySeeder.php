<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Sale;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('username', 'owner')->first();
        $employee = User::where('username', 'employee')->first();
        $sales = Sale::all();
        $vehicles = Vehicle::all();

        if ($sales->isEmpty() || $vehicles->isEmpty()) {
            $this->command->info('Sales or Vehicles not found. Skipping DeliverySeeder.');
            return;
        }

        $deliveries = [
            [
                'sale_id' => $sales->first()->id,
                'customer_id' => $sales->first()->customer_id,
                'vehicle_id' => $vehicles->where('status', 'available')->first()->id,
                'destination' => '123 Main Street, Downtown District',
                'delivery_date' => now()->subDays(3)->toDateString(),
                'delivery_time' => '09:00',
                'status' => 'delivered',
                'assigned_by' => $owner->id,
                'delivered_by' => $employee->id,
                'notes' => 'Delivered successfully',
            ],
            [
                'sale_id' => $sales->skip(1)->first()->id,
                'customer_id' => $sales->skip(1)->first()->customer_id,
                'vehicle_id' => $vehicles->skip(1)->where('status', 'available')->first()->id,
                'destination' => '456 Market Ave, Business District',
                'delivery_date' => now()->subDays(2)->toDateString(),
                'delivery_time' => '10:30',
                'status' => 'delivered',
                'assigned_by' => $owner->id,
                'delivered_by' => $owner->id,
                'notes' => 'On-time delivery',
            ],
            [
                'sale_id' => $sales->skip(2)->first()->id,
                'customer_id' => $sales->skip(2)->first()->customer_id,
                'vehicle_id' => $vehicles->where('status', 'available')->skip(2)->first()?->id,
                'destination' => '789 Beach Road, Coastal Area',
                'delivery_date' => now()->subDays(1)->toDateString(),
                'delivery_time' => '14:00',
                'status' => 'delivered',
                'assigned_by' => $employee->id,
                'delivered_by' => $employee->id,
                'notes' => 'Beach resort order',
            ],
            [
                'sale_id' => $sales->skip(3)->first()->id,
                'customer_id' => $sales->skip(3)->first()->customer_id,
                'vehicle_id' => $vehicles->where('status', 'available')->first()->id,
                'destination' => '321 Oak Lane, Residential District',
                'delivery_date' => now()->toDateString(),
                'delivery_time' => '11:00',
                'status' => 'pending',
                'assigned_by' => $owner->id,
                'delivered_by' => null,
                'notes' => 'Scheduled for today',
            ],
            [
                'sale_id' => $sales->skip(4)->first()->id,
                'customer_id' => $sales->skip(4)->first()->customer_id,
                'vehicle_id' => $vehicles->where('status', 'in_use')->first()?->id,
                'destination' => '654 Park Street, Event Center',
                'delivery_date' => now()->addDays(1)->toDateString(),
                'delivery_time' => '08:00',
                'status' => 'pending',
                'assigned_by' => $employee->id,
                'delivered_by' => null,
                'notes' => 'Tomorrow morning delivery',
            ],
        ];

        foreach ($deliveries as $delivery) {
            if ($delivery['vehicle_id'] === null && in_array($delivery['status'], ['pending'])) {
                // Skip if no vehicle and it's pending
                continue;
            }
            Delivery::create($delivery);
        }
    }
}
