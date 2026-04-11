@extends('layouts.app')
@section('title', 'Inventory Report')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Inventory Report</h3></div></div>
<div class="box"><div class="box-body overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Reference Type</th><th>Reference ID</th><th>Movement</th><th>Quantity</th><th>Stock Before</th><th>Stock After</th><th>Remarks</th><th>Created By</th><th>Date</th></tr></thead><tbody>
@forelse($inventoryLogs as $log)
<tr><td>{{ ucwords(str_replace('_', ' ', $log->reference_type)) }}</td><td>{{ $log->reference_id }}</td><td>{{ strtoupper($log->movement_type) }}</td><td>{{ number_format($log->quantity, 2) }}</td><td>{{ number_format($log->stock_before, 2) }}</td><td>{{ number_format($log->stock_after, 2) }}</td><td>{{ $log->remarks ?? '-' }}</td><td>{{ $log->creator->name ?? '-' }}</td><td>{{ $log->created_at?->format('M d, Y h:i A') }}</td></tr>
@empty <tr><td colspan="9" class="text-center">No records found.</td></tr>
@endforelse
</tbody></table>{{ $inventoryLogs->links() }}</div></div>
@endsection
