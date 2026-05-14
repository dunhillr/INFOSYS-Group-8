@extends('layouts.app')

@section('title', 'Track Delivery')

@section('content')

<!-- PAGE HEADER -->
<div class="block justify-between page-header md:flex mt-4">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Track & Update Delivery</h3>
        <p class="text-sm text-gray-500">Manage the progress of Delivery #{{ $delivery->id }}</p>
    </div>
</div>

<div class="grid grid-cols-12 gap-6 mt-6">

    <!-- LEFT: DELIVERY DETAILS SUMMARY -->
    <div class="xl:col-span-4 col-span-12">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">
                Delivery Details
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="text-xs text-gray-400 uppercase font-semibold">Sale Reference</label>
                    <p class="text-sm font-medium text-blue-600">{{ $delivery->sale->sale_number }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 uppercase font-semibold">Customer</label>
                    <p class="text-sm font-medium text-gray-800">{{ $delivery->customer->customer_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 uppercase font-semibold">Vehicle Assigned</label>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }}
                        @if($delivery->vehicle?->plate_number)
                            <span class="text-xs text-gray-400 ml-1">[{{ $delivery->vehicle->plate_number }}]</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 uppercase font-semibold">Destination</label>
                    <p class="text-sm text-gray-700">{{ $delivery->destination }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 uppercase font-semibold">Scheduled Date/Time</label>
                    <p class="text-sm text-gray-700">
                        {{ $delivery->delivery_date?->format('M d, Y') }} at {{ \Carbon\Carbon::parse($delivery->delivery_time)->format('h:i A') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: STATUS UPDATE FORM -->
    <div class="xl:col-span-8 col-span-12">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
                Update Status & Tracking Notes
            </div>
            <div class="p-5">
                <form action="{{ route('deliveries.update', $delivery) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <label class="form-label font-semibold">Current Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" @selected(old('status', $delivery->status) === 'pending')>Pending (Reserved)</option>
                                <option value="out_for_delivery" @selected(old('status', $delivery->status) === 'out_for_delivery')>Out for Delivery (In Transit)</option>
                                <option value="delivered" @selected(old('status', $delivery->status) === 'delivered')>Delivered (Completed)</option>
                                <option value="cancelled" @selected(old('status', $delivery->status) === 'cancelled')>Cancelled</option>
                            </select>
                        </div>

                        <div class="col-span-12">
                            <label class="form-label font-semibold">Update Status Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Enter any updates or notes regarding the current delivery status..."></textarea>
                            <div class="text-[10px] text-muted mt-1">These notes will be added to the tracking history.</div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                            Save Changes
                        </button>
                        <a href="{{ route('deliveries.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
                            Back to List
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- TRACKING HISTORY -->
        <div class="mt-8">
            <h4 class="text-lg font-semibold text-gray-700 mb-4">Delivery Tracking History</h4>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <ul class="list-none space-y-6">
                    @forelse ($delivery->logs as $log)
                        <li class="relative pl-8 pb-2 border-l-2 border-blue-100 last:border-0 last:pb-0">
                            <span class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow-sm"></span>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-blue-700 uppercase text-xs tracking-wider">
                                        {{ str_replace('_', ' ', $log->status) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium">
                                        {{ $log->created_at->format('M d, Y h:i A') }}
                                    </span>
                                </div>
                                @if ($log->notes)
                                    <p class="text-sm mt-1 text-gray-600 italic">"{{ $log->notes }}"</p>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-center text-gray-400 py-4 italic">No tracking history recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

</div>

@endsection