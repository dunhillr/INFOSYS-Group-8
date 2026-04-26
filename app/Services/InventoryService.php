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
    /**
     * Get inventory for a specific product, or the global inventory if no product specified.
     */
    public function getInventoryForProduct(?int $productId = null): Inventory
    {
        if ($productId) {
            return Inventory::firstOrCreate(
                ['product_id' => $productId],
                ['current_stock' => 0, 'low_stock_threshold' => 100]
            );
        }

        return Inventory::firstOrCreate([], ['current_stock' => 0, 'low_stock_threshold' => 100]);
    }

    /**
     * Backward-compatible alias.
     */
    public function getMainInventory(): Inventory
    {
        return $this->getInventoryForProduct();
    }

    public function addStock(float $quantity, string $referenceType, int $referenceId, ?int $userId = null, ?string $remarks = null, ?int $productId = null): Inventory
    {
        return DB::transaction(function () use ($quantity, $referenceType, $referenceId, $userId, $remarks, $productId) {
            $inventory = $this->getInventoryForProduct($productId);
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

    public function deductStock(float $quantity, string $referenceType, int $referenceId, ?int $userId = null, ?string $remarks = null, ?int $productId = null): Inventory
    {
        return DB::transaction(function () use ($quantity, $referenceType, $referenceId, $userId, $remarks, $productId) {
            $inventory = $this->getInventoryForProduct($productId);
            $before = (float) $inventory->current_stock;

            if ($quantity > $before) {
                $productName = $inventory->product?->product_name ?? 'Ice';
                throw new RuntimeException("Insufficient stock for {$productName}. Available: {$before}, Requested: {$quantity}");
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

        $productName = $inventory->product?->product_name ?? 'Ice';

        foreach (User::query()->where('user_type', 'owner')->get() as $owner) {
            SystemNotification::create([
                'user_id' => $owner->id,
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'message' => "{$productName} inventory is below the low stock threshold. Current stock: {$inventory->current_stock}",
            ]);
        }
    }
}