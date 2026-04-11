<?php

namespace Database\Seeders;

use App\Models\Production;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('username', 'owner')->first();
        $inventoryService = app(InventoryService::class);

        $productions = [
            [
                'production_date' => now()->subDays(5)->toDateString(),
                'batch_reference' => 'BATCH-2026-001',
                'quantity_produced' => 500.00,
                'remarks' => 'Initial production run - Tube Ice',
                'user_id' => $owner->id,
            ],
            [
                'production_date' => now()->subDays(4)->toDateString(),
                'batch_reference' => 'BATCH-2026-002',
                'quantity_produced' => 300.00,
                'remarks' => 'Block Ice production',
                'user_id' => $owner->id,
            ],
            [
                'production_date' => now()->subDays(3)->toDateString(),
                'batch_reference' => 'BATCH-2026-003',
                'quantity_produced' => 250.00,
                'remarks' => 'Crushed Ice batch',
                'user_id' => $owner->id,
            ],
            [
                'production_date' => now()->subDays(2)->toDateString(),
                'batch_reference' => 'BATCH-2026-004',
                'quantity_produced' => 400.00,
                'remarks' => 'Flake Ice production',
                'user_id' => $owner->id,
            ],
            [
                'production_date' => now()->subDays(1)->toDateString(),
                'batch_reference' => 'BATCH-2026-005',
                'quantity_produced' => 600.00,
                'remarks' => 'High volume Tube Ice production',
                'user_id' => $owner->id,
            ],
            [
                'production_date' => now()->toDateString(),
                'batch_reference' => 'BATCH-2026-006',
                'quantity_produced' => 450.00,
                'remarks' => 'Today\'s production run',
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
                    'Seeded production'
                );
            });
        }
    }
}
