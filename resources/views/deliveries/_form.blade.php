<div class="grid grid-cols-12 gap-4">

{{-- Wholesale Sale --}}
<div class="xl:col-span-6 col-span-12">
    <label class="form-label">Wholesale Sale</label>
    <select name="sale_id" class="form-control" required @disabled(isset($delivery))>
        <option value="">Select sale</option>
        @foreach ($sales as $saleOption)
            <option value="{{ $saleOption->id }}" @selected(old('sale_id', $delivery->sale_id ?? '') == $saleOption->id)>
                {{ $saleOption->sale_number }} — {{ $saleOption->customer->customer_name ?? 'No Customer' }}
            </option>
        @endforeach
    </select>
</div>

{{-- Customer --}}
<div class="xl:col-span-6 col-span-12">
    <label class="form-label">Customer</label>
    <select name="customer_id" class="form-control" required @disabled(isset($delivery))>
        <option value="">Select customer</option>
        @foreach ($customers as $customer)
            <option value="{{ $customer->id }}" @selected(old('customer_id', $delivery->customer_id ?? '') == $customer->id)>
                {{ $customer->customer_name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Vehicle --}}
<div class="xl:col-span-6 col-span-12">
    <label class="form-label">Vehicle</label>
    <select name="vehicle_id" class="form-control" @disabled(isset($delivery))>
        <option value="">Unassigned</option>
        @foreach ($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $delivery->vehicle_id ?? '') == $vehicle->id)>
                {{ $vehicle->vehicle_name }}{{ $vehicle->plate_number ? ' - '.$vehicle->plate_number : '' }} (Driver: {{ $vehicle->driver->name ?? 'None' }})
            </option>
        @endforeach
    </select>
</div>

{{-- Destination --}}
<div class="xl:col-span-6 col-span-12">
    <label class="form-label">Destination</label>
    <input type="text" name="destination" class="form-control"
           value="{{ old('destination', $delivery->destination ?? '') }}"
           required @disabled(isset($delivery))>
</div>

{{-- Delivery Date --}}
<div class="xl:col-span-6 col-span-12">
    <label class="form-label">Delivery Date</label>
    <input type="date" name="delivery_date" class="form-control"
           value="{{ old('delivery_date', isset($delivery) ? $delivery->delivery_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
           required @disabled(isset($delivery))>
</div>

{{-- Delivery Time --}}
<div class="xl:col-span-6 col-span-12">
    <label class="form-label">Delivery Time</label>
    <input type="time" name="delivery_time" class="form-control"
           value="{{ old('delivery_time', $delivery->delivery_time ?? '') }}"
           required @disabled(isset($delivery))>
</div>

{{-- Status --}}
<div class="xl:col-span-6 col-span-12">
    <label class="form-label">Status</label>
    <select name="status" class="form-control" required>
        <option value="pending"          @selected(old('status', $delivery->status ?? 'pending') === 'pending')>Pending</option>
        <option value="out_for_delivery" @selected(old('status', $delivery->status ?? '') === 'out_for_delivery')>In Transit</option>
        <option value="delivered"        @selected(old('status', $delivery->status ?? '') === 'delivered')>Delivered</option>
        <option value="cancelled"        @selected(old('status', $delivery->status ?? '') === 'cancelled')>Cancelled</option>
    </select>
</div>

{{-- Notes --}}
<div class="xl:col-span-12 col-span-12">
    <label class="form-label font-semibold">Update Status Notes</label>
    <textarea name="notes" class="form-control" rows="3"
              placeholder="Enter any updates or notes regarding the current delivery status...">{{ old('notes', $delivery->notes ?? '') }}</textarea>
    <div class="text-[10px] text-muted mt-1">These notes will be added to the tracking history.</div>
</div>

</div>