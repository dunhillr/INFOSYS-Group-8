@extends('layouts.app')
@section('title', 'Sales')

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
                Sales
            </h3>
            <p class="text-sm text-gray-500">
                Manage sales transactions
            </p>
        </div>

    </div>

    <!-- RIGHT: ADD BUTTON -->
    <a href="{{ route('sales.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Add Sale
    </a>

</div>

<!-- TABLE CARD -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <!-- HEADER -->
    <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
        Sales Records
    </div>

    <!-- FILTERS -->
    <div class="p-5 bg-gray-50 border-b border-gray-100">
        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-12 gap-4">
            <div class="xl:col-span-4 col-span-12">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Sale No. or Customer Name...">
            </div>
            <div class="xl:col-span-3 col-span-12">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="xl:col-span-3 col-span-12">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="xl:col-span-2 col-span-12 flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex-1">
                    Filter
                </button>
                <a href="{{ route('sales.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition" title="Reset Filters">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="p-5 overflow-auto">

        <table class="min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr class="text-left border-b text-gray-600">
                    <th class="py-3">Sale No.</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($sales as $sale)
                <tr class="border-b hover:bg-gray-50 transition">

                    <td class="py-3 font-medium text-gray-800">
                        {{ $sale->sale_number }}
                    </td>

                    <td class="text-gray-600">
                        {{ $sale->customer->customer_name ?? 'Walk-in' }}
                    </td>

                    <td class="text-gray-800 text-xs">
                        @foreach($sale->saleItems as $item)
                            <div class="mb-1">
                                <span class="font-semibold">{{ $item->product->product_name ?? 'Unknown' }}</span>
                                <span class="text-gray-500">(x{{ number_format($item->quantity, 2) }})</span>
                            </div>
                        @endforeach
                    </td>

                    <td class="text-blue-600 font-semibold">
                        {{ number_format($sale->total_amount, 2) }}
                    </td>

                    <td>
                        @if($sale->payment_status === 'paid')
                            <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                Paid
                            </span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">
                                Partial
                            </span>
                            <div class="text-xs text-gray-500 mt-1">Bal: {{ number_format($sale->balance_due, 2) }}</div>
                        @else
                            <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full">
                                Unpaid
                            </span>
                            <div class="text-xs text-gray-500 mt-1">Bal: {{ number_format($sale->balance_due, 2) }}</div>
                        @endif
                        
                        @if($sale->payment_method)
                            <div class="text-xs text-blue-500 mt-1 font-medium">{{ $sale->payment_method }}</div>
                        @endif
                    </td>

                    <td class="text-gray-600">
                        {{ $sale->sale_date?->format('M d, Y h:i A') }}
                    </td>

                    <td class="flex gap-2 py-3">

                        <a href="{{ route('sales.edit', $sale) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Edit
                        </a>

                        <form action="{{ route('sales.destroy', $sale) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this sale record?')">
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
                    <td colspan="10" class="text-center py-6 text-gray-400">
                        No sales records found.
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="p-4 border-t">
        {{ $sales->links() }}
    </div>

</div>

@endsection