<div class="grid grid-cols-12 gap-4">

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Product Name <span class="text-red-500">*</span></label>
        <input type="text" name="product_name" class="form-control"
               value="{{ old('product_name', $product->product_name ?? '') }}" required>
        @error('product_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Product Code</label>
        <input type="text" name="product_code" class="form-control"
               value="{{ old('product_code', $product->product_code ?? '') }}">
        @error('product_code')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Default Price <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" min="0" name="default_price" class="form-control"
               value="{{ old('default_price', $product->default_price ?? 0) }}" required>
    </div>

    {{-- ── Weight ── --}}
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">Weight (kg) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" min="0.01" name="weight_kg" class="form-control"
               value="{{ old('weight_kg', $product->weight_kg ?? '') }}" required placeholder="e.g. 150 for Block Ice, 25 for Crushed Ice bag">
        <p class="text-xs text-gray-500 mt-1">Used to automatically compute production yield.</p>
        @error('weight_kg')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- ── Parent Product (raw material source) ── --}}
    <div class="xl:col-span-6 col-span-12">
        <label class="form-label">
            Raw Material Source
            <span class="text-xs text-gray-400 font-normal ml-1">(optional — e.g. Block Ice for Crushed Ice)</span>
        </label>
        <select name="parent_product_id" class="form-control">
            <option value="">-- None (standalone product) --</option>
            @foreach($allProducts as $p)
                @if(($product->id ?? null) != $p->id)
                    <option value="{{ $p->id }}"
                        @selected(old('parent_product_id', $product->parent_product_id ?? '') == $p->id)>
                        {{ $p->product_name }}
                    </option>
                @endif
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">
            Set this if the product is derived from another product during production.
        </p>
        @error('parent_product_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="xl:col-span-12 col-span-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="xl:col-span-12 col-span-12">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   id="product_is_active" @checked(old('is_active', $product->is_active ?? true))>
            <label class="form-check-label" for="product_is_active">Active</label>
        </div>
    </div>

</div>