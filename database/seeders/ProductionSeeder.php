<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Production;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('username', 'bobongowner')->first() ?? User::where('username', 'owner')->first();
        $bea = User::where('username', 'beatoos')->first() ?? $owner;
        $vengie = User::where('username', 'vengiecabanilla')->first() ?? $owner;

        $inventoryService = app(InventoryService::class);

        // Get products by name
        $tubeIce = Product::where('product_name', 'Tube Ice')->first();
        $blockIce = Product::where('product_name', 'Block Ice')->first();
        $crushedIce = Product::where('product_name', 'Crushed Ice')->first();
        $flakeIce = Product::where('product_name', 'Flake Ice')->first();
        $dryIce = Product::where('product_name', 'Dry Ice')->first();

        $productions = [
            [
                'production_date' => now()->subDays(5)->toDateString(),
                'batch_reference' => 'BATCH-2026-001',
                'product_id' => $tubeIce?->id,
                'quantity_produced' => 500.00,
                'remarks' => 'Initial production run - Tube Ice',
                'user_id' => $owner->id,
            ],
            [
                'production_date' => now()->subDays(4)->toDateString(),
                'batch_reference' => 'BATCH-2026-002',
                'product_id' => $blockIce?->id,
                'quantity_produced' => 300.00,
                'remarks' => 'Block Ice production',
                'user_id' => $bea->id,
            ],
            [
                'production_date' => now()->subDays(3)->toDateString(),
                'batch_reference' => 'BATCH-2026-003',
                'product_id' => $crushedIce?->id,
                'quantity_produced' => 250.00,
                'remarks' => 'Crushed Ice batch',
                'user_id' => $vengie->id,
            ],
            [
                'production_date' => now()->subDays(2)->toDateString(),
                'batch_reference' => 'BATCH-2026-004',
                'product_id' => $flakeIce?->id,
                'quantity_produced' => 400.00,
                'remarks' => 'Flake Ice production',
                'user_id' => $bea->id,
            ],
            [
                'production_date' => now()->subDays(1)->toDateString(),
                'batch_reference' => 'BATCH-2026-005',
                'product_id' => $tubeIce?->id,
                'quantity_produced' => 600.00,
                'remarks' => 'High volume Tube Ice production',
                'user_id' => $owner->id,
            ],
            [
                'production_date' => now()->toDateString(),
                'batch_reference' => 'BATCH-2026-006',
                'product_id' => $crushedIce?->id,
                'quantity_produced' => 450.00,
                'remarks' => 'Today\'s Crushed Ice production run',
                'user_id' => $vengie->id,
            ],
            [
                'production_date' => now()->toDateString(),
                'batch_reference' => 'BATCH-2026-007',
                'product_id' => $blockIce?->id,
                'quantity_produced' => 200.00,
                'remarks' => 'Today\'s Block Ice production run',
                'user_id' => $bea->id,
            ],
            [
                'production_date' => now()->toDateString(),
                'batch_reference' => 'BATCH-2026-008',
                'product_id' => $dryIce?->id,
                'quantity_produced' => 150.00,
                'remarks' => 'Dry Ice production',
                'user_id' => $owner->id,
            ],
        ];

        foreach ($productions as $production) {
            DB::transaction(function () use ($production, $inventoryService) {
                $prod = Production::create($production);
                $inventoryService->addStock(
                    (float) $prod->quantity_produced,
                    'production',
                    $prod->id,
                    $prod->user_id,
                    'Seeded production',
                    $prod->product_id
                );
            });
        }
    }
}
