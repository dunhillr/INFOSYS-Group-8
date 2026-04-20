@extends('layouts.app')
@section('title', 'Productions')

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
                Productions
            </h3>
            <p class="text-sm text-gray-500">
                Manage production records
            </p>
        </div>

    </div>

    <!-- RIGHT: ADD BUTTON -->
    <a href="{{ route('productions.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Add Production
    </a>

</div>

<!-- TABLE CARD -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <!-- HEADER -->
    <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
        Production Records
    </div>

    <!-- TABLE -->
    <div class="p-5 overflow-auto">

        <table class="min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr class="text-left border-b text-gray-600">
                    <th class="py-3">Date</th>
                    <th>Batch Ref</th>
                    <th>Quantity</th>
                    <th>Encoded By</th>
                    <th>Remarks</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($productions as $production)
                <tr class="border-b hover:bg-gray-50 transition">

                    <td class="py-3 text-gray-800">
                        {{ $production->production_date?->format('M d, Y') }}
                    </td>

                    <td class="text-gray-600">
                        {{ $production->batch_reference ?? '-' }}
                    </td>

                    <td class="text-blue-600 font-semibold">
                        {{ number_format($production->quantity_produced, 2) }}
                    </td>

                    <td class="text-gray-800">
                        {{ $production->user->name ?? '-' }}
                    </td>

                    <td class="text-gray-500">
                        {{ $production->remarks ?? '-' }}
                    </td>

                    <td class="flex gap-2 py-3">

                        <a href="{{ route('productions.edit', $production) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Edit
                        </a>

                        <form action="{{ route('productions.destroy', $production) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this production record?')">
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
                    <td colspan="6" class="text-center py-6 text-gray-400">
                        No production records found.
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="p-4 border-t">
        {{ $productions->links() }}
    </div>

</div>

@endsection