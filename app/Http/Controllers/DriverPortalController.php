<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DriverPortalController extends Controller
{
    /** My deliveries list (mobile view) */
    public function index(): View
    {
        $driver = Auth::user();

        $deliveries = Delivery::with(['sale.saleItems.product', 'customer', 'vehicle'])
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['pending', 'out_for_delivery'])
            ->orderBy('delivery_date')
            ->orderBy('delivery_time')
            ->get();

        $completedToday = Delivery::where('driver_id', $driver->id)
            ->where('status', 'delivered')
            ->whereDate('delivery_date', today())
            ->count();

        return view('driver.index', compact('deliveries', 'completedToday'));
    }

    /** Single delivery detail */
    public function show(Delivery $delivery): View
    {
        // Ensure driver can only view their own deliveries
        if ($delivery->driver_id !== Auth::id()) {
            abort(403, 'This delivery is not assigned to you.');
        }

        $delivery->load(['sale.saleItems.product', 'customer', 'vehicle', 'logs']);

        return view('driver.show', compact('delivery'));
    }

    /** Driver clicks "Start Biyahe" → sets status to out_for_delivery */
    public function startDelivery(Request $request, Delivery $delivery): RedirectResponse
    {
        if ($delivery->driver_id !== Auth::id()) {
            abort(403);
        }

        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Delivery is not in Pending status.');
        }

        $delivery->update(['status' => 'out_for_delivery']);

        $delivery->logs()->create([
            'status' => 'out_for_delivery',
            'notes'  => 'Driver started the delivery.',
        ]);

        // Mark vehicle as in_transit
        if ($delivery->vehicle_id) {
            $delivery->vehicle->update(['status' => 'in_transit']);
        }

        ActivityLogService::log(Auth::id(), 'update', 'deliveries', 'Driver started delivery #'.$delivery->id, $request);

        \App\Models\SystemNotification::notifyUsers(
            'delivery_update',
            'Delivery In Transit',
            'Delivery #'.$delivery->id.' is now in transit.'
        );

        return redirect()->route('driver.show', $delivery)->with('success', '✅ Biyahe started! Stay safe on the road.');
    }

    /** Driver clicks "Confirm Delivery" → sets status to delivered + uploads POD */
    public function confirmDelivery(Request $request, Delivery $delivery): RedirectResponse
    {
        if ($delivery->driver_id !== Auth::id()) {
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

        $delivery->update([
            'status'            => 'delivered',
            'delivered_by'      => Auth::id(),
            'proof_of_delivery' => $path,
        ]);

        $delivery->logs()->create([
            'status' => 'delivered',
            'notes'  => 'Driver confirmed delivery with proof of delivery photo.',
        ]);

        // Release vehicle back to available
        if ($delivery->vehicle_id) {
            $delivery->vehicle->update(['status' => 'available']);
        }

        ActivityLogService::log(Auth::id(), 'update', 'deliveries', 'Driver confirmed delivery #'.$delivery->id, $request);

        \App\Models\SystemNotification::notifyUsers(
            'delivery_update',
            'Delivery Completed',
            'Delivery #'.$delivery->id.' has been successfully delivered.'
        );

        return redirect()->route('driver.index')->with('success', '🎉 Delivery confirmed! Salamat at ingat palagi.');
    }
}
