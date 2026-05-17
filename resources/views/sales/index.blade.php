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
                        <div class="font-semibold text-gray-800 mb-1">{{ $sale->customer->customer_name ?? 'Walk-in' }}</div>
                        
                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                            {{-- Base Order Type Badge --}}
                            <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase tracking-wider {{ $sale->delivery_type === 'walk_in' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-purple-50 text-purple-600 border border-purple-100' }}">
                                {{ $sale->delivery_type === 'walk_in' ? '🚶 Walk-In' : '🚚 Delivery' }}
                            </span>

                            {{-- Fulfillment / Delivery Status Badge --}}
                            @if($sale->delivery_type === 'delivery')
                                @php
                                    $deliveryStatus = $sale->delivery->status ?? 'pending';
                                    if ($deliveryStatus === 'out_for_delivery') {
                                        $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        $statusLabel = '🚚 In Transit';
                                    } else {
                                        $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        $statusLabel = '⏳ Pending';
                                    }
                                @endphp
                                <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            @else
                                {{-- Walk-in Fulfillment Status --}}
                                <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase tracking-wider border bg-green-50 text-green-700 border-green-200">
                                    🛍️ Pick Up
                                </span>
                            @endif
                        </div>
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

                    <td class="py-3">
                        <div class="flex gap-2">
                            <button 
                                type="button" 
                                class="bg-gray-100 text-gray-700 px-3 py-1 rounded text-xs hover:bg-gray-200 transition font-semibold"
                                onclick="viewReceipt({{ $sale->id }}, '{{ $sale->sale_number }}', this)">
                                👁️ View
                            </button>

                            @php
                                $isEditable = true;
                                $editTooltip = "";
                                if ($sale->delivery_type === 'delivery' && $sale->delivery) {
                                    if ($sale->delivery->status === 'out_for_delivery') {
                                        $isEditable = false;
                                        $editTooltip = "Hindi pwedeng i-edit habang nasa byahe (In Transit)";
                                    } elseif (in_array($sale->delivery->status, ['delivered', 'cancelled'])) {
                                        $isEditable = false;
                                        $editTooltip = "Hindi pwedeng i-edit dahil tapos na ang delivery";
                                    }
                                }
                            @endphp

                            @if($isEditable)
                                <a href="{{ route('sales.edit', $sale) }}"
                                   class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition font-semibold">
                                    Edit
                                </a>
                            @else
                                <button type="button" 
                                        class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-xs cursor-not-allowed font-semibold"
                                        title="{{ $editTooltip }}" disabled>
                                    🔒 Locked
                                </button>
                            @endif

                            @if(auth()->user()->user_type === 'owner')
                            <form action="{{ route('sales.destroy', $sale) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Delete this sale record?')">
                                @csrf 
                                @method('DELETE')

                                <button class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition font-semibold">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-6 text-gray-400">
                        No active sales records found today.
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

<!-- RECEIPT DETAIL MODAL -->
<div id="receiptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full max-h-[85vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-blue-600 text-white px-4 py-3 flex justify-between items-center sticky top-0">
            <h3 class="text-base font-bold">Receipt Details</h3>
            <button onclick="closeReceipt()" class="text-xl leading-none">×</button>
        </div>
        <!-- Modal Body -->
        <div id="receiptContent" class="p-4"></div>
        <!-- Modal Footer -->
        <div class="bg-gray-50 px-4 py-2 flex gap-2 justify-end border-t sticky bottom-0">
            <button onclick="printReceipt()" class="bg-green-600 text-white px-3 py-1 text-sm rounded hover:bg-green-700 transition">🖨️ Print</button>
            <button onclick="closeReceipt()" class="bg-gray-600 text-white px-3 py-1 text-sm rounded hover:bg-gray-700 transition">Close</button>
        </div>
    </div>
</div>

<script>
function viewReceipt(saleId, receiptNumber, btn) {
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '...';
    
    fetch(`/sales/${saleId}`)
        .then(response => response.text())
        .then(html => {
            const saleData = @json($sales);
            const sale = saleData.data.find(s => s.id == saleId);
            
            let receiptHTML = `
                <div class="receipt-container text-sm">
                    <h4 class="text-lg font-bold mb-3 text-center">Receipt #${receiptNumber}</h4>
                    <div class="grid grid-cols-2 gap-3 mb-4 pb-3 border-b">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Date</p>
                            <p class="font-semibold text-xs">${new Date(sale.sale_date).toLocaleDateString()} ${new Date(sale.sale_date).toLocaleTimeString()}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">ID</p>
                            <p class="font-semibold text-xs">${receiptNumber}</p>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-b">
                        <p class="text-xs text-gray-600 font-semibold mb-1 uppercase">Customer</p>
                        <p class="font-semibold text-xs">${sale.customer?.customer_name || 'Walk-in Customer'}</p>
                    </div>
                    <div class="mb-3 pb-3 border-b">
                        <p class="text-xs text-gray-600 font-semibold mb-1 uppercase">Sale Details</p>
                        <div class="space-y-1 text-xs">
                            <div class="border rounded p-2 mb-2 bg-gray-50">
                                ${sale.sale_items.map(item => `
                                    <div class="flex justify-between mb-1">
                                        <span>${item.product?.product_name || '-'} (x${parseFloat(item.quantity).toFixed(2)})</span>
                                        <span class="font-semibold">₱${parseFloat(item.subtotal).toFixed(2)}</span>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="flex justify-between">
                                <span>Type:</span> <span class="font-semibold">${sale.delivery_type.toUpperCase()}</span>
                            </div>
                            ${sale.delivery_fee > 0 ? `<div class="flex justify-between"><span>Fee:</span> <span class="font-semibold">₱${parseFloat(sale.delivery_fee).toFixed(2)}</span></div>` : ''}
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-b bg-blue-50 p-2 rounded">
                        <div class="flex justify-between text-sm font-bold">
                            <span>TOTAL:</span> <span class="text-blue-600">₱${parseFloat(sale.total_amount).toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="text-center text-[10px] text-gray-500 pt-2">
                        <p>Encoded by: ${sale.user?.name || 'System'}</p>
                    </div>
                </div>
            `;
            document.getElementById('receiptContent').innerHTML = receiptHTML;
            document.getElementById('receiptModal').classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}

function closeReceipt() { document.getElementById('receiptModal').classList.add('hidden'); }
function printReceipt() {
    const content = document.getElementById('receiptContent').innerHTML;
    const printWindow = window.open('', '', 'height=500,width=800');
    printWindow.document.write('<html><head><title>Receipt</title><style>body { font-family: Arial; margin: 20px; }</style></head><body>' + content + '</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>

@endsection