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

<!-- STOCK SUMMARY CARDS -->
@if($inventories->count())
<div class="grid grid-cols-12 gap-4 mb-6">
    @foreach($inventories as $inv)
        <div class="xl:col-span-3 md:col-span-4 col-span-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 
                {{ (float) $inv->current_stock <= (float) $inv->low_stock_threshold ? 'border-red-500' : 'border-green-500' }}
                hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $inv->product->product_name ?? 'Unknown' }}</p>
                <h4 class="text-xl font-bold mt-1 
                    {{ (float) $inv->current_stock <= (float) $inv->low_stock_threshold ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($inv->current_stock, 2) }}
                </h4>
                <p class="text-xs text-gray-400 mt-1">Available Stock</p>
            </div>
        </div>
    @endforeach
</div>
@endif

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
                    <th>Product</th>
                    <th>Batch Ref</th>
                    <th>Qty Produced</th>
                    <th>Available Stock</th>
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

                    <td>
                        @if($production->product)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $production->product->product_name }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    <td class="text-gray-600">
                        {{ $production->batch_reference ?? '-' }}
                    </td>

                    <td class="text-blue-600 font-semibold">
                        {{ number_format($production->quantity_produced, 2) }}
                    </td>

                    <td>
                        @if($production->product_id && isset($inventories[$production->product_id]))
                            @php $stock = (float) $inventories[$production->product_id]->current_stock; @endphp
                            <span class="font-semibold {{ $stock <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($stock, 2) }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
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
                    <td colspan="8" class="text-center py-6 text-gray-400">
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