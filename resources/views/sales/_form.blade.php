<div class="grid grid-cols-12 gap-4">
    <!-- Product Selection -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Product</label>
        <select name="product_id" id="product_select" class="form-control">
            <option value="" data-price="" data-stock="">Select product</option>
            @foreach ($products as $product)
                @php
                    $stock = isset($inventories) && isset($inventories[$product->id]) ? $inventories[$product->id]->current_stock : 0;
                @endphp
                <option value="{{ $product->id }}" data-price="{{ $product->default_price ?? '' }}" data-stock="{{ $stock }}" @selected(old('product_id', $sale->product_id ?? '') == $product->id)>
                    {{ $product->product_name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs mt-1 font-medium text-gray-500" id="stock_indicator" style="display: none;">
            Available Stock: <span id="stock_amount" class="font-bold text-blue-600"></span> bags
        </p>
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

    <!-- Delivery Type -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Delivery Type</label>
        <select name="delivery_type" id="delivery_type" class="form-control" required>
            <option value="walk_in"   @selected(old('delivery_type', $sale->delivery_type ?? 'walk_in') === 'walk_in')>🚶 Walk-in Pickup</option>
            <option value="delivery"  @selected(old('delivery_type', $sale->delivery_type ?? '') === 'delivery')>🚚 Delivery</option>
        </select>
        <p class="text-xs text-gray-400 mt-1">Walk-in will disable vehicle assignment.</p>
    </div>

    <!-- Vehicle Selection with Availability Indicator -->
    <div class="xl:col-span-6 col-span-12" id="vehicle_field">
        <label class="form-label">Assigned Vehicle <span class="text-gray-500 text-xs">(Optional)</span></label>
        <select name="vehicle_id" id="vehicle_select" class="form-control">
            <option value="">-- No Vehicle Assignment --</option>
            @foreach ($vehicles as $vehicle)
                @php
                    $isUnavailable = in_array($vehicle->status, ['in use', 'not available', 'maintenance']);
                    $statusIcon = match($vehicle->status) {
                        'available'     => '✓',
                        'in use'        => '🔴',
                        'not available' => '🚫',
                        'maintenance'   => '🔧',
                        default         => '?'
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


    <!-- Quantity -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Quantity</label>
        <input type="number" step="0.01" min="0.01" id="quantity_input" name="quantity" class="form-control" value="{{ old('quantity', $sale->quantity ?? '') }}" required>
    </div>

    <!-- Product Price (auto-filled from selected product) -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Product Price</label>
        <input type="number" step="0.01" min="0" id="unit_price_input" name="unit_price" class="form-control" value="{{ old('unit_price', $sale->unit_price ?? '') }}" required placeholder="0.00">
        <p class="text-xs text-gray-400 mt-1">Auto-filled when a product is selected. You may override.</p>
    </div>

    <!-- Discount Type -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Discount Type <span class="text-gray-400 text-xs font-normal">(Optional)</span></label>
        <select name="discount_type" id="discount_type" class="form-control">
            <option value="">-- No Discount --</option>
            <option value="fixed" @selected(old('discount_type', $sale->discount_type ?? '') === 'fixed')>Fixed Amount (₱)</option>
            <option value="percent" @selected(old('discount_type', $sale->discount_type ?? '') === 'percent')>Percentage (%)</option>
        </select>
    </div>

    <!-- Discount Amount -->
    <div class="xl:col-span-6 col-span-12" id="discount_amount_container" style="display: {{ old('discount_type', $sale->discount_type ?? '') ? 'block' : 'none' }};">
        <label class="form-label" id="discount_amount_label">Discount Amount</label>
        <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount_input" class="form-control" value="{{ old('discount_amount', $sale->discount_amount ?? '') }}" placeholder="0">
        <p class="text-xs text-gray-400 mt-1" id="discount_hint"></p>
    </div>

    <!-- Total Amount (Auto-computed, Read Only) -->
    <div class="xl:col-span-12 col-span-12">
        <label class="form-label">Total Amount <span class="text-xs text-gray-400 font-normal">(Auto-computed)</span></label>
        <input
            type="text"
            id="total_amount_display"
            class="form-control bg-gray-50 font-bold text-gray-800 cursor-not-allowed"
            value="{{ old('quantity') && old('unit_price') ? number_format(old('quantity') * old('unit_price'), 2) : (isset($sale) && $sale->total_amount ? number_format($sale->total_amount, 2) : '0.00') }}"
            readonly
            placeholder="0.00"
        >
        <p class="text-xs text-gray-400 mt-1">Total Amount = (Quantity × Product Price) − Discount</p>
    </div>

    <!-- Payment Method -->
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Payment Method</label>
        <select name="payment_method" id="payment_method" class="form-control">
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

    <!-- Amount Tendered (Cash transactions) -->
    <div class="xl:col-span-6 col-span-12" id="amount_tendered_container" style="display: {{ old('payment_method', $sale->payment_method ?? '') === 'Cash' ? 'block' : 'none' }};">
        <label class="form-label">Amount Tendered</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-semibold">₱</span>
            <input type="number" step="0.01" min="0" name="amount_tendered" id="amount_tendered" class="form-control pl-8" value="{{ old('amount_tendered', $sale->amount_tendered ?? '') }}" placeholder="0.00">
        </div>
    </div>

    <!-- Change Amount -->
    <div class="xl:col-span-6 col-span-12" id="change_amount_container" style="display: {{ old('payment_method', $sale->payment_method ?? '') === 'Cash' ? 'block' : 'none' }};">
        <label class="form-label">Change</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-green-500 font-semibold">₱</span>
            <input type="text" id="change_amount_display" class="form-control pl-8 bg-gray-50 font-bold text-green-600 cursor-not-allowed" value="{{ isset($sale) && $sale->change_amount ? number_format($sale->change_amount, 2) : '0.00' }}" readonly placeholder="0.00">
        </div>
    </div>

    <!-- Notes -->
    <div class="xl:col-span-12 col-span-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $sale->notes ?? '') }}</textarea>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Delivery Type: disable vehicle field on Walk-in ---
        const deliveryTypeSelect = document.getElementById('delivery_type');
        const vehicleSelect      = document.getElementById('vehicle_select');
        const vehicleField       = document.getElementById('vehicle_field');

        function toggleVehicleField() {
            if (!deliveryTypeSelect || !vehicleSelect) return;
            const isWalkIn = deliveryTypeSelect.value === 'walk_in';
            vehicleSelect.disabled = isWalkIn;
            vehicleField.style.opacity = isWalkIn ? '0.4' : '1';
            if (isWalkIn) {
                vehicleSelect.value = '';
            }
        }

        if (deliveryTypeSelect) {
            deliveryTypeSelect.addEventListener('change', toggleVehicleField);
            toggleVehicleField(); // Run on load
        }

        // --- Auto-fill Product Price and Show Stock when product is selected ---
        const productSelect   = document.getElementById('product_select');
        const unitPriceInput  = document.getElementById('unit_price_input');
        const stockIndicator  = document.getElementById('stock_indicator');
        const stockAmount     = document.getElementById('stock_amount');

        if (productSelect) {
            function updateProductDetails() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                
                // Update Price
                if (unitPriceInput) {
                    const price = selectedOption.getAttribute('data-price');
                    // Only auto-fill if it's an actual change by user or if the input is currently empty
                    // Since computeTotal uses value, setting value here forces the old price.
                    // We check if price exists and is not empty.
                    if (price && price !== '' && !productSelect.dataset.initialized) {
                         // Only set price on change, not on initial load if unit price already has a value (e.g. edit mode)
                    }
                    if (price && price !== '') {
                         // Only override if the user triggered the change event manually, not on initial DOMContentLoaded
                         // Actually, we do want to set it if they pick a new product.
                         // But we don't want to overwrite an old input or an existing sale's price on page load.
                    }
                }
            }

            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                // Update Price
                if (unitPriceInput) {
                    const price = selectedOption.getAttribute('data-price');
                    if (price && price !== '') {
                        unitPriceInput.value = parseFloat(price).toFixed(2);
                        computeTotal();
                    }
                }
                
                // Update Stock Display
                updateStockDisplay(selectedOption);
            });

            function updateStockDisplay(selectedOption) {
                 if (stockIndicator && stockAmount) {
                    const stock = selectedOption.getAttribute('data-stock');
                    if (stock && stock !== '') {
                        stockAmount.textContent = parseFloat(stock).toLocaleString('en-US', { maximumFractionDigits: 2 });
                        stockIndicator.style.display = 'block';
                    } else {
                        stockIndicator.style.display = 'none';
                    }
                }
            }

            // Run stock display on load
            updateStockDisplay(productSelect.options[productSelect.selectedIndex]);
        }

        // --- Auto-compute Total Amount (with discount) ---
        const quantityInput        = document.getElementById('quantity_input');
        const totalDisplay         = document.getElementById('total_amount_display');
        const discountTypeSelect   = document.getElementById('discount_type');
        const discountAmountInput  = document.getElementById('discount_amount_input');
        const discountContainer    = document.getElementById('discount_amount_container');
        const discountHint         = document.getElementById('discount_hint');
        const discountLabel        = document.getElementById('discount_amount_label');

        function computeTotal() {
            const qty      = parseFloat(quantityInput.value)  || 0;
            const price    = parseFloat(unitPriceInput.value) || 0;
            const subtotal = qty * price;

            const discountType = discountTypeSelect ? discountTypeSelect.value : '';
            const discountVal  = parseFloat(discountAmountInput ? discountAmountInput.value : 0) || 0;

            let discountValue = 0;
            if (discountType === 'percent') {
                discountValue = subtotal * (discountVal / 100);
            } else if (discountType === 'fixed') {
                discountValue = discountVal;
            }

            const total = Math.max(0, subtotal - discountValue);
            totalDisplay.value = total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Toggle discount amount field visibility
        if (discountTypeSelect) {
            discountTypeSelect.addEventListener('change', function() {
                if (this.value) {
                    discountContainer.style.display = 'block';
                    if (this.value === 'percent') {
                        discountLabel.textContent = 'Discount (%)'
                        discountHint.textContent  = 'Enter a value between 0 and 100';
                    } else {
                        discountLabel.textContent = 'Discount Amount (₱)';
                        discountHint.textContent  = 'Enter fixed discount in pesos';
                    }
                } else {
                    discountContainer.style.display = 'none';
                    if (discountAmountInput) discountAmountInput.value = '';
                }
                computeTotal();
            });
            // Set label on load (for edit page)
            const initialType = discountTypeSelect.value;
            if (initialType === 'percent') { discountLabel.textContent = 'Discount (%)'; discountHint.textContent = 'Enter a value between 0 and 100'; }
            else if (initialType === 'fixed') { discountLabel.textContent = 'Discount Amount (₱)'; discountHint.textContent = 'Enter fixed discount in pesos'; }
        }

        if (discountAmountInput) {
            discountAmountInput.addEventListener('input', computeTotal);
        }

        if (quantityInput && unitPriceInput && totalDisplay) {
            quantityInput.addEventListener('input', computeTotal);
            unitPriceInput.addEventListener('input', computeTotal);
            computeTotal(); // Run once on load
        }

        // --- Toggle Amount Paid field ---
        const paymentStatus      = document.getElementById('payment_status');
        const amountPaidContainer = document.getElementById('amount_paid_container');
        const amountPaidInput    = document.getElementById('amount_paid');

        if (paymentStatus && amountPaidContainer && amountPaidInput) {
            function toggleAmountPaid() {
                if (paymentStatus.value === 'partial') {
                    amountPaidContainer.style.display = 'block';
                    amountPaidInput.required = true;
                } else {
                    amountPaidContainer.style.display = 'none';
                    amountPaidInput.required = false;
                }
                computeChange();
            }
            paymentStatus.addEventListener('change', toggleAmountPaid);
            toggleAmountPaid();
        }

        // --- Amount Tendered and Change Logic ---
        const paymentMethodSelect = document.getElementById('payment_method');
        const amountTenderedContainer = document.getElementById('amount_tendered_container');
        const changeAmountContainer = document.getElementById('change_amount_container');
        const amountTenderedInput = document.getElementById('amount_tendered');
        const changeAmountDisplay = document.getElementById('change_amount_display');

        function computeChange() {
            if (!amountTenderedInput || !changeAmountDisplay || !paymentStatus || !totalDisplay) return;

            const tendered = parseFloat(amountTenderedInput.value) || 0;
            // Get raw total directly (not from formatted totalDisplay.value)
            const qty      = parseFloat(quantityInput.value)  || 0;
            const price    = parseFloat(unitPriceInput.value) || 0;
            const subtotal = qty * price;

            const discountTypeSelect = document.getElementById('discount_type');
            const discountAmountInput = document.getElementById('discount_amount_input');
            const discountType = discountTypeSelect ? discountTypeSelect.value : '';
            const discountVal  = parseFloat(discountAmountInput ? discountAmountInput.value : 0) || 0;

            let discountValue = 0;
            if (discountType === 'percent') {
                discountValue = subtotal * (discountVal / 100);
            } else if (discountType === 'fixed') {
                discountValue = discountVal;
            }
            const total = Math.max(0, subtotal - discountValue);
            
            let amountToPay = total;
            if (paymentStatus.value === 'partial') {
                amountToPay = parseFloat(amountPaidInput ? amountPaidInput.value : 0) || 0;
            }

            const change = Math.max(0, tendered - amountToPay);
            changeAmountDisplay.value = change.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        if (paymentMethodSelect && amountTenderedContainer && changeAmountContainer) {
            function toggleTenderedFields() {
                if (paymentMethodSelect.value === 'Cash') {
                    amountTenderedContainer.style.display = 'block';
                    changeAmountContainer.style.display = 'block';
                    amountTenderedInput.required = true;
                } else {
                    amountTenderedContainer.style.display = 'none';
                    changeAmountContainer.style.display = 'none';
                    amountTenderedInput.required = false;
                    amountTenderedInput.value = '';
                    changeAmountDisplay.value = '0.00';
                }
                computeChange();
            }
            paymentMethodSelect.addEventListener('change', toggleTenderedFields);
            toggleTenderedFields();
        }

        if (amountTenderedInput) {
            amountTenderedInput.addEventListener('input', computeChange);
        }
        if (amountPaidInput) {
            amountPaidInput.addEventListener('input', computeChange);
        }
        
        // Ensure computeTotal also updates change
        const originalComputeTotal = computeTotal;
        computeTotal = function() {
            originalComputeTotal();
            computeChange();
        };

    });
</script>