<div class="grid grid-cols-12 gap-4">

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Production Date</label>
        <input type="date" name="production_date" class="form-control"
               value="{{ old('production_date', isset($production) ? $production->production_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Batch Reference</label>
        <input type="text" name="batch_reference" class="form-control"
               value="{{ old('batch_reference', $production->batch_reference ?? '') }}">
    </div>

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Product (Ice Type)</label>
        <select name="product_id" class="form-control" required>
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
        <label class="form-label">Quantity Produced</label>
        <input type="number" step="0.01" min="0.01" name="quantity_produced" class="form-control"
               value="{{ old('quantity_produced', $production->quantity_produced ?? '') }}" required>
    </div>

    <div class="xl:col-span-12 col-span-12">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control" rows="4">{{ old('remarks', $production->remarks ?? '') }}</textarea>
    </div>

</div>