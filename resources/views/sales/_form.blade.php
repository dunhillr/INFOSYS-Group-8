<div class="grid grid-cols-12 gap-4">

    <!-- Customer Selection -->
    <div class="xl:col-span-12 col-span-12">
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
                    // If editing, the currently assigned vehicle should not be disabled even if marked 'in use'
                    $isCurrentVehicle = isset($sale) && $sale->vehicle_id == $vehicle->id;
                    if ($isCurrentVehicle) {
                        $isUnavailable = false;
                    }
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
                    @disabled($vehicle->remaining_capacity <= 0 && !$isCurrentVehicle)
                >
                    {{ $statusIcon }} {{ $vehicle->vehicle_name }} ({{ $vehicle->plate_number }}) 
                    — Available: {{ number_format($vehicle->remaining_capacity, 2) }} kg
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

    <!-- Items Table -->
    <div class="col-span-12 mb-2 mt-4">
        <label class="form-label font-bold text-lg">Order Items</label>
        <div class="border rounded-lg overflow-visible">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-2">Product</th>
                        <th class="px-4 py-2 w-32">Quantity</th>
                        <th class="px-4 py-2 w-32">Unit Price</th>
                        <th class="px-4 py-2 w-32">Subtotal</th>
                        <th class="px-4 py-2 w-16 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="items_table_body">
                    <!-- Rows will be added dynamically -->
                </tbody>
            </table>
            <div class="p-3 bg-gray-50 border-t flex justify-between items-center">
                <button type="button" id="add_item_btn" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-1 px-3 rounded shadow-sm text-sm transition">
                    + Add Product
                </button>
                <div class="text-right flex flex-col items-end">
                    <div class="mb-1">
                        <span class="text-gray-500 text-xs">Total Weight: </span>
                        <span class="text-sm font-bold text-gray-700" id="total_weight_display">0.00 kg</span>
                    </div>
                    <div>
                        <span class="text-gray-600 font-semibold">Items Subtotal: </span>
                        <span class="text-lg font-bold text-gray-800" id="items_subtotal_display">₱0.00</span>
                    </div>
                </div>
            </div>
        </div>
        @error('items') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Delivery Fee -->
    <div class="xl:col-span-4 col-span-12">
        <label class="form-label">Delivery Fee <span class="text-gray-400 text-xs font-normal">(Optional)</span></label>
        <input type="number" step="0.01" min="0" id="delivery_fee_input" name="delivery_fee" class="form-control calc-trigger" value="{{ old('delivery_fee', $sale->delivery_fee ?? '') }}" placeholder="0.00">
    </div>

    <!-- Discount Type -->
    <div class="xl:col-span-4 col-span-12">
        <label class="form-label">Discount Type <span class="text-gray-400 text-xs font-normal">(Optional)</span></label>
        <select name="discount_type" id="discount_type" class="form-control">
            <option value="">-- No Discount --</option>
            <option value="fixed" @selected(old('discount_type', $sale->discount_type ?? '') === 'fixed')>Fixed Amount (₱)</option>
            <option value="percent" @selected(old('discount_type', $sale->discount_type ?? '') === 'percent')>Percentage (%)</option>
        </select>
    </div>

    <!-- Discount Amount -->
    <div class="xl:col-span-4 col-span-12" id="discount_amount_container" style="display: {{ old('discount_type', $sale->discount_type ?? '') ? 'block' : 'none' }};">
        <label class="form-label" id="discount_amount_label">Discount Amount</label>
        <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount_input" class="form-control calc-trigger" value="{{ old('discount_amount', $sale->discount_amount ?? '') }}" placeholder="0">
        <p class="text-xs text-gray-400 mt-1" id="discount_hint"></p>
    </div>

    <!-- Total Amount (Auto-computed, Read Only) -->
    <div class="xl:col-span-12 col-span-12 mt-2">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex justify-between items-center">
            <div>
                <h4 class="text-blue-800 font-bold text-lg mb-0">Total Amount</h4>
                <p class="text-xs text-blue-600 mt-0">Subtotal + Delivery Fee − Discount</p>
            </div>
            <div>
                <input
                    type="text"
                    id="total_amount_display"
                    class="form-control bg-transparent border-0 font-bold text-blue-800 text-2xl text-right p-0 focus:ring-0 w-48"
                    value="0.00"
                    readonly
                >
            </div>
        </div>
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

    <!-- Amount Paid (Visible only when Partial) -->
    <div class="xl:col-span-4 col-span-12" id="amount_paid_container" style="display: {{ old('payment_status', $sale->payment_status ?? '') === 'partial' ? 'block' : 'none' }};">
        <label class="form-label">Amount Paid</label>
        <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" class="form-control calc-trigger" value="{{ old('amount_paid', $sale->amount_paid ?? '') }}">
    </div>

    <!-- Amount Tendered (Cash transactions) -->
    <div class="xl:col-span-4 col-span-12" id="amount_tendered_container" style="display: {{ old('payment_method', $sale->payment_method ?? '') === 'Cash' ? 'block' : 'none' }};">
        <label class="form-label">Amount Tendered</label>
        <div class="relative">
            <input type="number" step="0.01" min="0" name="amount_tendered" id="amount_tendered" class="form-control calc-trigger" value="{{ old('amount_tendered', $sale->amount_tendered ?? '') }}" placeholder="0.00">
        </div>
    </div>

    <!-- Change Amount -->
    <div class="xl:col-span-4 col-span-12" id="change_amount_container" style="display: {{ old('payment_method', $sale->payment_method ?? '') === 'Cash' ? 'block' : 'none' }};">
        <label class="form-label">Change</label>
        <div class="relative">
            <input type="text" id="change_amount_display" class="form-control bg-gray-50 font-bold text-green-600 cursor-not-allowed" value="{{ isset($sale) && $sale->change_amount ? number_format($sale->change_amount, 2) : '0.00' }}" readonly placeholder="0.00">
        </div>
    </div>

    <!-- Notes -->
    <div class="xl:col-span-12 col-span-12 mt-4">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $sale->notes ?? '') }}</textarea>
    </div>
</div>

<!-- Products Data for JS -->
<script>
    const productsData = [
        @foreach($products as $product)
        {
            id: {{ $product->id }},
            name: "{{ addslashes($product->product_name) }}",
            price: {{ $product->default_price ?? 0 }},
            weight: {{ $product->weight_kg ?? 0 }},
            stock: {{ isset($inventories) && isset($inventories[$product->id]) ? $inventories[$product->id]->current_stock : 0 }}
        },
        @endforeach
    ];

    // Initial Items from Old Input or Existing Sale
    let initialItems = [
        @if(old('items'))
            @foreach(old('items') as $item)
            {
                product_id: "{{ $item['product_id'] ?? '' }}",
                quantity: "{{ $item['quantity'] ?? '' }}",
                unit_price: "{{ $item['unit_price'] ?? '' }}"
            },
            @endforeach
        @elseif(isset($sale) && $sale->saleItems->count() > 0)
            @foreach($sale->saleItems as $item)
            {
                product_id: "{{ $item->product_id }}",
                quantity: "{{ $item->quantity }}",
                unit_price: "{{ $item->unit_price }}"
            },
            @endforeach
        @endif
    ];
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Delivery Type Logic ---
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
            toggleVehicleField(); 
        }

        // --- Items Table Logic ---
        const tbody = document.getElementById('items_table_body');
        const addBtn = document.getElementById('add_item_btn');
        let rowCount = 0;

        function createRow(data = {}) {
            const tr = document.createElement('tr');
            tr.className = 'border-b hover:bg-gray-50';
            const index = rowCount++;
            
            // Product Options
            let options = '<option value="">Select product...</option>';
            productsData.forEach(p => {
                const selected = p.id == data.product_id ? 'selected' : '';
                options += `<option value="${p.id}" data-price="${p.price}" data-weight="${p.weight}" data-stock="${p.stock}" ${selected}>${p.name}</option>`;
            });

            tr.innerHTML = `
                <td class="px-4 py-2">
                    <select name="items[${index}][product_id]" class="form-control item-product" required>
                        ${options}
                    </select>
                    <p class="text-[10px] text-gray-500 mt-1 stock-info"></p>
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.01" min="0.01" name="items[${index}][quantity]" class="form-control item-qty calc-trigger" value="${data.quantity || ''}" required placeholder="0">
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.01" min="0" name="items[${index}][unit_price]" class="form-control item-price calc-trigger" value="${data.unit_price || ''}" required placeholder="0.00">
                </td>
                <td class="px-4 py-2 font-semibold text-gray-800 item-subtotal-display">
                    ₱0.00
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" class="text-red-500 hover:text-red-700 remove-item-btn" title="Remove">
                        ✖
                    </button>
                </td>
            `;
            
            tbody.appendChild(tr);

            // Bind events for new row
            const productSelect = tr.querySelector('.item-product');
            const priceInput = tr.querySelector('.item-price');
            const qtyInput = tr.querySelector('.item-qty');
            const removeBtn = tr.querySelector('.remove-item-btn');
            const stockInfo = tr.querySelector('.stock-info');

            function updateProductDetails() {
                const option = productSelect.options[productSelect.selectedIndex];
                if (option.value) {
                    const price = option.getAttribute('data-price');
                    const stock = option.getAttribute('data-stock');
                    
                    // Only auto-fill if not prepopulated (like from old input) or if user manually changed
                    if (!data.unit_price || productSelect.dataset.changed) {
                        priceInput.value = parseFloat(price).toFixed(2);
                    }
                    
                    stockInfo.textContent = `Stock: ${parseFloat(stock).toLocaleString('en-US')}`;
                    if (parseFloat(stock) <= 0) {
                        stockInfo.classList.add('text-red-500');
                    } else {
                        stockInfo.classList.remove('text-red-500');
                    }
                } else {
                    stockInfo.textContent = '';
                }
                calculateTotals();
            }

            productSelect.addEventListener('change', function() {
                productSelect.dataset.changed = 'true';
                updateProductDetails();
            });
            
            qtyInput.addEventListener('input', calculateTotals);
            priceInput.addEventListener('input', calculateTotals);
            
            removeBtn.addEventListener('click', function() {
                if (tbody.children.length > 1) {
                    tr.remove();
                    calculateTotals();
                } else {
                    alert('You must have at least one item in the sale.');
                }
            });

            // Initial call if data exists
            if (data.product_id) {
                updateProductDetails();
            }
        }

        if (initialItems.length > 0) {
            initialItems.forEach(item => createRow(item));
        } else {
            createRow(); // Create at least one empty row
        }

        addBtn.addEventListener('click', () => createRow());

        // --- Calculation Logic ---
        const deliveryFeeInput = document.getElementById('delivery_fee_input');
        const discountTypeSelect = document.getElementById('discount_type');
        const discountAmountInput = document.getElementById('discount_amount_input');
        const discountContainer = document.getElementById('discount_amount_container');
        const discountHint = document.getElementById('discount_hint');
        const discountLabel = document.getElementById('discount_amount_label');
        const totalDisplay = document.getElementById('total_amount_display');
        const itemsSubtotalDisplay = document.getElementById('items_subtotal_display');
        
        const paymentStatus = document.getElementById('payment_status');
        const amountPaidContainer = document.getElementById('amount_paid_container');
        const amountPaidInput = document.getElementById('amount_paid');
        const paymentMethodSelect = document.getElementById('payment_method');
        const amountTenderedContainer = document.getElementById('amount_tendered_container');
        const changeAmountContainer = document.getElementById('change_amount_container');
        const amountTenderedInput = document.getElementById('amount_tendered');
        const changeAmountDisplay = document.getElementById('change_amount_display');

        // Discount Type Toggle
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
                calculateTotals();
            });
            // Initial toggle
            if (discountTypeSelect.value) {
                discountTypeSelect.dispatchEvent(new Event('change'));
            }
        }

        // Payment Status Toggle
        if (paymentStatus) {
            paymentStatus.addEventListener('change', function() {
                if (this.value === 'partial') {
                    amountPaidContainer.style.display = 'block';
                    amountPaidInput.required = true;
                } else {
                    amountPaidContainer.style.display = 'none';
                    amountPaidInput.required = false;
                }
                calculateTotals();
            });
            paymentStatus.dispatchEvent(new Event('change'));
        }

        // Payment Method Toggle
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                if (this.value === 'Cash') {
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
                calculateTotals();
            });
            paymentMethodSelect.dispatchEvent(new Event('change'));
        }

        function calculateTotals() {
            let subtotal = 0;
            let totalWeight = 0;
            
            // Calculate item subtotals
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const productSelect = row.querySelector('.item-product');
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const productWeight = selectedOption ? (parseFloat(selectedOption.getAttribute('data-weight')) || 0) : 0;
                
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const rowSub = qty * price;
                subtotal += rowSub;
                totalWeight += (qty * productWeight);
                
                row.querySelector('.item-subtotal-display').textContent = `₱${rowSub.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            });

            itemsSubtotalDisplay.textContent = `₱${subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('total_weight_display').textContent = `${totalWeight.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} kg`;

            // Add Delivery Fee
            const deliveryFee = parseFloat(deliveryFeeInput.value) || 0;
            
            // Subtract Discount
            const discountType = discountTypeSelect ? discountTypeSelect.value : '';
            const discountVal = parseFloat(discountAmountInput ? discountAmountInput.value : 0) || 0;
            
            let discountValue = 0;
            if (discountType === 'percent') {
                discountValue = subtotal * (discountVal / 100);
            } else if (discountType === 'fixed') {
                discountValue = discountVal;
            }

            const total = Math.max(0, subtotal + deliveryFee - discountValue);
            totalDisplay.value = `₱ ${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Calculate Change
            const tendered = parseFloat(amountTenderedInput.value) || 0;
            let amountToPay = total;
            if (paymentStatus.value === 'partial') {
                amountToPay = parseFloat(amountPaidInput.value) || 0;
            }

            const change = Math.max(0, tendered - amountToPay);
            changeAmountDisplay.value = change.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Bind global calc triggers
        document.querySelectorAll('.calc-trigger').forEach(el => {
            el.addEventListener('input', calculateTotals);
        });

        // Initial calculation
        calculateTotals();
    });
</script>