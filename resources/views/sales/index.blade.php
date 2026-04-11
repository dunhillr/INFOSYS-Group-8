@extends('layouts.app')
@section('title', 'Sales')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Sales</h3></div><div><a href="{{ route('sales.create') }}" class="ti-btn ti-btn-primary-full">Add Sale</a></div></div>
<div class="box"><div class="box-body"><div class="overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Sale No.</th><th>Product</th><th>Customer</th><th>Type</th><th>Quantity</th><th>Unit Price</th><th>Total</th><th>Payment</th><th>Date</th><th width="150">Actions</th></tr></thead><tbody>
@forelse ($sales as $sale)
<tr><td>{{ $sale->sale_number }}</td><td>{{ $sale->product->product_name ?? '-' }}</td><td>{{ $sale->customer->customer_name ?? 'Walk-in' }}</td><td>{{ ucfirst($sale->sale_type) }}</td><td>{{ number_format($sale->quantity, 2) }}</td><td>{{ number_format($sale->unit_price, 2) }}</td><td>{{ number_format($sale->total_amount, 2) }}</td><td>{{ ucfirst($sale->payment_status) }}</td><td>{{ $sale->sale_date?->format('M d, Y h:i A') }}</td><td><a href="{{ route('sales.edit', $sale) }}" class="ti-btn ti-btn-info-full ti-btn-sm">Edit</a><form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this sale record?')">@csrf @method('DELETE')<button class="ti-btn ti-btn-danger-full ti-btn-sm">Delete</button></form></td></tr>
@empty <tr><td colspan="10" class="text-center">No sales records found.</td></tr>
@endforelse
</tbody></table></div>{{ $sales->links() }}</div></div>
@endsection