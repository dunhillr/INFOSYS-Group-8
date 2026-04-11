<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('username', 'owner')->first();
        $employee = User::where('username', 'employee')->first();
        $inventoryService = app(InventoryService::class);

        $customers = Customer::all();
        $products = Product::all();
        $wholesaleCustomers = Customer::where('customer_type', 'wholesale')->get();
        $walkInCustomers = Customer::where('customer_type', 'walk-in')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->info('Customers or Products not found. Skipping SaleSeeder.');
            return;
        }

        $sales = [
            [
                'product_id' => $products->first()->id ?? 1,
                'customer_id' => $wholesaleCustomers->first()?->id ?? $customers->first()->id ?? 1,
                'sale_date' => now()->subDays(4)->toDateString(),
                'sale_type' => 'wholesale',
                'quantity' => 100.00,
                'unit_price' => 25.00,
                'payment_status' => 'paid',
                'notes' => 'Large wholesale order',
                'user_id' => $owner->id,
            ],
            [
                'product_id' => $products->get(1)?->id ?? $products->first()->id ?? 1,
                'customer_id' => $wholesaleCustomers->get(1)?->id ?? $wholesaleCustomers->first()?->id ?? $customers->first()->id ?? 1,
                'sale_date' => now()->subDays(3)->toDateString(),
                'sale_type' => 'wholesale',
                'quantity' => 50.00,
                'unit_price' => 100.00,
                'payment_status' => 'partial',
                'notes' => 'Block ice for catering',
                'user_id' => $employee->id,
            ],
            [
                'product_id' => $products->first()->id ?? 1,
                'customer_id' => $walkInCustomers->first()?->id ?? $customers->first()->id ?? 1,
                'sale_date' => now()->subDays(2)->toDateString(),
                'sale_type' => 'retail',
                'quantity' => 20.00,
                'unit_price' => 25.00,
                'payment_status' => 'paid',
                'notes' => 'Walk-in retail order',
                'user_id' => $owner->id,
            ],
            [
                'product_id' => $products->get(2)?->id ?? $products->first()->id ?? 1,
                'customer_id' => $wholesaleCustomers->get(2)?->id ?? $wholesaleCustomers->first()?->id ?? $customers->first()->id ?? 1,
                'sale_date' => now()->subDays(1)->toDateString(),
                'sale_type' => 'wholesale',
                'quantity' => 75.00,
                'unit_price' => 15.00,
                'payment_status' => 'pending',
                'notes' => 'Crushed ice wholesale',
                'user_id' => $employee->id,
            ],
            [
                'product_id' => $products->get(3)?->id ?? $products->first()->id ?? 1,
                'customer_id' => $walkInCustomers->get(1)?->id ?? $walkInCustomers->first()?->id ?? $customers->first()->id ?? 1,
                'sale_date' => now()->toDateString(),
                'sale_type' => 'retail',
                'quantity' => 30.00,
                'unit_price' => 20.00,
                'payment_status' => 'paid',
                'notes' => 'Flake ice retail',
                'user_id' => $owner->id,
            ],
            [
                'product_id' => $products->first()->id ?? 1,
                'customer_id' => $wholesaleCustomers->get(3)?->id ?? $wholesaleCustomers->first()?->id ?? $customers->first()->id ?? 1,
                'sale_date' => now()->toDateString(),
                'sale_type' => 'wholesale',
                'quantity' => 150.00,
                'unit_price' => 25.00,
                'payment_status' => 'paid',
                'notes' => 'Large wholesale daily order',
                'user_id' => $employee->id,
            ],
        ];

        foreach ($sales as $sale) {
            DB::transaction(function () use ($sale, $inventoryService) {
                $saleRecord = Sale::create([
                    'sale_number' => 'SAL-'.now()->format('YmdHis').'-'.rand(100, 999),
                    ...$sale,
                    'total_amount' => $sale['quantity'] * $sale['unit_price'],
                ]);

                try {
                    $inventoryService->deductStock(
                        (float) $saleRecord->quantity,
                        'sale',
                        $saleRecord->id,
                        $saleRecord->user_id,
                        'Seeded sale'
                    );
                } catch (\RuntimeException $e) {
                    // If stock is insufficient, just log it
                    $this->command->info('Stock insufficient for sale #'.$saleRecord->id.': '.$e->getMessage());
                }
            });
        }
    }
}
