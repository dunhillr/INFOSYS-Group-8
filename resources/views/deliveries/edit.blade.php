@extends('layouts.app')

@section('title', 'Track Delivery')

@section('content')

<!-- PAGE HEADER -->
<div class="block justify-between page-header md:flex mt-4 items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Track Delivery</h3>
        <p class="text-sm text-gray-500">Manage the progress of Delivery #{{ $delivery->id }}</p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="{{ route('deliveries.index') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm font-medium shadow-sm">
            ← Back to List
        </a>
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
                    <label class="text-xs text-gray-400 uppercase font-semibold">Assigned Driver</label>
                    <p class="text-sm font-medium text-gray-800">
                        @if($delivery->status === 'delivered' && $delivery->deliverer)
                            {{ $delivery->deliverer->name }} <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full ml-1 font-bold tracking-wide">COMPLETED BY</span>
                        @else
                            {{ $delivery->vehicle->driver->name ?? 'None' }}
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
                @if($delivery->proof_of_delivery)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="text-xs text-gray-400 uppercase font-semibold mb-2 block">Proof of Delivery</label>
                    <div class="rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                        <img src="{{ asset('storage/' . $delivery->proof_of_delivery) }}" 
                             alt="Proof of Delivery" 
                             class="w-full h-auto cursor-pointer hover:opacity-90 transition"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RIGHT: TRACKING HISTORY & STATUS -->
    <div class="xl:col-span-8 col-span-12">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-6">
            <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
                Current Status
            </div>
            <div class="p-5 flex items-center gap-4">
                @php
                    $statusMap = [
                        'pending' => ['bg-yellow-100 text-yellow-800', 'PENDING (Reserved)'],
                        'out_for_delivery' => ['bg-blue-100 text-blue-800', 'IN TRANSIT'],
                        'delivered' => ['bg-green-100 text-green-800', 'DELIVERED (Completed)'],
                        'cancelled' => ['bg-red-100 text-red-800', 'CANCELLED'],
                    ];
                    [$color, $label] = $statusMap[$delivery->status] ?? ['bg-gray-100 text-gray-800', strtoupper($delivery->status)];
                @endphp
                <div class="text-4xl">
                    @if($delivery->status === 'delivered') ✅
                    @elseif($delivery->status === 'cancelled') ❌
                    @elseif($delivery->status === 'out_for_delivery') 🚚
                    @else 🕐 @endif
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-800">{{ $label }}</h4>
                    <p class="text-sm text-gray-500 mt-1">Status updates are managed by the driver via the mobile portal.</p>
                </div>
            </div>
        </div>

        <!-- TRACKING HISTORY -->
        <div>
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