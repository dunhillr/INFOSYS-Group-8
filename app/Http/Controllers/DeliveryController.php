<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Sale;
use App\Models\Vehicle;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(): View
    {
        $deliveries = Delivery::with(['sale', 'customer', 'vehicle', 'assigner', 'deliverer'])->latest()->paginate(10);
        return view('deliveries.index', compact('deliveries'));
    }

    public function create(): View
    {
        $sales = Sale::with('customer')->where('sale_type', 'wholesale')->latest()->get();
        $customers = Customer::orderBy('customer_name')->get();
        $vehicles = Vehicle::orderBy('vehicle_name')->get();
        return view('deliveries.create', compact('sales', 'customers', 'vehicles'));
    }

    public function store(StoreDeliveryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['vehicle_id'])) {
            $conflict = Delivery::query()
                ->where('vehicle_id', $data['vehicle_id'])
                ->where('delivery_date', $data['delivery_date'])
                ->where('delivery_time', $data['delivery_time'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($conflict) {
                return back()->withInput()->withErrors(['vehicle_id' => 'Selected vehicle already has a delivery at the same date and time.']);
            }
        }

        $delivery = Delivery::create([
            ...$data,
            'assigned_by' => Auth::id(),
            'delivered_by' => $data['status'] === 'delivered' ? Auth::id() : null,
        ]);

        ActivityLogService::log(Auth::id(), 'create', 'deliveries', 'Created delivery #'.$delivery->id, $request);

        return redirect()->route('deliveries.index')->with('success', 'Delivery created successfully.');
    }

    public function edit(Delivery $delivery): View
    {
        $sales = Sale::with('customer')->where('sale_type', 'wholesale')->latest()->get();
        $customers = Customer::orderBy('customer_name')->get();
        $vehicles = Vehicle::orderBy('vehicle_name')->get();
        return view('deliveries.edit', compact('delivery', 'sales', 'customers', 'vehicles'));
    }

    public function update(UpdateDeliveryRequest $request, Delivery $delivery): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['vehicle_id'])) {
            $conflict = Delivery::query()
                ->where('vehicle_id', $data['vehicle_id'])
                ->where('delivery_date', $data['delivery_date'])
                ->where('delivery_time', $data['delivery_time'])
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $delivery->id)
                ->exists();

            if ($conflict) {
                return back()->withInput()->withErrors(['vehicle_id' => 'Selected vehicle already has a delivery at the same date and time.']);
            }
        }

        $delivery->update([
            ...$data,
            'delivered_by' => $data['status'] === 'delivered' ? Auth::id() : null,
        ]);

        ActivityLogService::log(Auth::id(), 'update', 'deliveries', 'Updated delivery #'.$delivery->id, $request);

        return redirect()->route('deliveries.index')->with('success', 'Delivery updated successfully.');
    }

    public function destroy(Request $request, Delivery $delivery): RedirectResponse
    {
        $delivery->delete();
        ActivityLogService::log(Auth::id(), 'delete', 'deliveries', 'Deleted delivery #'.$delivery->id, $request);
        return redirect()->route('deliveries.index')->with('success', 'Delivery deleted successfully.');
    }
}
