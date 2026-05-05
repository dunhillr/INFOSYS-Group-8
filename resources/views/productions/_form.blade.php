{{-- 
    Pass products as JSON so the JS can look up parent info without AJAX.
    Each product object includes: id, product_name, parent_product_id, parent_product_name
--}}
@php
    $productsJson = $products->map(fn($p) => [
        'id'            => $p->id,
        'name'          => $p->product_name,
        'parent_id'     => $p->parent_product_id,
        'parent_name'   => $p->parentProduct?->product_name,
        'weight'        => $p->weight_kg,
        'parent_weight' => $p->parentProduct?->weight_kg,
    ])->keyBy('id')->toJson();
@endphp

<div class="grid grid-cols-12 gap-4">

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Production Date</label>
        <input type="date" name="production_date" class="form-control"
               value="{{ old('production_date', isset($production) ? $production->production_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>

    {{-- ── STEP 1: Select the output product ── --}}
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Product / Ice Type <span class="text-red-500">*</span></label>
        <select id="product_id_select" name="product_id" class="form-control" required>
            <option value="">-- Select Product --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    @selected(old('product_id', $production->product_id ?? '') == $product->id)>
                    {{ $product->product_name }}
                </option>
            @endforeach
        </select>
        @error('product_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Quantity Produced <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" min="0.01" name="quantity_produced" id="quantity_produced_input" class="form-control"
               value="{{ old('quantity_produced', $production->quantity_produced ?? '') }}" required>
        <p class="text-xs text-gray-500 mt-1" id="quantity_produced_hint"></p>
        @error('quantity_produced')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- ── STEP 2: Parent (raw material) fields — shown only when product has a parent ── --}}
    <div id="parent_fields_wrapper" class="col-span-12" style="display:none">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 grid grid-cols-12 gap-4">

            <div class="col-span-12">
                <p class="text-sm font-semibold text-amber-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Raw Material Consumption
                </p>
                <p id="parent_description" class="text-xs text-amber-700 mt-1"></p>
            </div>

            {{-- Hidden: the actual parent_product_id that gets submitted --}}
            <input type="hidden" id="parent_product_id_input" name="parent_product_id"
                   value="{{ old('parent_product_id', $production->parent_product_id ?? '') }}">

            <div class="xl:col-span-6 col-span-12">
                <label class="form-label">Raw Material Used</label>
                <input type="text" id="parent_product_display" class="form-control bg-gray-100" readonly
                       placeholder="Auto-filled based on selected product">
                @error('parent_product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="xl:col-span-6 col-span-12">
                <label class="form-label" id="parent_qty_label">Quantity of Raw Material Used <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01"
                       id="parent_quantity_used_input"
                       name="parent_quantity_used"
                       class="form-control"
                       value="{{ old('parent_quantity_used', $production->parent_quantity_used ?? '') }}"
                       placeholder="e.g. 2">
                <p class="text-xs text-gray-500 mt-1" id="parent_qty_hint"></p>
                @error('parent_quantity_used')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

</div>

{{-- ── JavaScript: Dynamically show/hide parent fields ── --}}
<script>
(function () {
    const PRODUCTS = @json(json_decode($productsJson));

    const productSelect   = document.getElementById('product_id_select');
    const parentWrapper   = document.getElementById('parent_fields_wrapper');
    const parentIdInput   = document.getElementById('parent_product_id_input');
    const parentDisplay   = document.getElementById('parent_product_display');
    const parentQtyInput  = document.getElementById('parent_quantity_used_input');
    const parentDesc      = document.getElementById('parent_description');
    const parentQtyHint   = document.getElementById('parent_qty_hint');
    const qtyProducedInput = document.getElementById('quantity_produced_input');
    const qtyProducedHint  = document.getElementById('quantity_produced_hint');

    let currentProduct = null;

    function updateParentFields() {
        const productId = productSelect.value;
        currentProduct   = productId ? PRODUCTS[productId] : null;

        if (currentProduct && currentProduct.parent_id) {
            // Show the raw material section
            parentWrapper.style.display = 'block';
            parentIdInput.value   = currentProduct.parent_id;
            parentDisplay.value   = currentProduct.parent_name;
            parentQtyInput.required = true;
            
            // Calculate yield info text
            const parentWeight = parseFloat(currentProduct.parent_weight) || 0;
            const currentWeight = parseFloat(currentProduct.weight) || 0;
            
            let yieldText = '';
            if (parentWeight > 0 && currentWeight > 0) {
                const yieldRate = parentWeight / currentWeight;
                yieldText = `(Formula: ${parentWeight}kg / ${currentWeight}kg = ${yieldRate.toFixed(2)} bags per 1 ${currentProduct.parent_name})`;
            } else {
                yieldText = "(Warning: Missing weight setup in Products)";
            }
            
            // Make quantity produced readonly and auto-calculated
            qtyProducedInput.readOnly = true;
            qtyProducedInput.classList.add('bg-gray-100');
            qtyProducedHint.textContent = `Auto-calculated based on weight. ${yieldText}`;

            parentDesc.textContent =
                `"${currentProduct.name}" is produced from "${currentProduct.parent_name}". ` +
                `Enter how many units of "${currentProduct.parent_name}" will be consumed.`;

            parentQtyHint.textContent =
                `e.g. Enter the number of ${currentProduct.parent_name} used. The quantity produced will auto-update.`;
                
            calculateYield();
        } else {
            // Hide — product has no parent (e.g. Tube Ice)
            parentWrapper.style.display = 'none';
            parentIdInput.value     = '';
            parentDisplay.value     = '';
            parentQtyInput.value    = '';
            parentQtyInput.required = false;
            
            // Make quantity produced editable again
            qtyProducedInput.readOnly = false;
            qtyProducedInput.classList.remove('bg-gray-100');
            qtyProducedHint.textContent = '';
        }
    }
    
    function calculateYield() {
        if (currentProduct && currentProduct.parent_id) {
            const rawMaterialQty = parseFloat(parentQtyInput.value) || 0;
            const parentWeight = parseFloat(currentProduct.parent_weight) || 0;
            const currentWeight = parseFloat(currentProduct.weight) || 0;
            
            if (rawMaterialQty > 0 && parentWeight > 0 && currentWeight > 0) {
                const yieldRate = parentWeight / currentWeight;
                qtyProducedInput.value = (rawMaterialQty * yieldRate).toFixed(2);
            } else {
                qtyProducedInput.value = '';
            }
        }
    }

    // Run on page load (for edit form pre-population)
    updateParentFields();

    // Run on change
    productSelect.addEventListener('change', updateParentFields);
    parentQtyInput.addEventListener('input', calculateYield);
})();
</script>