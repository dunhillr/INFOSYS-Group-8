<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Services\ActivityLogService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DriverPortalController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    /** My deliveries list (mobile view) */
    public function index(): View
    {
        $driver = Auth::user();

        $assignedVehicles = \App\Models\Vehicle::where('assigned_driver_id', $driver->id)->get();

        $deliveries = Delivery::with(['sale.saleItems.product', 'customer', 'vehicle'])
            ->whereHas('vehicle', function($q) use ($driver) {
                $q->where('assigned_driver_id', $driver->id);
            })
            ->whereIn('status', ['pending', 'out_for_delivery'])
            ->orderBy('delivery_date')
            ->orderBy('delivery_time')
            ->get();

        $completedToday = Delivery::whereHas('vehicle', function($q) use ($driver) {
                $q->where('assigned_driver_id', $driver->id);
            })
            ->where('status', 'delivered')
            ->whereDate('delivery_date', today())
            ->count();

        return view('driver.index', compact('deliveries', 'completedToday', 'assignedVehicles'));
    }

    /** Single delivery detail */
    public function show(Delivery $delivery): View
    {
        // Ensure driver can only view their own deliveries
        if (!$delivery->vehicle || $delivery->vehicle->assigned_driver_id !== Auth::id()) {
            abort(403, 'This delivery is not assigned to you.');
        }

        $delivery->load(['sale.saleItems.product', 'customer', 'vehicle', 'logs']);

        return view('driver.show', compact('delivery'));
    }

    /** Driver clicks "Start Trip" → sets ALL pending status to out_for_delivery */
    public function startTrip(Request $request): RedirectResponse
    {
        $driver = Auth::user();

        $deliveries = Delivery::whereHas('vehicle', function($q) use ($driver) {
                $q->where('assigned_driver_id', $driver->id);
            })
            ->where('status', 'pending')
            ->get();

        if ($deliveries->isEmpty()) {
            return back()->with('error', 'Wala pang naka-assign na delivery para sa iyo ngayon.');
        }

        foreach ($deliveries as $delivery) {
            $delivery->update(['status' => 'out_for_delivery']);

            $delivery->logs()->create([
                'status' => 'out_for_delivery',
                'notes'  => 'Driver started the delivery.',
            ]);

            // Mark vehicle as in_transit
            if ($delivery->vehicle_id) {
                $delivery->vehicle->update(['status' => 'in_transit']);
            }
        }

        ActivityLogService::log(Auth::id(), 'update', 'deliveries', 'Driver started trip for '.$deliveries->count().' deliveries.', $request);

        \App\Models\SystemNotification::notifyUsers(
            'delivery_update',
            'Trip Started',
            'Driver ' . $driver->name . ' has started their trip with ' . $deliveries->count() . ' deliveries.'
        );

        return redirect()->route('driver.index')->with('success', '✅ Delivery started! Stay safe.');
    }

    /** Driver clicks "Confirm Delivery" → sets status to delivered + uploads POD */
    public function confirmDelivery(Request $request, Delivery $delivery): RedirectResponse
    {
        if (!$delivery->vehicle || $delivery->vehicle->assigned_driver_id !== Auth::id()) {
            abort(403);
        }

        if ($delivery->status !== 'out_for_delivery') {
            return back()->with('error', 'Delivery must be In Transit before confirming.');
        }

        $request->validate([
            'proof_of_delivery' => ['required', 'image', 'max:10240'],
        ], [
            'proof_of_delivery.required' => 'Kailangan ng larawan ng resibo o na-deliver na yelo bilang Proof of Delivery.',
            'proof_of_delivery.image'    => 'Ang file ay dapat na larawan (JPG, PNG, etc.).',
            'proof_of_delivery.max'      => 'Ang larawan ay hindi dapat lumampas sa 10MB.',
        ]);

        $path = $request->file('proof_of_delivery')->store('proof_of_delivery', 'public');

        DB::transaction(function () use ($request, $delivery, $path) {
            $delivery->update([
                'status'            => 'delivered',
                'delivered_by'      => Auth::id(),
                'proof_of_delivery' => $path,
            ]);

            // DEDUCT STOCK HERE (As per user request: Delivery stock is deducted only upon delivery)
            if ($delivery->sale) {
                foreach ($delivery->sale->saleItems as $item) {
                    $this->inventoryService->deductStock(
                        (float) $item->quantity, 
                        'sale_delivery_final', 
                        $delivery->sale->id, 
                        Auth::id(), 
                        'Stock deducted upon successful delivery confirmation.', 
                        (int) $item->product_id
                    );
                }
            }

            $delivery->logs()->create([
                'status' => 'delivered',
                'notes'  => 'Driver confirmed delivery with proof of delivery photo. Stock deducted from main inventory.',
            ]);
        });

        // Release vehicle back to available if no other pending deliveries exist
        if ($delivery->vehicle_id) {
            $hasOtherPending = \App\Models\Delivery::where('vehicle_id', $delivery->vehicle_id)
                ->where('id', '!=', $delivery->id)
                ->whereIn('status', ['pending', 'out_for_delivery'])
                ->exists();

            if (!$hasOtherPending) {
                $delivery->vehicle->update(['status' => 'available']);
            }
        }

        ActivityLogService::log(Auth::id(), 'update', 'deliveries', 'Driver confirmed delivery #'.$delivery->id, $request);

        \App\Models\SystemNotification::notifyUsers(
            'delivery_update',
            'Delivery Completed',
            'Delivery #'.$delivery->id.' has been successfully delivered.'
        );

        return redirect()->route('driver.index')->with('success', '🎉 Delivery confirmed! Salamat at ingat palagi.');
    }

    /** History of deliveries */
    public function history(): View
    {
        $driver = Auth::user();

        // Show deliveries where:
        // 1. The driver was the one who delivered it successfully
        // 2. OR it was cancelled while assigned to their vehicle
        $deliveries = Delivery::with(['sale.saleItems.product', 'customer', 'vehicle'])
            ->where(function($q) use ($driver) {
                $q->where('delivered_by', $driver->id)
                  ->orWhere(function($sub) use ($driver) {
                      $sub->where('status', 'cancelled')
                          ->whereHas('vehicle', function($v) use ($driver) {
                              $v->where('assigned_driver_id', $driver->id);
                          });
                  });
            })
            ->whereIn('status', ['delivered', 'cancelled'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('driver.history', compact('deliveries'));
    }
}
