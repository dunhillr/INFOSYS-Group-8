@extends('layouts.app')
@section('title', 'Vehicles')

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
                Vehicles
            </h3>
            <p class="text-sm text-gray-500">
                Manage your fleet records
            </p>
        </div>

    </div>

    <!-- RIGHT: ADD BUTTON -->
    <a href="{{ route('vehicles.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Add Vehicle
    </a>

</div>

<!-- TABLE CARD -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <!-- HEADER -->
    <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
        Vehicle List
    </div>

    <!-- TABLE -->
    <div class="p-5 overflow-auto">

        <table class="min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr class="text-left border-b text-gray-600">
                    <th class="py-3">Name</th>
                    <th>Plate Number</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($vehicles as $vehicle)
                <tr class="border-b hover:bg-gray-50 transition">

                    <td class="py-3 font-medium text-gray-800">
                        {{ $vehicle->vehicle_name }}
                    </td>

                    <td class="text-gray-600">
                        {{ $vehicle->plate_number ?? '-' }}
                    </td>

                    <td class="text-gray-800 font-semibold">
                        {{ $vehicle->capacity ? number_format($vehicle->capacity, 2) : '-' }}
                    </td>

                    <td>
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                            {{ ucwords(str_replace('_', ' ', $vehicle->status)) }}
                        </span>
                    </td>

                    <td class="flex gap-2 py-3">

                        <a href="{{ route('vehicles.edit', $vehicle) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Edit
                        </a>

                        <form action="{{ route('vehicles.destroy', $vehicle) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this vehicle?')">
                            @csrf 
                            @method('DELETE')

                            <button class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                Delete
                            </button>
                        </form>

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        No vehicles found.
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="p-4 border-t">
        {{ $vehicles->links() }}
    </div>

</div>

@endsection