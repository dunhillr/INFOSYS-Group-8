@extends('layouts.app')
@section('title', 'Sales')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-input { background-color: white !important; }
</style>
@endpush

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
                Sales
            </h3>
            <p class="text-sm text-gray-500">
                Manage sales transactions
            </p>
        </div>

    </div>

    <!-- RIGHT: ADD BUTTON -->
    <a href="{{ route('sales.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Add Sale
    </a>

</div>

<!-- TABLE CARD -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <!-- HEADER -->
    <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
        Sales Records
    </div>

    <!-- FILTERS -->
    <div class="p-5 bg-gray-50 border-b border-gray-100">
        <form action="{{ route('sales.index') }}" method="GET" id="saleFilterForm">
            <div class="grid grid-cols-12 gap-4">
                {{-- Search --}}
                <div class="xl:col-span-3 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control text-sm" placeholder="Sale # or Customer Name...">
                </div>

                {{-- Payment Status --}}
                <div class="xl:col-span-2 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Payment</label>
                    <select name="payment_status" class="form-control text-sm" onchange="this.form.submit()">
                        <option value="">All Payments</option>
                        <option value="paid" @selected(request('payment_status') == 'paid')>Paid</option>
                        <option value="partial" @selected(request('payment_status') == 'partial')>Partial</option>
                        <option value="unpaid" @selected(request('payment_status') == 'unpaid')>Unpaid</option>
                    </select>
                </div>

                {{-- Single Calendar Date Range --}}
                <div class="xl:col-span-4 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Date Range (Select Start & End)</label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input type="text" name="date_range" id="sale_date_range" value="{{ request('date_range') }}" 
                                   class="form-control text-sm pl-9" placeholder="Select Date Range...">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button type="button" onclick="setSaleToday()" class="bg-white border border-gray-200 px-2 py-2 rounded text-xs font-bold text-gray-600 hover:bg-gray-100 transition shadow-sm">Today</button>
                            <button type="button" onclick="setSaleThisWeek()" class="bg-white border border-gray-200 px-2 py-2 rounded text-xs font-bold text-gray-600 hover:bg-gray-100 transition shadow-sm">This Week</button>
                        </div>
                    </div>
                    {{-- Hidden inputs for shortcuts --}}
                    <input type="hidden" name="start_date" id="sale_start_date">
                    <input type="hidden" name="end_date" id="sale_end_date">
                </div>

                {{-- Reset --}}
                <div class="xl:col-span-3 col-span-12 flex items-start mt-5">
                    <a href="{{ route('sales.index') }}" class="bg-white border border-gray-200 text-gray-500 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm font-medium w-full justify-center shadow-sm" title="Reset Filters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Filters
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    // Initialize Flatpickr Range
    flatpickr("#sale_date_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                document.getElementById('saleFilterForm').submit();
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

    function setSaleToday() {
        const today = getLocalDate();
        document.getElementById('sale_start_date').value = today;
        document.getElementById('sale_end_date').value = today;
        document.getElementById('saleFilterForm').submit();
    }

    function setSaleThisWeek() {
        const now = new Date();
        const dayOfWeek = now.getDay();
        const diff = now.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
        const monday = new Date(now.setDate(diff));
        
        const start = monday.getFullYear() + '-' + 
                      String(monday.getMonth() + 1).padStart(2, '0') + '-' + 
                      String(monday.getDate()).padStart(2, '0');
        
        const end = getLocalDate();

        document.getElementById('sale_start_date').value = start;
        document.getElementById('sale_end_date').value = end;
        document.getElementById('saleFilterForm').submit();
    }
    </script>

    <!-- TABLE -->
    <div class="p-5 overflow-auto">

        <table class="min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr class="text-left border-b text-gray-600">
                    <th class="py-3">Sale No.</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($sales as $sale)
                <tr class="border-b hover:bg-gray-50 transition">

                    <td class="py-3 font-medium text-gray-800">
                        {{ $sale->sale_number }}
                    </td>

                    <td class="text-gray-600">
                        {{ $sale->customer->customer_name ?? 'Walk-in' }}
                    </td>

                    <td class="text-gray-800 text-xs">
                        @foreach($sale->saleItems as $item)
                            <div class="mb-1">
                                <span class="font-semibold">{{ $item->product->product_name ?? 'Unknown' }}</span>
                                <span class="text-gray-500">(x{{ number_format($item->quantity, 2) }})</span>
                            </div>
                        @endforeach
                    </td>

                    <td class="text-blue-600 font-semibold">
                        {{ number_format($sale->total_amount, 2) }}
                    </td>

                    <td>
                        @if($sale->payment_status === 'paid')
                            <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                Paid
                            </span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">
                                Partial
                            </span>
                            <div class="text-xs text-gray-500 mt-1">Bal: {{ number_format($sale->balance_due, 2) }}</div>
                        @else
                            <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full">
                                Unpaid
                            </span>
                            <div class="text-xs text-gray-500 mt-1">Bal: {{ number_format($sale->balance_due, 2) }}</div>
                        @endif
                        
                        @if($sale->payment_method)
                            <div class="text-xs text-blue-500 mt-1 font-medium">{{ $sale->payment_method }}</div>
                        @endif
                    </td>

                    <td class="text-gray-600">
                        {{ $sale->sale_date?->format('M d, Y h:i A') }}
                    </td>

                    <td class="flex gap-2 py-3">

                        <a href="{{ route('sales.edit', $sale) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Edit
                        </a>

                        @if(auth()->user()->user_type === 'owner')
                        <form action="{{ route('sales.destroy', $sale) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this sale record?')">
                            @csrf 
                            @method('DELETE')

                            <button class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                        @endif

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-6 text-gray-400">
                        No sales records found.
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="p-4 border-t">
        {{ $sales->links() }}
    </div>

</div>

@endsection