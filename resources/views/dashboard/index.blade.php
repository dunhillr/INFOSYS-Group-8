@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<!-- PAGE HEADER -->
<div class="flex justify-between items-center mt-4 mb-6">

    <!-- LEFT: LOGO + TITLE -->
    <div class="flex items-center gap-3">

        <img src="{{ asset('images/logo.png') }}" 
             alt="Logo" 
             class="h-10 w-auto object-contain">

        <div>
            <h3 class="text-2xl font-bold text-gray-800">
                Dashboard
            </h3>
            <p class="text-sm text-gray-500">
                Overview of your system performance
            </p>
        </div>

    </div>

</div>

<!-- STATS CARDS -->
<div class="grid grid-cols-12 gap-6">

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-blue-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Today Production (All Products)</p>
            <h4 class="text-2xl font-bold text-blue-600">
                {{ number_format($todayProduction, 2) }}
            </h4>
        </div>
    </div>

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-blue-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Today Sales</p>
            <h4 class="text-2xl font-bold text-blue-600">
                {{ number_format($todaySales, 2) }}
            </h4>
        </div>
    </div>

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-yellow-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Pending Deliveries</p>
            <h4 class="text-2xl font-bold text-yellow-600">
                {{ $pendingDeliveries }}
            </h4>
        </div>
    </div>

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-purple-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">In Transit</p>
            <h4 class="text-2xl font-bold text-purple-600">
                {{ $inTransitDeliveries }}
            </h4>
        </div>
    </div>

</div>

<!-- AVAILABLE STOCK PER PRODUCT -->
@if($inventories->count())
<div class="mt-6">
    <h4 class="text-lg font-semibold text-gray-700 mb-3">Available Stock per Product</h4>
    <div class="grid grid-cols-12 gap-4">
        @foreach($inventories as $inv)
            <div class="xl:col-span-3 md:col-span-4 col-span-6">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 
                    {{ (float) $inv->current_stock <= (float) $inv->low_stock_threshold ? 'border-red-500' : 'border-green-500' }}
                    hover:shadow-md transition">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $inv->product->product_name ?? 'Unknown' }}</p>
                    <h4 class="text-xl font-bold mt-1 
                        {{ (float) $inv->current_stock <= (float) $inv->low_stock_threshold ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($inv->current_stock, 2) }}
                    </h4>
                    <p class="text-xs text-gray-400 mt-1">Available</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- TODAY'S PRODUCTION BREAKDOWN -->
@if($todayProductionByProduct->count())
<div class="mt-6">
    <h4 class="text-lg font-semibold text-gray-700 mb-3">Today's Production Breakdown</h4>
    <div class="grid grid-cols-12 gap-4">
        @foreach($todayProductionByProduct as $item)
            <div class="xl:col-span-3 md:col-span-4 col-span-6">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-400 hover:shadow-md transition">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $item->product->product_name ?? 'Unknown' }}</p>
                    <h4 class="text-xl font-bold mt-1 text-blue-600">
                        {{ number_format($item->total_produced, 2) }}
                    </h4>
                    <p class="text-xs text-gray-400 mt-1">Produced Today</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- DELIVERY LOGISTICS STATUS -->
@if($recentDeliveries->count())
<div class="mt-8">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-700">Delivery Logistics Status</h4>
        <a href="{{ route('deliveries.index') }}" class="text-sm text-blue-600 hover:underline">View All Deliveries</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full whitespace-nowrap table-auto border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sale No.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Destination</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status & Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentDeliveries as $delivery)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3 text-sm text-gray-700">
                                {{ $delivery->delivery_date?->format('M d, Y') }} 
                                <span class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($delivery->delivery_time)->format('h:i A') }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm font-bold">
                                <a href="{{ route('deliveries.edit', $delivery) }}" class="text-blue-600 hover:underline">
                                    {{ $delivery->sale->sale_number ?? 'DET-'.$delivery->id }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-700 font-medium">
                                {{ $delivery->customer->customer_name ?? 'Walk-in' }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="bx bx-map text-red-500"></i>
                                    <span class="truncate max-w-[200px]" title="{{ $delivery->destination }}">{{ $delivery->destination }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600">
                                {{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }}
                            </td>
                            <td class="px-5 py-3 text-sm">
                                <div class="flex items-center gap-3">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'out_for_delivery' => 'bg-blue-100 text-blue-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $colorClass = $statusColors[$delivery->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $colorClass }}">
                                        {{ ucwords(str_replace('_', ' ', $delivery->status)) }}
                                    </span>

                                    {{-- Dynamic Actions --}}
                                    <div class="flex gap-1">
                                        @if($delivery->status === 'pending')
                                            <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="out_for_delivery">
                                                <button type="submit" class="bg-blue-600 text-white px-2 py-0.5 rounded text-[10px] font-bold hover:bg-blue-700 transition">
                                                    Dispatch
                                                </button>
                                            </form>
                                        @elseif($delivery->status === 'out_for_delivery')
                                            <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="delivered">
                                                <button type="submit" class="bg-green-600 text-white px-2 py-0.5 rounded text-[10px] font-bold hover:bg-green-700 transition">
                                                    Done
                                                </button>
                                            </form>
                                        @else
                                            {{-- Eye icon for finished deliveries --}}
                                            <a href="{{ route('deliveries.edit', $delivery) }}" class="text-blue-500 hover:bg-blue-100 p-1 rounded transition" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- CONTENT SECTION -->
<div class="grid grid-cols-12 gap-6 mt-8">

    <!-- NOTIFICATIONS -->
    <div class="xl:col-span-6 col-span-12">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <!-- HEADER -->
            <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
                Recent Notifications
            </div>

            <div class="p-5 space-y-4">

                @forelse($notifications as $notification)
                    <div class="border-b border-gray-100 pb-3 last:border-none">
                        <h6 class="font-semibold text-gray-800">
                            {{ $notification->title }}
                        </h6>
                        <p class="text-gray-500 text-sm">
                            {{ $notification->message }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No notifications found.</p>
                @endforelse

            </div>
        </div>
    </div>

    <!-- ACTIVITY LOGS -->
    <div class="xl:col-span-6 col-span-12">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <!-- HEADER -->
            <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
                Recent Activity Logs
            </div>

            <div class="p-5 space-y-4">

                @forelse($logs as $log)
                    <div class="border-b border-gray-100 pb-3 last:border-none">
                        <h6 class="font-semibold text-gray-800">
                            {{ $log->user?->name ?? 'System' }}
                        </h6>
                        <p class="text-gray-500 text-sm">
                            {{ $log->action }} {{ $log->module }} - {{ $log->description }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No activity logs found.</p>
                @endforelse

            </div>

        </div>
    </div>

</div>

@endsection