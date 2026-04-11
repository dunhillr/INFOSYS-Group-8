@extends('layouts.app')
@section('title', 'Vehicles')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Vehicles</h3></div><div><a href="{{ route('vehicles.create') }}" class="ti-btn ti-btn-primary-full">Add Vehicle</a></div></div>
<div class="box"><div class="box-body"><div class="overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Name</th><th>Plate Number</th><th>Capacity</th><th>Status</th><th width="150">Actions</th></tr></thead><tbody>
@forelse ($vehicles as $vehicle)
<tr><td>{{ $vehicle->vehicle_name }}</td><td>{{ $vehicle->plate_number ?? '-' }}</td><td>{{ $vehicle->capacity ? number_format($vehicle->capacity, 2) : '-' }}</td><td>{{ ucwords(str_replace('_', ' ', $vehicle->status)) }}</td><td><a href="{{ route('vehicles.edit', $vehicle) }}" class="ti-btn ti-btn-info-full ti-btn-sm">Edit</a><form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this vehicle?')">@csrf @method('DELETE')<button class="ti-btn ti-btn-danger-full ti-btn-sm">Delete</button></form></td></tr>
@empty <tr><td colspan="5" class="text-center">No vehicles found.</td></tr>
@endforelse
</tbody></table></div>{{ $vehicles->links() }}</div></div>
@endsection
