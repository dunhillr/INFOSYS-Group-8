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
        $owner = User::where('username', 'bobongowner')->first() ?? User::where('username', 'owner')->first();
        $bea = User::where('username', 'beatoos')->first() ?? $owner;
        $vengie = User::where('username', 'vengiecabanilla')->first() ?? $owner;

        // Fetch custom drivers
        $robert = User::where('username', 'robertosquiza')->first();
        $dunhill = User::where('username', 'dunhillbendoy')->first();
        $ivan = User::where('username', 'ivanmailem')->first();
        $cj = User::where('username', 'cjtacis')->first();

        $sales = Sale::all();
        $vehicles = Vehicle::all();

        if ($sales->isEmpty() || $vehicles->isEmpty()) {
            $this->command->info('Sales or Vehicles not found. Skipping DeliverySeeder.');
            return;
        }

        // Pair drivers with vehicles
        if ($vehicles->count() >= 4) {
            $vehicles->get(0)->update(['assigned_driver_id' => $robert?->id]);
            $vehicles->get(1)->update(['assigned_driver_id' => $dunhill?->id]);
            $vehicles->get(2)->update(['assigned_driver_id' => $ivan?->id]);
            $vehicles->get(3)->update(['assigned_driver_id' => $cj?->id]);
        }

        $deliveries = [
            [
                'sale_id' => $sales->first()->id,
                'customer_id' => $sales->first()->customer_id,
                'vehicle_id' => $vehicles->get(0)->id,
                'destination' => '123 Main Street, Downtown District',
                'delivery_date' => now()->subDays(3)->toDateString(),
                'delivery_time' => '09:00',
                'status' => 'delivered',
                'assigned_by' => $owner->id,
                'delivered_by' => $bea->id,
                'notes' => 'Delivered successfully',
            ],
            [
                'sale_id' => $sales->skip(1)->first()->id,
                'customer_id' => $sales->skip(1)->first()->customer_id,
                'vehicle_id' => $vehicles->get(1)->id,
                'destination' => '456 Market Ave, Business District',
                'delivery_date' => now()->subDays(2)->toDateString(),
                'delivery_time' => '10:30',
                'status' => 'delivered',
                'assigned_by' => $bea->id,
                'delivered_by' => $vengie->id,
                'notes' => 'On-time delivery',
            ],
            [
                'sale_id' => $sales->skip(2)->first()->id,
                'customer_id' => $sales->skip(2)->first()->customer_id,
                'vehicle_id' => $vehicles->get(2)->id,
                'destination' => '789 Beach Road, Coastal Area',
                'delivery_date' => now()->subDays(1)->toDateString(),
                'delivery_time' => '14:00',
                'status' => 'delivered',
                'assigned_by' => $vengie->id,
                'delivered_by' => $bea->id,
                'notes' => 'Beach resort order',
            ],
            [
                'sale_id' => $sales->skip(3)->first()->id,
                'customer_id' => $sales->skip(3)->first()->customer_id,
                'vehicle_id' => $vehicles->get(0)->id,
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
                'vehicle_id' => $vehicles->get(3)->id,
                'destination' => '654 Park Street, Event Center',
                'delivery_date' => now()->addDays(1)->toDateString(),
                'delivery_time' => '08:00',
                'status' => 'pending',
                'assigned_by' => $vengie->id,
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
