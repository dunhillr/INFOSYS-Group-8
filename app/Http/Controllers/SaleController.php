<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SystemNotification;
use App\Models\Vehicle;
use App\Services\ActivityLogService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class SaleController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function index(): View
    {
        $sales = Sale::with(['product', 'customer', 'user'])->latest()->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $customers   = Customer::orderBy('customer_name')->get();
        $products    = Product::where('is_active', true)->orderBy('product_name')->get();
        $vehicles    = Vehicle::orderBy('vehicle_name')->get();
        $inventories = Inventory::whereNotNull('product_id')->get()->keyBy('product_id');
        return view('sales.create', compact('customers', 'products', 'vehicles', 'inventories'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();
                $quantity   = (float) $data['quantity'];
                $unitPrice  = (float) $data['unit_price'];
                $productId  = $data['product_id'] ?? null;

                $subtotal       = $quantity * $unitPrice;
                $discountType   = $data['discount_type'] ?? null;
                $discountAmount = (float) ($data['discount_amount'] ?? 0);

                // Compute discount value
                $discountValue = 0;
                if ($discountType === 'percent') {
                    $discountValue = $subtotal * ($discountAmount / 100);
                } elseif ($discountType === 'fixed') {
                    $discountValue = $discountAmount;
                }

                $totalAmount   = max(0, $subtotal - $discountValue);
                $paymentStatus = $data['payment_status'];
                $amountPaid    = 0;
                $balanceDue    = $totalAmount;

                $amountTendered = isset($data['amount_tendered']) ? (float) $data['amount_tendered'] : null;
                $changeAmount   = null;

                if ($paymentStatus === 'paid') {
                    $amountPaid = $totalAmount;
                    $balanceDue = 0;
                    if ($amountTendered !== null) {
                        $changeAmount = max(0, $amountTendered - $totalAmount);
                    }
                } elseif ($paymentStatus === 'partial') {
                    $amountPaid = (float) ($data['amount_paid'] ?? 0);
                    $balanceDue = max(0, $totalAmount - $amountPaid);
                    if ($amountTendered !== null) {
                        $changeAmount = max(0, $amountTendered - $amountPaid);
                    }
                }

                // Generate Reference Number
                $today = now()->format('Ymd');
                $latestSale = Sale::where('sale_number', 'like', "SALE-{$today}-%")->orderBy('id', 'desc')->first();
                $sequence = 1;
                if ($latestSale && preg_match('/SALE-' . $today . '-(\d+)/', $latestSale->sale_number, $matches)) {
                    $sequence = intval($matches[1]) + 1;
                }
                $saleNumber = 'SALE-' . $today . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

                $sale = Sale::create([
                    'sale_number'     => $saleNumber,
                    'product_id'      => $productId,
                    'customer_id'     => $data['customer_id'] ?? null,
                    'vehicle_id'      => $data['vehicle_id'] ?? null,
                    'sale_date'       => now(),
                    'sale_type'       => $data['sale_type'] ?? 'retail',
                    'delivery_type'   => $data['delivery_type'],
                    'quantity'        => $quantity,
                    'unit_price'      => $unitPrice,
                    'discount_type'   => $discountType,
                    'discount_amount' => $discountAmount,
                    'total_amount'    => $totalAmount,
                    'payment_status'  => $paymentStatus,
                    'amount_paid'     => $amountPaid,
                    'amount_tendered' => $amountTendered,
                    'change_amount'   => $changeAmount,
                    'balance_due'     => $balanceDue,
                    'payment_method'  => $data['payment_method'] ?? null,
                    'notes'           => $data['notes'] ?? null,
                    'user_id'         => Auth::id(),
                ]);

                // Update vehicle status and auto-create Delivery only for non-walk-in types
                if (($data['vehicle_id'] ?? null) && $data['delivery_type'] !== 'walk_in') {
                    Vehicle::where('id', $data['vehicle_id'])->update(['status' => 'in_use']);

                    // Automatically create a Delivery record
                    $customer = Customer::find($data['customer_id'] ?? null);
                    \App\Models\Delivery::create([
                        'sale_id'       => $sale->id,
                        'customer_id'   => $data['customer_id'],
                        'vehicle_id'    => $data['vehicle_id'],
                        'destination'   => $customer && $customer->customer_address ? $customer->customer_address : 'Not specified',
                        'delivery_date' => now()->toDateString(),
                        'delivery_time' => now()->format('H:i:s'),
                        'status'        => 'pending',
                        'assigned_by'   => Auth::id(),
                    ]);
                }

                $this->inventoryService->deductStock($quantity, 'sale', $sale->id, Auth::id(), 'Sale stock deduction', $productId ? (int) $productId : null);
                ActivityLogService::log(Auth::id(), 'create', 'sales', 'Created sale #'.$sale->id, $request);

                SystemNotification::notifyUsers(
                    'new_sale',
                    'New Sale Recorded',
                    'Sale #'.$sale->sale_number.' was recorded for total amount ₱'.number_format($totalAmount, 2).'.'
                );
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['quantity' => $exception->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(Sale $sale): View
    {
        $customers   = Customer::orderBy('customer_name')->get();
        $products    = Product::where('is_active', true)->orderBy('product_name')->get();
        $vehicles    = Vehicle::orderBy('vehicle_name')->get();
        $inventories = Inventory::whereNotNull('product_id')->get()->keyBy('product_id');
        return view('sales.edit', compact('sale', 'customers', 'products', 'vehicles', 'inventories'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $sale) {
                $data = $request->validated();
                $newQuantity = (float) $data['quantity'];
                $unitPrice = (float) $data['unit_price'];
                $oldQuantity = (float) $sale->quantity;
                $oldVehicleId = $sale->vehicle_id;
                $oldProductId = $sale->product_id;
                $newProductId = $data['product_id'] ?? null;

                // Reverse old stock, apply new stock
                $this->inventoryService->addStock($oldQuantity, 'sale_update_reversal', $sale->id, Auth::id(), 'Reversed previous sale quantity before update', $oldProductId ? (int) $oldProductId : null);
                $this->inventoryService->deductStock($newQuantity, 'sale_update', $sale->id, Auth::id(), 'Applied updated sale quantity', $newProductId ? (int) $newProductId : null);

                $totalAmount = $newQuantity * $unitPrice;
                $paymentStatus = $data['payment_status'];
                $amountPaid = 0;
                $balanceDue = $totalAmount;

                if ($paymentStatus === 'paid') {
                    $amountPaid = $totalAmount;
                    $balanceDue = 0;
                } elseif ($paymentStatus === 'partial') {
                    $amountPaid = (float) ($data['amount_paid'] ?? 0);
                    $balanceDue = max(0, $totalAmount - $amountPaid);
                }

                $sale->update([
                    'product_id' => $newProductId,
                    'customer_id' => $data['customer_id'] ?? null,
                    'vehicle_id' => $data['vehicle_id'] ?? null,
                    'sale_date' => $data['sale_date'],
                    'sale_type' => $data['sale_type'],
                    'quantity' => $newQuantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $totalAmount,
                    'payment_status' => $paymentStatus,
                    'amount_paid' => $amountPaid,
                    'balance_due' => $balanceDue,
                    'payment_method' => $data['payment_method'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                // Handle vehicle status changes
                $newVehicleId = $data['vehicle_id'] ?? null;

                // If vehicle assignment was changed
                if ($oldVehicleId !== $newVehicleId) {
                    // Revert old vehicle status back to "available" if it was assigned
                    if ($oldVehicleId) {
                        Vehicle::where('id', $oldVehicleId)->update(['status' => 'available']);
                    }

                    // Update new vehicle status to "in_use" if a vehicle is now assigned
                    if ($newVehicleId) {
                        Vehicle::where('id', $newVehicleId)->update(['status' => 'in_use']);
                    }
                }

                ActivityLogService::log(Auth::id(), 'update', 'sales', 'Updated sale #'.$sale->id, $request);
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['quantity' => $exception->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Request $request, Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($request, $sale) {
            $this->inventoryService->addStock((float) $sale->quantity, 'sale_delete_reversal', $sale->id, Auth::id(), 'Deleted sale stock restored', $sale->product_id ? (int) $sale->product_id : null);
            
            // Revert vehicle status back to "available" if a vehicle was assigned
            if ($sale->vehicle_id) {
                Vehicle::where('id', $sale->vehicle_id)->update(['status' => 'available']);
            }

            $id = $sale->id;
            $sale->delete();
            ActivityLogService::log(Auth::id(), 'delete', 'sales', 'Deleted sale #'.$id, $request);
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    public function history(): View
    {
        $sales = Sale::with(['product', 'customer', 'vehicle', 'user'])
            ->orderByDesc('sale_date')
            ->paginate(15);
        return view('sales.history', compact('sales'));
    }
}
