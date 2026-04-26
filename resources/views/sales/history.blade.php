@extends('layouts.app')
@section('title', 'Sales History / Transactions')

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
                <tr class="text-left border-b text-gray-600 font-semibold">
                    <th class="py-3">Date</th>
                    <th>Receipt ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Assigned Vehicle</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
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
                        {{ $sale->customer->customer_name ?? 'Walk-in' }}
                        @if($sale->customer)
                            <br>
                            <span class="text-xs text-gray-500">
                                {{ ucfirst($sale->customer->customer_type ?? 'N/A') }}
                            </span>
                        @endif
                    </td>

                    <!-- Product -->
                    <td class="text-gray-600">
                        {{ $sale->product->product_name ?? '-' }}
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

                    <!-- Quantity -->
                    <td class="text-gray-800">
                        {{ number_format($sale->quantity, 2) }}
                    </td>

                    <!-- Unit Price -->
                    <td class="text-gray-800">
                        ₱{{ number_format($sale->unit_price, 2) }}
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

<script>
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
                        ${sale.customer ? `<p class="text-xs text-gray-600">${sale.customer.customer_type.toUpperCase()}</p>` : ''}
                    </div>

                    <div class="mb-3 pb-3 border-b">
                        <p class="text-xs text-gray-600 font-semibold mb-1">SALE DETAILS</p>
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span>Product:</span>
                                <span class="font-semibold">${sale.product?.product_name || '-'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Type:</span>
                                <span class="font-semibold">${sale.sale_type.toUpperCase()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Quantity:</span>
                                <span class="font-semibold">${parseFloat(sale.quantity).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Unit Price:</span>
                                <span class="font-semibold">₱${parseFloat(sale.unit_price).toFixed(2)}</span>
                            </div>
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
