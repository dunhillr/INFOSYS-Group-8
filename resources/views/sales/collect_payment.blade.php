@extends('layouts.app')
@section('title', 'Collect Balance')

@section('content')
<div class="flex justify-center items-center mt-10">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 w-full max-w-md overflow-hidden">
        
        <!-- Header -->
        <div class="bg-blue-600 text-white p-5">
            <div class="flex items-center gap-3">
                <span class="text-2xl">💳</span>
                <div>
                    <h3 class="text-xl font-bold uppercase tracking-tight">Collect Balance</h3>
                    <p class="text-blue-100 text-sm font-medium">{{ $sale->sale_number }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('sales.update', $sale) }}" method="POST" class="p-6">
            @csrf
            @method('PATCH')
            
            <!-- Hidden flag to tell the controller to use updatePayment logic -->
            <input type="hidden" name="is_collect_payment" value="1">

            <!-- Summary Section -->
            <div class="space-y-4 mb-6">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Customer</label>
                    <div class="text-gray-800 font-semibold text-lg">{{ $sale->customer->customer_name ?? 'Walk-in Customer' }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4 py-4 border-y border-dashed border-gray-200">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Total Order</label>
                        <div class="text-gray-500 font-medium">₱{{ number_format($sale->total_amount, 2) }}</div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block text-right">Already Paid</label>
                        <div class="text-gray-500 font-medium text-right">₱{{ number_format($sale->amount_paid, 2) }}</div>
                    </div>
                </div>

                <div class="bg-red-50 p-4 rounded-lg flex justify-between items-center">
                    <span class="text-red-700 font-bold text-sm uppercase">Remaining Balance</span>
                    <span class="text-red-700 font-black text-xl">₱{{ number_format($sale->balance_due, 2) }}</span>
                </div>
            </div>

            <!-- Editable Fields -->
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-blue-600 uppercase mb-1 block">Enter Payment Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 font-bold">₱</span>
                        <input type="number" 
                               name="new_payment_amount" 
                               step="0.01" 
                               min="0.01" 
                               max="{{ $sale->balance_due }}"
                               value="{{ $sale->balance_due }}"
                               class="form-control pl-8 text-lg font-bold border-blue-200 focus:border-blue-500 focus:ring-blue-200" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-blue-600 uppercase mb-1 block">Payment Method</label>
                    <select name="payment_method" class="form-control border-blue-200 focus:border-blue-500" required>
                        <option value="Cash" @selected($sale->payment_method === 'Cash')>Cash</option>
                        <option value="GCash" @selected($sale->payment_method === 'GCash')>GCash</option>
                        <option value="Bank Transfer" @selected($sale->payment_method === 'Bank Transfer')>Bank Transfer</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col gap-3">
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-blue-700 active:scale-[0.98] transition-all">
                    Update Payment
                </button>
                <a href="{{ route('sales.index') }}" class="text-center text-gray-500 text-sm font-semibold py-2 hover:text-gray-700 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
