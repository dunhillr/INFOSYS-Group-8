<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Sale;
use App\Models\SystemNotification;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Delivery::with(['sale.saleItems.product', 'customer', 'vehicle', 'driver', 'assigner', 'deliverer', 'logs'])->latest();

        // Broad Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('sale', function($sq) use ($search) {
                    $sq->where('sale_number', 'like', "%{$search}%");
                })
                ->orWhereHas('customer', function($cq) use ($search) {
                    $cq->where('customer_name', 'like', "%{$search}%");
                })
                ->orWhereHas('vehicle', function($vq) use ($search) {
                    $vq->where('plate_number', 'like', "%{$search}%")
                       ->orWhere('vehicle_name', 'like', "%{$search}%");
                })
                ->orWhereHas('driver', function($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Date Range Filter (Supports Flatpickr Range)
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $start = \Carbon\Carbon::parse($dates[0], 'Asia/Manila')->startOfDay()->setTimezone('UTC');
                $end   = \Carbon\Carbon::parse($dates[1], 'Asia/Manila')->endOfDay()->setTimezone('UTC');
                $query->whereBetween('delivery_date', [$start, $end]);
            } else {
                $date = \Carbon\Carbon::parse($dates[0], 'Asia/Manila')->startOfDay()->setTimezone('UTC');
                $query->whereDate('delivery_date', $date);
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $start = \Carbon\Carbon::parse($request->start_date, 'Asia/Manila')->startOfDay()->setTimezone('UTC');
            $end   = \Carbon\Carbon::parse($request->end_date, 'Asia/Manila')->endOfDay()->setTimezone('UTC');
            $query->whereBetween('delivery_date', [$start, $end]);
        }

        $deliveries = $query->paginate(10)->withQueryString();
        $vehicles   = Vehicle::orderBy('vehicle_name')->get();

        return view('deliveries.index', compact('deliveries', 'vehicles'));
    }

    public function create(): View
    {
        $sales    = Sale::with('customer')->where('sale_type', 'wholesale')->latest()->get();
        $customers = Customer::orderBy('customer_name')->get();
        $vehicles  = Vehicle::orderBy('vehicle_name')->get();
        $drivers   = User::where('user_type', 'driver')->where('is_active', true)->orderBy('name')->get();
        return view('deliveries.create', compact('sales', 'customers', 'vehicles', 'drivers'));
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
        if (!$delivery->is_opened) {
            $delivery->update(['is_opened' => true]);
        }

        $delivery->load('logs');
        $sales     = Sale::with('customer')->where('sale_type', 'wholesale')->latest()->get();
        $customers = Customer::orderBy('customer_name')->get();
        $vehicles  = Vehicle::orderBy('vehicle_name')->get();
        $drivers   = User::where('user_type', 'driver')->where('is_active', true)->orderBy('name')->get();
        return view('deliveries.edit', compact('delivery', 'sales', 'customers', 'vehicles', 'drivers'));
    }

    public function update(UpdateDeliveryRequest $request, Delivery $delivery): RedirectResponse
    {
        $data      = $request->validated();
        $oldStatus = $delivery->status;

        $delivery->update([
            'status'       => $data['status'],
            'notes'        => $data['notes'],
            'delivered_by' => $data['status'] === 'delivered' ? Auth::id() : $delivery->delivered_by,
        ]);

        // Create log if status changed or notes provided
        if ($oldStatus !== $data['status'] || !empty($data['notes'])) {
            $delivery->logs()->create([
                'status' => $data['status'],
                'notes'  => $data['notes'],
            ]);
        }

        ActivityLogService::log(Auth::id(), 'update', 'deliveries', 'Updated delivery #'.$delivery->id, $request);

        if ($oldStatus !== $data['status']) {
            SystemNotification::notifyUsers(
                'delivery_update',
                'Delivery Status Updated',
                'Delivery #'.$delivery->id.' status changed to '.$data['status'].'.'
            );

            $this->syncVehicleStatus($delivery, $data['status']);
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery updated successfully.');
    }

    public function updateStatus(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'status'           => ['required', 'in:pending,out_for_delivery,delivered,cancelled'],
            'proof_of_delivery'=> ['nullable', 'image', 'max:5120'],
            'notes'            => ['nullable', 'string'],
        ]);

        $status    = $validated['status'];
        $oldStatus = $delivery->status;

        $updateData = [
            'status'       => $status,
            'delivered_by' => $status === 'delivered' ? Auth::id() : $delivery->delivered_by,
        ];

        // Handle Proof of Delivery upload
        if ($request->hasFile('proof_of_delivery')) {
            $path = $request->file('proof_of_delivery')->store('proof_of_delivery', 'public');
            $updateData['proof_of_delivery'] = $path;
        }

        $delivery->update($updateData);

        if ($oldStatus !== $status) {
            $logNote = $validated['notes'] ?? 'Status updated via quick action.';
            $delivery->logs()->create([
                'status' => $status,
                'notes'  => $logNote,
            ]);

            ActivityLogService::log(Auth::id(), 'update', 'deliveries', 'Updated status of delivery #'.$delivery->id.' to '.$status, $request);

            SystemNotification::notifyUsers(
                'delivery_update',
                'Delivery Status Updated',
                'Delivery #'.$delivery->id.' status changed to '.$status.'.'
            );

            $this->syncVehicleStatus($delivery, $status);
        }

        return back()->with('success', 'Delivery status updated successfully.');
    }

    public function destroy(Request $request, Delivery $delivery): RedirectResponse
    {
        $delivery->delete();
        ActivityLogService::log(Auth::id(), 'delete', 'deliveries', 'Deleted delivery #'.$delivery->id, $request);
        return redirect()->route('deliveries.index')->with('success', 'Delivery deleted successfully.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function syncVehicleStatus(Delivery $delivery, string $newStatus): void
    {
        if (! $delivery->vehicle_id) return;

        if ($newStatus === 'out_for_delivery') {
            $delivery->vehicle->update(['status' => 'in_transit']);
        } elseif (in_array($newStatus, ['delivered', 'cancelled'])) {
            $delivery->vehicle->update(['status' => 'available']);
        }
    }
}
