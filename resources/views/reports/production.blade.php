@extends('layouts.app')
@section('title', 'Production Report')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Production Report</h3></div></div>
<div class="box"><div class="box-body overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Date</th><th>Batch Reference</th><th>Quantity Produced</th><th>Remarks</th><th>Recorded By</th><th>Created At</th></tr></thead><tbody>
@forelse($productions as $production)
<tr><td>{{ $production->production_date?->format('M d, Y') }}</td><td>{{ $production->batch_reference ?? '-' }}</td><td>{{ number_format($production->quantity_produced, 2) }}</td><td>{{ $production->remarks ?? '-' }}</td><td>{{ $production->user->name ?? '-' }}</td><td>{{ $production->created_at?->format('M d, Y h:i A') }}</td></tr>
@empty <tr><td colspan="6" class="text-center">No records found.</td></tr>
@endforelse
</tbody></table>{{ $productions->links() }}</div></div>
@endsection