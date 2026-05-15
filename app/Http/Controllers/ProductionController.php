<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Production;
use App\Models\SystemNotification;
use App\Services\ActivityLogService;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class ProductionController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function index(): View
    {
        $productions = Production::with(['user', 'product', 'parentProduct'])->latest()->paginate(10);

        // Get available stock per product for display
        $inventories = Inventory::with('product')
            ->whereNotNull('product_id')
            ->get()
            ->keyBy('product_id');

        return view('productions.index', compact('productions', 'inventories'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->with('parentProduct')->orderBy('product_name')->get();
        return view('productions.create', compact('products'));
    }

    /**
     * Returns the parent product info for a given product (used by the form via AJAX).
     */
    public function getProductParent(Product $product): JsonResponse
    {
        $product->load('parentProduct');
        return response()->json([
            'has_parent'  => (bool) $product->parent_product_id,
            'parent_id'   => $product->parent_product_id,
            'parent_name' => $product->parentProduct?->product_name,
        ]);
    }

    public function store(StoreProductionRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();

            $production = Production::create([
                ...$data,
                'user_id' => Auth::id(),
            ]);

            // Add output product stock
            $this->inventoryService->addStock(
                (float) $production->quantity_produced,
                'production',
                $production->id,
                Auth::id(),
                'Production stock added',
                $production->product_id
            );

            // Deduct parent/raw-material stock (e.g. Block Ice consumed to make Crushed Ice)
            if ($production->parent_product_id && $production->parent_quantity_used > 0) {
                $this->inventoryService->deductStock(
                    (float) $production->parent_quantity_used,
                    'production_material',
                    $production->id,
                    Auth::id(),
                    'Raw material consumed for production #'.$production->id,
                    $production->parent_product_id
                );
            }

            ActivityLogService::log(Auth::id(), 'create', 'productions', 'Created production #'.$production->id, $request);

            SystemNotification::notifyUsers(
                'new_production',
                'Production Alert',
                'New production of '.$production->quantity_produced.' recorded.'
            );
        });

        return redirect()->route('productions.index')->with('success', 'Production recorded successfully.');
    }

    public function edit(Production $production): View
    {
        $products = Product::where('is_active', true)->with('parentProduct')->orderBy('product_name')->get();
        return view('productions.edit', compact('production', 'products'));
    }

    public function update(UpdateProductionRequest $request, Production $production): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $production) {
                $oldQuantity  = (float) $production->quantity_produced;
                $oldProductId = $production->product_id;
                $oldParentId  = $production->parent_product_id;
                $oldParentQty = (float) ($production->parent_quantity_used ?? 0);

                $data         = $request->validated();
                $newQuantity  = (float) $data['quantity_produced'];
                $newProductId = (int) $data['product_id'];
                $newParentId  = $data['parent_product_id'] ?? null;
                $newParentQty = (float) ($data['parent_quantity_used'] ?? 0);

                $production->update($data);

                // ── Output product stock adjustment ──────────────────────────
                if ($oldProductId !== $newProductId) {
                    $this->inventoryService->deductStock($oldQuantity, 'production_adjustment', $production->id, Auth::id(), 'Production product changed - reversed', $oldProductId);
                    $this->inventoryService->addStock($newQuantity, 'production_adjustment', $production->id, Auth::id(), 'Production product changed - added', $newProductId);
                } else {
                    if ($newQuantity > $oldQuantity) {
                        $this->inventoryService->addStock($newQuantity - $oldQuantity, 'production_adjustment', $production->id, Auth::id(), 'Production quantity increased', $newProductId);
                    } elseif ($newQuantity < $oldQuantity) {
                        $this->inventoryService->deductStock($oldQuantity - $newQuantity, 'production_adjustment', $production->id, Auth::id(), 'Production quantity reduced', $newProductId);
                    }
                }

                // ── Parent/raw-material stock adjustment ─────────────────────
                // Restore old parent consumption first
                if ($oldParentId && $oldParentQty > 0) {
                    $this->inventoryService->addStock($oldParentQty, 'production_adjustment', $production->id, Auth::id(), 'Raw material restored - production updated', $oldParentId);
                }
                // Apply new parent consumption
                if ($newParentId && $newParentQty > 0) {
                    $this->inventoryService->deductStock($newParentQty, 'production_adjustment', $production->id, Auth::id(), 'Raw material consumed - production updated', $newParentId);
                }

                ActivityLogService::log(Auth::id(), 'update', 'productions', 'Updated production #'.$production->id, $request);
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['quantity_produced' => $exception->getMessage()]);
        }

        return redirect()->route('productions.index')->with('success', 'Production updated successfully.');
    }

    public function destroy(Request $request, Production $production): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $production) {
                // Reverse output product stock
                $this->inventoryService->deductStock(
                    (float) $production->quantity_produced,
                    'production_delete',
                    $production->id,
                    Auth::id(),
                    'Deleted production stock reversal',
                    $production->product_id
                );

                // Restore parent/raw-material stock that was consumed
                if ($production->parent_product_id && $production->parent_quantity_used > 0) {
                    $this->inventoryService->addStock(
                        (float) $production->parent_quantity_used,
                        'production_delete',
                        $production->id,
                        Auth::id(),
                        'Raw material restored - production deleted',
                        $production->parent_product_id
                    );
                }

                $id = $production->id;
                $production->delete();
                ActivityLogService::log(Auth::id(), 'delete', 'productions', 'Deleted production #'.$id, $request);
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['production' => 'Cannot delete this production because current stock is too low to reverse it.']);
        }

        return redirect()->route('productions.index')->with('success', 'Production deleted successfully.');
    }
}
