@extends('layouts.app')
@section('title', 'Deliveries')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Deliveries</h3></div><div></div></div>
<div class="box"><div class="box-body"><div class="overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Sale No.</th><th>Customer</th><th>Vehicle</th><th>Destination</th><th>Date</th><th>Time</th><th>Status</th><th>Assigned By</th><th width="150">Actions</th></tr></thead><tbody>
@forelse ($deliveries as $delivery)
<tr @if(!$delivery->is_opened) class="bg-primary/5" @endif>
    <td>
        {{ $delivery->sale->sale_number ?? '-' }}
        @if(!$delivery->is_opened)
            <span class="badge bg-primary text-white ms-2">NEW</span>
        @endif
    </td>
    <td>{{ $delivery->customer->customer_name ?? '-' }}</td>
    <td>{{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }}</td>
    <td>{{ $delivery->destination }}</td>
    <td>{{ $delivery->delivery_date?->format('M d, Y') }}</td>
    <td>{{ \Carbon\Carbon::parse($delivery->delivery_time)->format('h:i A') }}</td>
    <td>{{ ucwords(str_replace('_', ' ', $delivery->status)) }}</td>
    <td>{{ $delivery->assigner->name ?? '-' }}</td>
    <td>
        <a href="{{ route('deliveries.edit', $delivery) }}" class="ti-btn ti-btn-info-full ti-btn-sm">Edit</a>
        <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this delivery?')">
            @csrf @method('DELETE')
            <button class="ti-btn ti-btn-danger-full ti-btn-sm">Delete</button>
        </form>
    </td>
</tr>
@empty <tr><td colspan="9" class="text-center">No delivery records found.</td></tr>
@endforelse
</tbody></table></div>{{ $deliveries->links() }}</div></div>
@endsection