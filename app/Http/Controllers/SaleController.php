<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
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
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();
                $quantity = (float) $data['quantity'];
                $unitPrice = (float) $data['unit_price'];

                $sale = Sale::create([
                    'sale_number' => 'SAL-'.now()->format('YmdHis').'-'.rand(100, 999),
                    'product_id' => $data['product_id'] ?? null,
                    'customer_id' => $data['customer_id'] ?? null,
                    'sale_date' => $data['sale_date'],
                    'sale_type' => $data['sale_type'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $quantity * $unitPrice,
                    'payment_status' => $data['payment_status'],
                    'notes' => $data['notes'] ?? null,
                    'user_id' => Auth::id(),
                ]);

                $this->inventoryService->deductStock($quantity, 'sale', $sale->id, Auth::id(), 'Sale stock deduction');
                ActivityLogService::log(Auth::id(), 'create', 'sales', 'Created sale #'.$sale->id, $request);
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['quantity' => $exception->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(Sale $sale): View
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $sale) {
                $data = $request->validated();
                $newQuantity = (float) $data['quantity'];
                $unitPrice = (float) $data['unit_price'];
                $oldQuantity = (float) $sale->quantity;

                $this->inventoryService->addStock($oldQuantity, 'sale_update_reversal', $sale->id, Auth::id(), 'Reversed previous sale quantity before update');
                $this->inventoryService->deductStock($newQuantity, 'sale_update', $sale->id, Auth::id(), 'Applied updated sale quantity');

                $sale->update([
                    'product_id' => $data['product_id'] ?? null,
                    'customer_id' => $data['customer_id'] ?? null,
                    'sale_date' => $data['sale_date'],
                    'sale_type' => $data['sale_type'],
                    'quantity' => $newQuantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $newQuantity * $unitPrice,
                    'payment_status' => $data['payment_status'],
                    'notes' => $data['notes'] ?? null,
                ]);

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
            $this->inventoryService->addStock((float) $sale->quantity, 'sale_delete_reversal', $sale->id, Auth::id(), 'Deleted sale stock restored');
            $id = $sale->id;
            $sale->delete();
            ActivityLogService::log(Auth::id(), 'delete', 'sales', 'Deleted sale #'.$id, $request);
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }
}
