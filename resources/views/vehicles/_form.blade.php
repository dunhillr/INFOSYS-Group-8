<div class="grid grid-cols-12 gap-4">
<div class="xl:col-span-6 col-span-12"><label class="form-label">Vehicle Name</label><input type="text" name="vehicle_name" class="form-control" value="{{ old('vehicle_name', $vehicle->vehicle_name ?? '') }}" required></div>
<div class="xl:col-span-6 col-span-12"><label class="form-label">Plate Number</label><input type="text" name="plate_number" class="form-control" value="{{ old('plate_number', $vehicle->plate_number ?? '') }}"></div>
<div class="xl:col-span-6 col-span-12"><label class="form-label">Capacity (kg)</label><input type="number" step="0.01" min="0" name="capacity" class="form-control" value="{{ old('capacity', $vehicle->capacity ?? '') }}"></div>
<div class="xl:col-span-6 col-span-12"><label class="form-label">Status</label><select name="status" class="form-control" required><option value="available" @selected(old('status', $vehicle->status ?? 'available') === 'available')>Available</option><option value="reserved" @selected(old('status', $vehicle->status ?? '') === 'reserved')>Reserved</option><option value="in_transit" @selected(old('status', $vehicle->status ?? '') === 'in_transit')>In Transit</option><option value="maintenance" @selected(old('status', $vehicle->status ?? '') === 'maintenance')>Maintenance</option></select></div>
<div class="xl:col-span-12 col-span-12">
    <label class="form-label">Assigned Driver</label>
    <select name="assigned_driver_id" class="form-control">
        <option value="">— No Driver Assigned —</option>
        @foreach ($drivers as $driver)
            <option value="{{ $driver->id }}" @selected(old('assigned_driver_id', $vehicle->assigned_driver_id ?? '') == $driver->id)>
                {{ $driver->name }}
            </option>
        @endforeach
    </select>
</div>
</div>