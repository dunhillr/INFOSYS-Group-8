<div class="grid grid-cols-12 gap-4">
    <!-- Product Selection -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Product</label>
        <select name="product_id" class="form-control">
            <option value="">Select product</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $sale->product_id ?? '') == $product->id)>
                    {{ $product->product_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Customer Selection -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-control">
            <option value="">Walk-in / None</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $sale->customer_id ?? '') == $customer->id)>
                    {{ $customer->customer_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Vehicle Selection with Availability Indicator -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Assigned Vehicle <span class="text-gray-500 text-xs">(Optional)</span></label>
        <select name="vehicle_id" class="form-control">
            <option value="">-- No Vehicle Assignment --</option>
            @foreach ($vehicles as $vehicle)
                @php
                    $isUnavailable = in_array($vehicle->status, ['in use', 'not available', 'maintenance']);
                    $statusIcon = match($vehicle->status) {
                        'available' => '✓',
                        'in use' => '🔴',
                        'not available' => '🚫',
                        'maintenance' => '🔧',
                        default => '?'
                    };
                    $statusColor = match($vehicle->status) {
                        'available' => 'text-green-600',
                        'in use' => 'text-red-600',
                        'not available' => 'text-red-700',
                        'maintenance' => 'text-orange-600',
                        default => 'text-gray-600'
                    };
                @endphp
                <option 
                    value="{{ $vehicle->id }}"
                    @selected(old('vehicle_id', $sale->vehicle_id ?? '') == $vehicle->id)
                    @disabled($isUnavailable)
                >
                    {{ $statusIcon }} {{ $vehicle->vehicle_name }} ({{ $vehicle->plate_number }})
                    @if($isUnavailable)
                        — {{ ucfirst($vehicle->status) }}
                    @endif
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-2">
            <span class="text-green-600">✓ Available</span> • 
            <span class="text-red-600">🔴 In Use</span> • 
            <span class="text-red-700">🚫 Not Available</span> • 
            <span class="text-orange-600">🔧 Maintenance</span>
        </p>
    </div>

    <!-- Sale Date -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Sale Date</label>
        <input type="datetime-local" name="sale_date" class="form-control" value="{{ old('sale_date', isset($sale) && $sale->sale_date ? $sale->sale_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
    </div>

    <!-- Sale Type -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Sale Type</label>
        <select name="sale_type" class="form-control" required>
            <option value="retail" @selected(old('sale_type', $sale->sale_type ?? 'retail') === 'retail')>Retail</option>
            <option value="wholesale" @selected(old('sale_type', $sale->sale_type ?? '') === 'wholesale')>Wholesale</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Quantity</label>
        <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" value="{{ old('quantity', $sale->quantity ?? '') }}" required>
    </div>

    <!-- Unit Price -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Unit Price</label>
        <input type="number" step="0.01" min="0" name="unit_price" class="form-control" value="{{ old('unit_price', $sale->unit_price ?? '') }}" required>
    </div>

    <!-- Payment Method -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Payment Method</label>
        <select name="payment_method" class="form-control">
            <option value="">Select Method (Optional)</option>
            <option value="Cash" @selected(old('payment_method', $sale->payment_method ?? '') === 'Cash')>Cash</option>
            <option value="GCash" @selected(old('payment_method', $sale->payment_method ?? '') === 'GCash')>GCash</option>
            <option value="Bank Transfer" @selected(old('payment_method', $sale->payment_method ?? '') === 'Bank Transfer')>Bank Transfer</option>
            <option value="Cheque" @selected(old('payment_method', $sale->payment_method ?? '') === 'Cheque')>Cheque</option>
        </select>
    </div>

    <!-- Payment Status -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Payment Status</label>
        <select name="payment_status" id="payment_status" class="form-control" required>
            <option value="paid" @selected(old('payment_status', $sale->payment_status ?? 'paid') === 'paid')>Paid</option>
            <option value="partial" @selected(old('payment_status', $sale->payment_status ?? '') === 'partial')>Partial</option>
            <option value="unpaid" @selected(old('payment_status', $sale->payment_status ?? '') === 'unpaid')>Unpaid</option>
        </select>
    </div>

    <!-- Amount Paid (Visible only when Partial) -->
    <div class="xl:col-span-6 col-span-12" id="amount_paid_container" style="display: {{ old('payment_status', $sale->payment_status ?? '') === 'partial' ? 'block' : 'none' }};">
        <label class="form-label">Amount Paid</label>
        <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" class="form-control" value="{{ old('amount_paid', $sale->amount_paid ?? '') }}">
    </div>

    <!-- Notes -->
    <div class="xl:col-span-12 col-span-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $sale->notes ?? '') }}</textarea>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentStatus = document.getElementById('payment_status');
        const amountPaidContainer = document.getElementById('amount_paid_container');
        const amountPaidInput = document.getElementById('amount_paid');

        if(paymentStatus && amountPaidContainer && amountPaidInput) {
            function toggleAmountPaid() {
                if (paymentStatus.value === 'partial') {
                    amountPaidContainer.style.display = 'block';
                    amountPaidInput.required = true;
                } else {
                    amountPaidContainer.style.display = 'none';
                    amountPaidInput.required = false;
                }
            }

            paymentStatus.addEventListener('change', toggleAmountPaid);
            toggleAmountPaid(); // Initial check
        }
    });
</script>