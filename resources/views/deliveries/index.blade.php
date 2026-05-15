@extends('layouts.app')
@section('title', 'Deliveries Control Center')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-input { background-color: white !important; }
</style>
@endpush

@section('content')

<!-- PAGE HEADER -->
<div class="flex justify-between items-center mt-4 mb-6">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Deliveries Control Center</h3>
            <p class="text-sm text-gray-500">Monitor and manage your fleet logistics in real-time</p>
        </div>
    </div>
</div>

<div class="box mb-4">
    <div class="box-body p-4 bg-gray-50 border-b border-gray-100 rounded-t-lg">
        <form action="{{ route('deliveries.index') }}" method="GET" id="filterForm">
            <div class="grid grid-cols-12 gap-4">
                {{-- Search --}}
                <div class="xl:col-span-3 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control text-sm" placeholder="Sale #, Customer, Plate...">
                </div>

                {{-- Status Filter --}}
                <div class="xl:col-span-2 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Status</label>
                    <select name="status" class="form-control text-sm" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                        <option value="out_for_delivery" @selected(request('status') == 'out_for_delivery')>In Transit</option>
                        <option value="delivered" @selected(request('status') == 'delivered')>Delivered</option>
                        <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelled</option>
                    </select>
                </div>

                {{-- Vehicle Filter --}}
                <div class="xl:col-span-2 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Vehicle</label>
                    <select name="vehicle_id" class="form-control text-sm" onchange="this.form.submit()">
                        <option value="">All Vehicles</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" @selected(request('vehicle_id') == $v->id)>
                                {{ $v->vehicle_name }} ({{ $v->plate_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range (Single Calendar) --}}
                <div class="xl:col-span-3 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Date Range (Select Start & End)</label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input type="text" name="date_range" id="date_range_picker" value="{{ request('date_range') }}" 
                                   class="form-control text-sm pl-9" placeholder="Select Date Range...">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button type="button" onclick="setToday()" class="bg-white border border-gray-200 px-2 py-2 rounded text-[10px] font-bold text-gray-600 hover:bg-gray-100 transition shadow-sm">Today</button>
                            <button type="button" onclick="setThisWeek()" class="bg-white border border-gray-200 px-2 py-2 rounded text-[10px] font-bold text-gray-600 hover:bg-gray-100 transition shadow-sm">This Week</button>
                        </div>
                    </div>
                    {{-- Hidden inputs for shortcuts --}}
                    <input type="hidden" name="start_date" id="start_date">
                    <input type="hidden" name="end_date" id="end_date">
                </div>

                {{-- Reset --}}
                <div class="xl:col-span-2 col-span-12 flex items-start mt-5">
                    <a href="{{ route('deliveries.index') }}" class="bg-white border border-gray-200 text-gray-500 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm font-medium w-full justify-center shadow-sm" title="Clear all filters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Initialize Flatpickr Range
flatpickr("#date_range_picker", {
    mode: "range",
    dateFormat: "Y-m-d",
    onClose: function(selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            document.getElementById('filterForm').submit();
        }
    }
});

function getLocalDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function setToday() {
    const today = getLocalDate();
    document.getElementById('start_date').value = today;
    document.getElementById('end_date').value = today;
    document.getElementById('filterForm').submit();
}

function setThisWeek() {
    const now = new Date();
    const dayOfWeek = now.getDay(); // 0 is Sunday, 1 is Monday
    const diff = now.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); // Adjust for Monday start
    const monday = new Date(now.setDate(diff));
    
    const start = monday.getFullYear() + '-' + 
                  String(monday.getMonth() + 1).padStart(2, '0') + '-' + 
                  String(monday.getDate()).padStart(2, '0');
    
    const end = getLocalDate();

    document.getElementById('start_date').value = start;
    document.getElementById('end_date').value = end;
    document.getElementById('filterForm').submit();
}
</script>

<div class="box">
    <div class="box-body">
        <div class="overflow-visible pb-24">
            <table class="table min-w-full whitespace-nowrap table-bordered">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Sale No.</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Customer</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Vehicle</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Destination</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase text-center">Status</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($deliveries as $delivery)
                    <tr class="hover:bg-gray-50 transition {{ !$delivery->is_opened ? 'bg-blue-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-800">{{ $delivery->sale->sale_number ?? 'DET-'.$delivery->id }}</span>
                            @if(!$delivery->is_opened)
                                <span class="badge bg-blue-600 text-white text-[10px] px-1.5 ms-1">NEW</span>
                            @endif
                            @if($delivery->proof_of_delivery)
                                <span class="text-green-600 ms-1" title="Proof of Delivery Uploaded">
                                    <i class="bx bx-camera text-sm"></i>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $delivery->customer->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-800">{{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $delivery->vehicle->plate_number ?? '' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-[200px] truncate" title="{{ $delivery->destination }}">
                            {{ $delivery->destination }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusMap = [
                                    'pending' => ['bg-yellow-100 text-yellow-800', 'PENDING'],
                                    'out_for_delivery' => ['bg-blue-100 text-blue-800', 'IN TRANSIT'],
                                    'delivered' => ['bg-green-100 text-green-800', 'DELIVERED'],
                                    'cancelled' => ['bg-red-100 text-red-800', 'CANCELLED'],
                                ];
                                [$color, $label] = $statusMap[$delivery->status] ?? ['bg-gray-100 text-gray-800', strtoupper($delivery->status)];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider {{ $color }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center items-center">
                                {{-- ── CONSOLIDATED MANAGE DROPDOWN ── --}}
                                <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                                    <button id="actions-{{ $delivery->id }}" type="button" class="hs-dropdown-toggle bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50 transition flex items-center gap-1 shadow-sm">
                                        Manage
                                        <svg class="hs-dropdown-open:rotate-180 w-3.5 h-3.5 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden z-[100] bg-white shadow-xl rounded-xl p-2 mt-2 border border-gray-100 min-w-[180px] text-start" aria-labelledby="actions-{{ $delivery->id }}">
                                        {{-- ── SET TO PENDING ── --}}
                                        @if($delivery->status !== 'pending')
                                            <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="w-full flex items-center gap-2 py-2 px-3 rounded-md text-sm text-yellow-600 hover:bg-yellow-50 font-bold text-start">
                                                    🕐 Set Pending
                                                </button>
                                            </form>
                                        @endif

                                        {{-- ── MARK DELIVERED ── --}}
                                        @if($delivery->status !== 'delivered')
                                            <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="delivered">
                                                <button type="submit" class="w-full flex items-center gap-2 py-2 px-3 rounded-md text-sm text-green-600 hover:bg-green-50 font-bold text-start">
                                                    ✅ Mark Delivered
                                                </button>
                                            </form>
                                        @endif

                                        {{-- ── CANCEL DELIVERY ── --}}
                                        @if($delivery->status !== 'cancelled')
                                            <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="w-full flex items-center gap-2 py-2 px-3 rounded-md text-sm text-red-600 hover:bg-red-50 font-bold text-start">
                                                    ❌ Cancel Delivery
                                                </button>
                                            </form>
                                        @endif

                                        {{-- ── OWNER DELETE (SUBTLE) ── --}}
                                        @if(Auth::user()->user_type === 'owner')
                                            <div class="h-px bg-gray-100 my-1"></div>
                                            <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2 py-2 px-3 rounded-md text-xs text-gray-400 hover:text-red-600 hover:bg-red-50 font-medium text-start">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete Record
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 italic">No delivery records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $deliveries->links() }}
        </div>
    </div>
</div>
@endsection