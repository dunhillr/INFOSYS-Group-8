<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function getMainInventory(): Inventory
    {
        return Inventory::firstOrCreate([], ['current_stock' => 0, 'low_stock_threshold' => 100]);
    }

    public function addStock(float $quantity, string $referenceType, int $referenceId, ?int $userId = null, ?string $remarks = null): Inventory
    {
        return DB::transaction(function () use ($quantity, $referenceType, $referenceId, $userId, $remarks) {
            $inventory = $this->getMainInventory();
            $before = (float) $inventory->current_stock;
            $after = $before + $quantity;

            $inventory->update(['current_stock' => $after, 'updated_by' => $userId]);

            InventoryLog::create([
                'inventory_id' => $inventory->id,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'movement_type' => 'in',
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'remarks' => $remarks,
                'created_by' => $userId,
            ]);

            $this->notifyLowStockIfNeeded($inventory);

            return $inventory->fresh();
        });
    }

    public function deductStock(float $quantity, string $referenceType, int $referenceId, ?int $userId = null, ?string $remarks = null): Inventory
    {
        return DB::transaction(function () use ($quantity, $referenceType, $referenceId, $userId, $remarks) {
            $inventory = $this->getMainInventory();
            $before = (float) $inventory->current_stock;

            if ($quantity > $before) {
                throw new RuntimeException('Insufficient stock.');
            }

            $after = $before - $quantity;

            $inventory->update(['current_stock' => $after, 'updated_by' => $userId]);

            InventoryLog::create([
                'inventory_id' => $inventory->id,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'movement_type' => 'out',
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'remarks' => $remarks,
                'created_by' => $userId,
            ]);

            $this->notifyLowStockIfNeeded($inventory);

            return $inventory->fresh();
        });
    }

    protected function notifyLowStockIfNeeded(Inventory $inventory): void
    {
        if ((float) $inventory->current_stock > (float) $inventory->low_stock_threshold) {
            return;
        }

        foreach (User::query()->where('user_type', 'owner')->get() as $owner) {
            SystemNotification::create([
                'user_id' => $owner->id,
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'message' => 'Inventory is below the low stock threshold. Current stock: '.$inventory->current_stock,
            ]);
        }
    }
}