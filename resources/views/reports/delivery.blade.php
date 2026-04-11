@extends('layouts.app')
@section('title', 'Delivery Report')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Delivery Report</h3></div></div>
<div class="box"><div class="box-body overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Sale No.</th><th>Customer</th><th>Vehicle</th><th>Destination</th><th>Date</th><th>Time</th><th>Status</th></tr></thead><tbody>
@forelse($deliveries as $delivery)
<tr><td>{{ $delivery->sale->sale_number ?? '-' }}</td><td>{{ $delivery->customer->customer_name ?? '-' }}</td><td>{{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }}</td><td>{{ $delivery->destination }}</td><td>{{ $delivery->delivery_date?->format('M d, Y') }}</td><td>{{ \Carbon\Carbon::parse($delivery->delivery_time)->format('h:i A') }}</td><td>{{ ucwords(str_replace('_', ' ', $delivery->status)) }}</td></tr>
@empty <tr><td colspan="7" class="text-center">No records found.</td></tr>
@endforelse
</tbody></table>{{ $deliveries->links() }}</div></div>
@endsection