@extends('layouts.app')
@section('title', 'Sales History / Transactions')

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
                Sales History
            </h3>
            <p class="text-sm text-gray-500">
                View completed transactions
            </p>
        </div>

    </div>

    <!-- RIGHT: BACK BUTTON -->
    <a href="{{ route('sales.index') }}" 
       class="bg-gray-600 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 transition">
        ← Back to Sales
    </a>

</div>

<!-- ADVANCED FILTERS -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
    <div class="p-4 bg-gray-50 border-b border-gray-100">
        <form action="{{ route('sales.history') }}" method="GET" id="historyFilterForm">
            <div class="grid grid-cols-12 gap-4">
                {{-- Search --}}
                <div class="xl:col-span-4 col-span-12">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Search Transaction</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control text-sm" placeholder="Search Receipt # or Customer...">
                </div>

                {{-- Single Calendar Range Picker --}}
                <div class="xl:col-span-5 col-span-12">
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
                            <button type="button" onclick="setHistoryToday()" class="bg-white border border-gray-200 px-3 py-2 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-100 transition shadow-sm">Today</button>
                            <button type="button" onclick="setHistoryThisWeek()" class="bg-white border border-gray-200 px-3 py-2 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-100 transition shadow-sm">This Week</button>
                        </div>
                    </div>
                    {{-- Hidden inputs for shortcuts --}}
                    <input type="hidden" name="start_date" id="h_start_date">
                    <input type="hidden" name="end_date" id="h_end_date">
                </div>

                {{-- Reset --}}
                <div class="xl:col-span-3 col-span-12 flex items-end">
                    <a href="{{ route('sales.history') }}" class="bg-white border border-gray-200 text-gray-500 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm font-medium w-full justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset History
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SUMMARY CARDS -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <!-- Total Transactions -->
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-600">
        <p class="text-sm text-gray-600 font-semibold">Total Transactions</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $sales->total() }}</p>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-600">
        <p class="text-sm text-gray-600 font-semibold">Total Revenue</p>
        <p class="text-2xl font-bold text-green-600 mt-1">₱{{ number_format($sales->sum('total_amount'), 2) }}</p>
    </div>

    <!-- Average Transaction -->
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-600">
        <p class="text-sm text-gray-600 font-semibold">Average Transaction</p>
        <p class="text-2xl font-bold text-purple-600 mt-1">₱{{ number_format($sales->avg('total_amount') ?? 0, 2) }}</p>
    </div>

</div>

<!-- TABLE CARD -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <!-- HEADER -->
    <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
        Transaction Records
    </div>

    <!-- TABLE -->
    <div class="p-5 overflow-auto">

        <table class="min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr>
                    <th class="py-3">Date</th>
                    <th>Receipt ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Encoded By</th>
                    <th>Total Amount</th>
                    <th>Payment Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($sales as $sale)
                <tr class="border-b hover:bg-gray-50 transition">

                    <!-- Date -->
                    <td class="py-3 text-gray-800 font-medium">
                        {{ $sale->sale_date->format('M d, Y') }}
                        <br>
                        <span class="text-xs text-gray-500">
                            {{ $sale->sale_date->format('h:i A') }}
                        </span>
                    </td>

                    <!-- Receipt ID -->
                    <td class="text-gray-800 font-mono">
                        <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                            {{ $sale->sale_number }}
                        </span>
                    </td>

                    <!-- Customer -->
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
                                    $deliveryBadges = [
                                        'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'out_for_delivery' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'delivered' => 'bg-green-50 text-green-700 border-green-200',
                                        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                    $badgeClass = $deliveryBadges[$deliveryStatus] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                    
                                    $statusLabels = [
                                        'pending' => '⏳ Pending',
                                        'out_for_delivery' => '🚚 In Transit',
                                        'delivered' => '🟢 Delivered',
                                        'cancelled' => '🔴 Cancelled',
                                    ];
                                    $statusLabel = $statusLabels[$deliveryStatus] ?? strtoupper($deliveryStatus);
                                @endphp
                                <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            @else
                                {{-- Walk-in Fulfillment Status --}}
                                @if($sale->payment_status === 'paid')
                                    <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase tracking-wider bg-green-50 text-green-700 border border-green-200">
                                        🟢 Picked Up
                                    </span>
                                @else
                                    <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase tracking-wider bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        ⏳ Pending Pickup
                                    </span>
                                @endif
                            @endif
                        </div>
                    </td>

                    <!-- Items -->
                    <td class="text-gray-800 text-xs">
                        @foreach($sale->saleItems as $item)
                            <div class="mb-1">
                                <span class="font-semibold">{{ $item->product->product_name ?? 'Unknown' }}</span>
                                <span class="text-gray-500">(x{{ number_format($item->quantity, 2) }})</span>
                            </div>
                        @endforeach
                    </td>

                    <!-- Assigned Vehicle -->
                    <td class="text-gray-600">
                        @if($sale->vehicle)
                            <div class="flex items-center gap-1">
                                🚗
                                <span>{{ $sale->vehicle->vehicle_name }}</span>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $sale->vehicle->plate_number }}
                            </span>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>

                    <!-- Encoded By -->
                    <td class="text-gray-600 text-xs font-semibold">
                        <div class="flex items-center gap-1">
                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded">
                                {{ $sale->user->name ?? 'System' }}
                            </span>
                        </div>
                    </td>

                    <!-- Total Amount -->
                    <td class="text-blue-600 font-semibold text-lg">
                        ₱{{ number_format($sale->total_amount, 2) }}
                    </td>

                    <!-- Payment Status -->
                    <td class="text-center">
                        @if($sale->payment_status === 'paid')
                            <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full font-semibold">
                                ✓ Paid
                            </span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-semibold">
                                ⚠ Partial
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full font-semibold">
                                ✗ Unpaid
                            </span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="text-center">
                        <button 
                            type="button" 
                            class="view-receipt-btn bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 active:bg-blue-800 active:scale-95 transition-all cursor-pointer font-semibold shadow-sm"
                            onclick="viewReceipt({{ $sale->id }}, '{{ $sale->sale_number }}', this)">
                            👁️ View
                        </button>
                    </td>

                </tr>
                @empty

                <!-- Empty State -->
                <tr>
                    <td colspan="10" class="py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-4xl mb-2">📋</div>
                            <p class="font-semibold">No transactions found</p>
                            <p class="text-sm">Start by creating your first sale</p>
                        </div>
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="p-5 border-t border-gray-100">
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
        <div id="receiptContent" class="p-4">
            <!-- Content will be loaded here -->
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-4 py-2 flex gap-2 justify-end border-t sticky bottom-0">
            <button 
                onclick="printReceipt()" 
                class="bg-green-600 text-white px-3 py-1 text-sm rounded hover:bg-green-700 transition">
                🖨️ Print
            </button>
            <button 
                onclick="closeReceipt()" 
                class="bg-gray-600 text-white px-3 py-1 text-sm rounded hover:bg-gray-700 transition">
                Close
            </button>
        </div>

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
            document.getElementById('historyFilterForm').submit();
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

function setHistoryToday() {
    const today = getLocalDate();
    document.getElementById('h_start_date').value = today;
    document.getElementById('h_end_date').value = today;
    document.getElementById('historyFilterForm').submit();
}

function setHistoryThisWeek() {
    const now = new Date();
    const dayOfWeek = now.getDay();
    const diff = now.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
    const mondayDate = new Date(now.setDate(diff));
    
    const start = mondayDate.getFullYear() + '-' + 
                  String(mondayDate.getMonth() + 1).padStart(2, '0') + '-' + 
                  String(mondayDate.getDate()).padStart(2, '0');
    
    const end = getLocalDate();

    document.getElementById('h_start_date').value = start;
    document.getElementById('h_end_date').value = end;
    document.getElementById('historyFilterForm').submit();
}

function viewReceipt(saleId, receiptNumber, btn) {
    // Show loading state on button
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.classList.add('opacity-70');
    btn.innerHTML = 'Loading...';
    
    // Fetch sale details via AJAX
    fetch(`/sales/${saleId}`)
        .then(response => response.text())
        .then(html => {
            // Parse the response to get sale details
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Build receipt content
            const saleData = @json($sales);
            const sale = saleData.data.find(s => s.id == saleId);
            
            let receiptHTML = `
                <div class="receipt-container text-sm">
                    <h4 class="text-lg font-bold mb-3 text-center">Receipt #${receiptNumber}</h4>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4 pb-3 border-b">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">TRANSACTION DATE</p>
                            <p class="font-semibold text-xs">${new Date(sale.sale_date).toLocaleDateString()} ${new Date(sale.sale_date).toLocaleTimeString()}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">RECEIPT ID</p>
                            <p class="font-semibold text-xs">${receiptNumber}</p>
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-b">
                        <p class="text-xs text-gray-600 font-semibold mb-1">CUSTOMER</p>
                        <p class="font-semibold text-xs">${sale.customer?.customer_name || 'Walk-in Customer'}</p>
                    </div>

                    <div class="mb-3 pb-3 border-b">
                        <p class="text-xs text-gray-600 font-semibold mb-1">SALE DETAILS</p>
                        <div class="space-y-1 text-xs">
                            <div class="mb-2">
                                <span class="font-semibold text-gray-600">ITEMS:</span>
                            </div>
                            <div class="border rounded p-2 mb-2 bg-gray-50">
                                ${sale.sale_items.map(item => `
                                    <div class="flex justify-between mb-1">
                                        <span>${item.product?.product_name || '-'} (x${parseFloat(item.quantity).toFixed(2)})</span>
                                        <span class="font-semibold">₱${parseFloat(item.subtotal).toFixed(2)}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-500 text-[10px] mb-2 border-b border-gray-200 pb-1 last:border-0 last:pb-0 last:mb-0">
                                        <span>@ ₱${parseFloat(item.unit_price).toFixed(2)}/unit</span>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="flex justify-between">
                                <span>Sales Type:</span>
                                <span class="font-semibold">${sale.sale_type.toUpperCase()}</span>
                            </div>
                            ${sale.delivery_fee > 0 ? `
                            <div class="flex justify-between">
                                <span>Delivery Fee:</span>
                                <span class="font-semibold">₱${parseFloat(sale.delivery_fee).toFixed(2)}</span>
                            </div>
                            ` : ''}
                            ${sale.discount_amount > 0 ? `
                            <div class="flex justify-between text-red-500">
                                <span>Discount (${sale.discount_type}):</span>
                                <span class="font-semibold">-₱${parseFloat(sale.discount_type === 'percent' ? (sale.total_amount + parseFloat(sale.discount_amount)) * (parseFloat(sale.discount_amount)/100) : sale.discount_amount).toFixed(2)}</span>
                            </div>
                            ` : ''}
                            ${sale.vehicle ? `
                            <div class="flex justify-between">
                                <span>Vehicle:</span>
                                <span class="font-semibold">${sale.vehicle.vehicle_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Plate:</span>
                                <span>${sale.vehicle.plate_number}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-b bg-blue-50 p-2 rounded">
                        <div class="flex justify-between text-sm font-bold">
                            <span>TOTAL:</span>
                            <span class="text-blue-600">₱${parseFloat(sale.total_amount).toFixed(2)}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-xs text-gray-600 font-semibold mb-1">PAYMENT STATUS</p>
                        <p class="font-semibold text-xs">${sale.payment_status.toUpperCase()}</p>
                    </div>

                    ${sale.notes ? `
                    <div class="mb-3 pb-3 border-b">
                        <p class="text-xs text-gray-600 font-semibold mb-1">NOTES</p>
                        <p class="text-xs">${sale.notes}</p>
                    </div>
                    ` : ''}

                    <div class="text-center text-xs text-gray-500 pt-2">
                        <p>Processed by: ${sale.user?.name || 'System'}</p>
                        <p>Generated: ${new Date().toLocaleString()}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('receiptContent').innerHTML = receiptHTML;
            document.getElementById('receiptModal').classList.remove('hidden');
            
            // Restore button state
            btn.disabled = false;
            btn.classList.remove('opacity-70');
            btn.innerHTML = originalText;
        })
        .catch(error => {
            console.error('Error fetching sale details:', error);
            alert('Unable to load receipt details');
            
            // Restore button state on error
            btn.disabled = false;
            btn.classList.remove('opacity-70');
            btn.innerHTML = originalText;
        });
}

function closeReceipt() {
    document.getElementById('receiptModal').classList.add('hidden');
}

function printReceipt() {
    const content = document.getElementById('receiptContent').innerHTML;
    const printWindow = window.open('', '', 'height=500,width=800');
    printWindow.document.write('<html><head><title>Receipt</title>');
    printWindow.document.write('<style>body { font-family: Arial; margin: 20px; } .receipt-container { max-width: 600px; margin: 0 auto; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Close modal when clicking outside
document.getElementById('receiptModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReceipt();
    }
});
</script>

@endsection
