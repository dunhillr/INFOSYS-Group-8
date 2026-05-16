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

    public function index(Request $request): View
    {
        $query = Sale::with(['saleItems.product', 'customer', 'user', 'vehicle'])->latest();

        // Apply Active Sales Logic: 
        // 1. Walk-ins that are NOT yet fully paid (Ongoing collections)
        // 2. OR Deliveries that are Pending or In Transit (Ongoing logistics)
        $query->where(function($q) {
            $q->where(function($sub) {
                $sub->where('delivery_type', 'walk_in')
                    ->where('payment_status', '!=', 'paid');
            })
            ->orWhereHas('delivery', function($sub) {
                $sub->whereIn('status', ['pending', 'out_for_delivery']);
            });
        });

        // Search Filter (Sale # or Customer)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        // Payment Status Filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Date Range Filter (Supports Flatpickr Range)
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $start = \Carbon\Carbon::parse($dates[0], 'Asia/Manila')->startOfDay()->setTimezone('UTC');
                $end = \Carbon\Carbon::parse($dates[1], 'Asia/Manila')->endOfDay()->setTimezone('UTC');
                $query->whereBetween('sale_date', [$start, $end]);
            } else {
                $date = \Carbon\Carbon::parse($dates[0], 'Asia/Manila')->startOfDay()->setTimezone('UTC');
                $query->whereDate('sale_date', $date);
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            // Fallback for shortcut buttons
            $start = \Carbon\Carbon::parse($request->start_date, 'Asia/Manila')->startOfDay()->setTimezone('UTC');
            $end = \Carbon\Carbon::parse($request->end_date, 'Asia/Manila')->endOfDay()->setTimezone('UTC');
            $query->whereBetween('sale_date', [$start, $end]);
        }

        $sales = $query->paginate(15)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $customers   = Customer::orderBy('customer_name')->get();
        $products    = Product::where('is_active', true)->orderBy('product_name')->get()->map(function($product) {
            $product->available_stock = $this->inventoryService->getAvailableStock($product->id);
            return $product;
        });
        $inventories = Inventory::whereNotNull('product_id')->get()->keyBy('product_id');

        // Fetch vehicles and calculate remaining capacity
        $vehicles = Vehicle::orderBy('vehicle_name')->get()->map(function ($vehicle) {
            // Calculate current load from pending/out_for_delivery deliveries
            $currentLoad = \App\Models\Delivery::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'out_for_delivery'])
                ->with('sale.saleItems.product')
                ->get()
                ->sum(function ($delivery) {
                    return $delivery->sale->saleItems->sum(function ($item) {
                        return (float) $item->quantity * (float) ($item->product->weight_kg ?? 0);
                    });
                });

            $vehicle->remaining_capacity = max(0, (float) $vehicle->capacity - $currentLoad);
            return $vehicle;
        });

        return view('sales.create', compact('customers', 'products', 'vehicles', 'inventories'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();
                
                $items = $data['items'];
                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal += ($item['quantity'] * $item['unit_price']);
                }

                $deliveryFee    = (float) ($data['delivery_fee'] ?? 0);
                $discountType   = $data['discount_type'] ?? null;
                $discountAmount = (float) ($data['discount_amount'] ?? 0);

                // Compute discount value
                $discountValue = 0;
                if ($discountType === 'percent') {
                    $discountValue = $subtotal * ($discountAmount / 100);
                } elseif ($discountType === 'fixed') {
                    $discountValue = $discountAmount;
                }

                $totalAmount   = max(0, $subtotal + $deliveryFee - $discountValue);
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

                // Check Vehicle Capacity if a vehicle is assigned
                if (!empty($data['vehicle_id'])) {
                    $vehicle = Vehicle::findOrFail($data['vehicle_id']);
                    $totalWeight = 0;
                    foreach ($items as $item) {
                        $product = Product::findOrFail($item['product_id']);
                        $totalWeight += ($item['quantity'] * ($product->weight_kg ?? 0));
                    }

                    if ($totalWeight > $vehicle->capacity) {
                        throw new RuntimeException("Vehicle capacity exceeded! Total weight: " . number_format($totalWeight, 2) . "kg, Vehicle capacity: " . number_format($vehicle->capacity, 2) . "kg");
                    }
                }

                $sale = Sale::create([
                    'sale_number'     => $saleNumber,
                    'customer_id'     => $data['customer_id'] ?? null,
                    'vehicle_id'      => $data['vehicle_id'] ?? null,
                    'sale_date'       => now(),
                    'sale_type'       => $data['sale_type'] ?? 'retail',
                    'delivery_type'   => $data['delivery_type'],
                    'delivery_fee'    => $deliveryFee,
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

                foreach ($items as $item) {
                    $itemSubtotal = $item['quantity'] * $item['unit_price'];
                    $sale->saleItems()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $itemSubtotal,
                    ]);

                    // Only deduct stock immediately for Walk-In sales
                    if ($data['delivery_type'] === 'walk_in') {
                        $this->inventoryService->deductStock($item['quantity'], 'sale', $sale->id, Auth::id(), 'Walk-in sale stock deduction', (int) $item['product_id']);
                    }
                }

                // Update vehicle status and auto-create Delivery only for non-walk-in types
                if (($data['vehicle_id'] ?? null) && $data['delivery_type'] !== 'walk_in') {
                    Vehicle::where('id', $data['vehicle_id'])->update(['status' => 'reserved']);

                    // Automatically create a Delivery record
                    $customer = Customer::find($data['customer_id'] ?? null);
                    $delivery = \App\Models\Delivery::create([
                        'sale_id'       => $sale->id,
                        'customer_id'   => $data['customer_id'],
                        'vehicle_id'    => $data['vehicle_id'],
                        'destination'   => $customer && $customer->customer_address ? $customer->customer_address : 'Not specified',
                        'delivery_date' => now()->toDateString(),
                        'delivery_time' => now()->format('H:i:s'),
                        'status'        => 'pending',
                        'assigned_by'   => Auth::id(),
                    ]);

                    $delivery->logs()->create([
                        'status' => 'pending',
                        'notes' => 'Delivery initiated from Sale #' . $sale->sale_number,
                    ]);
                }

                ActivityLogService::log(Auth::id(), 'create', 'sales', 'Created sale #'.$sale->id, $request);

                SystemNotification::notifyUsers(
                    'new_sale',
                    'New Sale Recorded',
                    'Sale #'.$sale->sale_number.' was recorded for total amount ₱'.number_format($totalAmount, 2).'.'
                );
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(Sale $sale): View
    {
        if ($sale->payment_status === 'partial') {
            return view('sales.collect_payment', compact('sale'));
        }

        $sale->load('saleItems.product');
        $customers   = Customer::orderBy('customer_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get()->map(function($product) use ($sale) {
            // Get base available stock
            $available = $this->inventoryService->getAvailableStock($product->id);
            
            // If this sale already occupies some of that stock (either reserved or deducted), add it back to "available" for editing purposes
            $currentSaleQty = $sale->saleItems->where('product_id', $product->id)->sum('quantity');
            $product->available_stock = $available + $currentSaleQty;
            
            return $product;
        });
        $inventories = Inventory::whereNotNull('product_id')->get()->keyBy('product_id');

        // Fetch vehicles and calculate remaining capacity
        $vehicles = Vehicle::orderBy('vehicle_name')->get()->map(function ($vehicle) use ($sale) {
            // Calculate current load from pending/out_for_delivery deliveries, excluding THIS sale
            $currentLoad = \App\Models\Delivery::where('vehicle_id', $vehicle->id)
                ->where('sale_id', '!=', $sale->id) // Exclude current sale
                ->whereIn('status', ['pending', 'out_for_delivery'])
                ->with('sale.saleItems.product')
                ->get()
                ->sum(function ($delivery) {
                    return $delivery->sale->saleItems->sum(function ($item) {
                        return (float) $item->quantity * (float) ($item->product->weight_kg ?? 0);
                    });
                });

            $vehicle->remaining_capacity = max(0, (float) $vehicle->capacity - $currentLoad);
            return $vehicle;
        });

        return view('sales.edit', compact('sale', 'customers', 'products', 'vehicles', 'inventories'));
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        // If coming from the Collect Payment view, handle separately
        if ($request->has('is_collect_payment')) {
            return $this->updatePayment($request, $sale);
        }

        try {
            DB::transaction(function () use ($request, $sale) {
                // Manually validate for standard edits
                $data = $request->validate((new UpdateSaleRequest())->rules());
                
                // Conditional Reverse: Only if stock was actually deducted
                $wasDeducted = ($sale->delivery_type === 'walk_in' || ($sale->delivery && $sale->delivery->status === 'delivered'));
                
                if ($wasDeducted) {
                    foreach ($sale->saleItems as $oldItem) {
                        $this->inventoryService->addStock((float) $oldItem->quantity, 'sale_update_reversal', $sale->id, Auth::id(), 'Reversed previous sale quantity before update', (int) $oldItem->product_id);
                    }
                }

                $sale->saleItems()->delete();

                $items = $data['items'];
                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal += ($item['quantity'] * $item['unit_price']);
                }

                $deliveryFee    = (float) ($data['delivery_fee'] ?? 0);
                $discountType   = $data['discount_type'] ?? null;
                $discountAmount = (float) ($data['discount_amount'] ?? 0);

                // Compute discount value
                $discountValue = 0;
                if ($discountType === 'percent') {
                    $discountValue = $subtotal * ($discountAmount / 100);
                } elseif ($discountType === 'fixed') {
                    $discountValue = $discountAmount;
                }

                $totalAmount = max(0, $subtotal + $deliveryFee - $discountValue);
                $paymentStatus = $data['payment_status'];
                $amountPaid = 0;
                $balanceDue = $totalAmount;

                // Check Vehicle Capacity if a vehicle is assigned
                if (!empty($data['vehicle_id'])) {
                    $vehicle = Vehicle::findOrFail($data['vehicle_id']);
                    $totalWeight = 0;
                    foreach ($items as $item) {
                        $product = Product::findOrFail($item['product_id']);
                        $totalWeight += ($item['quantity'] * ($product->weight_kg ?? 0));
                    }

                    if ($totalWeight > $vehicle->capacity) {
                        throw new RuntimeException("Vehicle capacity exceeded! Total weight: " . number_format($totalWeight, 2) . "kg, Vehicle capacity: " . number_format($vehicle->capacity, 2) . "kg");
                    }
                }

                if ($paymentStatus === 'paid') {
                    $amountPaid = $totalAmount;
                    $balanceDue = 0;
                } elseif ($paymentStatus === 'partial') {
                    $amountPaid = (float) ($data['amount_paid'] ?? 0);
                    $balanceDue = max(0, $totalAmount - $amountPaid);
                }

                $oldVehicleId = $sale->vehicle_id;

                $sale->update([
                    'customer_id' => $data['customer_id'] ?? null,
                    'vehicle_id' => $data['vehicle_id'] ?? null,
                    'sale_date' => $data['sale_date'] ?? $sale->sale_date,
                    'sale_type' => $data['sale_type'] ?? $sale->sale_type,
                    'delivery_type' => $data['delivery_type'],
                    'delivery_fee' => $deliveryFee,
                    'discount_type' => $discountType,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'payment_status' => $paymentStatus,
                    'amount_paid' => $amountPaid,
                    'balance_due' => $balanceDue,
                    'payment_method' => $data['payment_method'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($items as $item) {
                    $itemSubtotal = $item['quantity'] * $item['unit_price'];
                    $sale->saleItems()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $itemSubtotal,
                    ]);

                    // Conditional Deduct: Only if walk_in (deliveries deduct on completion)
                    if ($data['delivery_type'] === 'walk_in') {
                        $this->inventoryService->deductStock($item['quantity'], 'sale_update', $sale->id, Auth::id(), 'Applied updated walk-in sale quantity', (int) $item['product_id']);
                    }
                }

                // Handle vehicle status changes
                $newVehicleId = $data['vehicle_id'] ?? null;

                // If vehicle assignment was changed
                if ($oldVehicleId !== $newVehicleId) {
                    // Revert old vehicle status back to "available" if it was assigned
                    if ($oldVehicleId) {
                        Vehicle::where('id', $oldVehicleId)->update(['status' => 'available']);
                    }

                    // Update new vehicle status to "reserved" if a vehicle is now assigned
                    if ($newVehicleId && $data['delivery_type'] !== 'walk_in') {
                        Vehicle::where('id', $newVehicleId)->update(['status' => 'reserved']);
                    }
                }

                ActivityLogService::log(Auth::id(), 'update', 'sales', 'Updated sale #'.$sale->id, $request);
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function updatePayment(Request $request, Sale $sale): RedirectResponse
    {
        $request->validate([
            'new_payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        $newPayment = (float) $request->new_payment_amount;
        $totalPaid = (float) $sale->amount_paid + $newPayment;
        $balance = max(0, (float) $sale->total_amount - $totalPaid);
        
        $newStatus = ($balance <= 0) ? 'paid' : 'partial';

        $sale->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $balance,
            'payment_status' => $newStatus,
            'payment_method' => $request->payment_method, // Update with the latest method used
        ]);

        ActivityLogService::log(Auth::id(), 'update', 'sales', 'Collected payment ₱'.number_format($newPayment, 2).' for sale #'.$sale->id, $request);

        $msg = ($newStatus === 'paid') ? 'Payment completed! Sale moved to history.' : 'Payment updated. Remaining balance: ₱' . number_format($balance, 2);

        return redirect()->route('sales.index')->with('success', $msg);
    }

    public function destroy(Request $request, Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($request, $sale) {
            // Conditional Reverse: Only if stock was actually deducted
            $wasDeducted = ($sale->delivery_type === 'walk_in' || ($sale->delivery && $sale->delivery->status === 'delivered'));

            if ($wasDeducted) {
                foreach ($sale->saleItems as $item) {
                    $this->inventoryService->addStock((float) $item->quantity, 'sale_delete_reversal', $sale->id, Auth::id(), 'Deleted sale stock restored', (int) $item->product_id);
                }
            }
            
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

    public function history(Request $request): View
    {
        $query = Sale::with(['saleItems.product', 'customer', 'vehicle', 'user'])->latest('sale_date');

        // Apply Archive/History Sales Logic:
        // 1. Walk-ins that are fully PAID (Finished transactions)
        // 2. OR Deliveries that are Delivered or Cancelled (Finished logistics)
        $query->where(function($q) {
            $q->where(function($sub) {
                $sub->where('delivery_type', 'walk_in')
                    ->where('payment_status', 'paid');
            })
            ->orWhereHas('delivery', function($sub) {
                $sub->whereIn('status', ['delivered', 'cancelled']);
            });
        });

        // Search Filter (Sale # or Customer)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        // Date Range Filter (Supports Flatpickr Range)
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $start = \Carbon\Carbon::parse($dates[0], 'Asia/Manila')->startOfDay()->setTimezone('UTC');
                $end = \Carbon\Carbon::parse($dates[1], 'Asia/Manila')->endOfDay()->setTimezone('UTC');
                $query->whereBetween('sale_date', [$start, $end]);
            } else {
                // Single date selected
                $date = \Carbon\Carbon::parse($dates[0], 'Asia/Manila')->startOfDay()->setTimezone('UTC');
                $query->whereDate('sale_date', $date);
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            // Fallback for Today/This Week buttons
            $start = \Carbon\Carbon::parse($request->start_date, 'Asia/Manila')->startOfDay()->setTimezone('UTC');
            $end = \Carbon\Carbon::parse($request->end_date, 'Asia/Manila')->endOfDay()->setTimezone('UTC');
            $query->whereBetween('sale_date', [$start, $end]);
        }

        $sales = $query->paginate(15)->withQueryString();
        return view('sales.history', compact('sales'));
    }
}
