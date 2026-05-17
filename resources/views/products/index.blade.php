@extends('layouts.app')
@section('title', 'Products')

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
                Products
            </h3>
            <p class="text-sm text-gray-500">
                Manage your product inventory
            </p>
        </div>

    </div>

    <!-- RIGHT: ADD BUTTON -->
    <a href="{{ route('products.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Add Product
    </a>

</div>

<!-- TABLE CARD -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <!-- HEADER (VALEx STYLE BLUE BAR) -->
    <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
        Product List
    </div>

    <!-- TABLE -->
    <div class="p-5 overflow-auto">

        <table class="min-w-full whitespace-nowrap text-sm">

            <thead>
                <tr class="text-left border-b text-gray-600">
                    <th class="py-3">Name</th>
                    <th>Code</th>
                    <th>Default Price</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($products as $product)
                <tr class="border-b hover:bg-gray-50 transition">

                    <td class="py-3 font-medium text-gray-800">
                        {{ $product->product_name }}
                    </td>

                    <td class="text-gray-600">
                        {{ $product->product_code ?? '-' }}
                    </td>

                    <td class="text-blue-600 font-semibold">
                        {{ number_format($product->default_price, 2) }}
                    </td>

                    <td>
                        @if($product->is_active)
                            <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                Active
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full">
                                Inactive
                            </span>
                        @endif
                    </td>

                    <td class="flex gap-2 py-3">

                        <a href="{{ route('products.edit', $product) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Edit
                        </a>

                        @if(Auth::user()->isOwner())
                        <form action="{{ route('products.destroy', $product) }}" 
                              method="POST"
                              data-confirm-delete
                              data-confirm-item="{{ $product->product_name }}">
                            @csrf 
                            @method('DELETE')

                            <button class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                        @endif

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        No products found.
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="p-4 border-t">
        {{ $products->links() }}
    </div>

</div>

@endsection