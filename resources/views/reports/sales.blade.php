@extends('layouts.app')
@section('title', 'Sales Report')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Sales Report</h3></div></div>
<div class="box"><div class="box-body overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Sale No.</th><th>Products</th><th>Customer</th><th>Quantity</th><th>Unit Price</th><th>Total</th><th>Payment</th><th>Date</th><th>Recorded By</th></tr></thead><tbody>
@forelse($sales as $sale)
<tr>
    <td>{{ $sale->sale_number }}</td>
    <td>
        @foreach($sale->saleItems as $item)
            <div class="text-[10px] leading-tight">{{ $item->product->product_name ?? '-' }}</div>
        @endforeach
    </td>
    <td>{{ $sale->customer->customer_name ?? 'Walk-in' }}</td>
    <td>
        @foreach($sale->saleItems as $item)
            <div class="text-[10px] leading-tight">{{ number_format($item->quantity, 2) }}</div>
        @endforeach
    </td>
    <td>
        @foreach($sale->saleItems as $item)
            <div class="text-[10px] leading-tight">₱{{ number_format($item->unit_price, 2) }}</div>
        @endforeach
    </td>
    <td>₱{{ number_format($sale->total_amount, 2) }}</td>
    <td>{{ ucfirst($sale->payment_status) }}</td>
    <td>{{ $sale->sale_date?->format('M d, Y h:i A') }}</td>
    <td>{{ $sale->user->name ?? '-' }}</td>
</tr>
@empty <tr><td colspan="9" class="text-center">No records found.</td></tr>
@endforelse
</tbody></table>{{ $sales->links() }}</div></div>
@endsection