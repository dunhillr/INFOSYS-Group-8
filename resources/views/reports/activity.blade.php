@extends('layouts.app')
@section('title', 'Activity Report')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Activity Report</h3></div></div>
<div class="box"><div class="box-body overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>IP Address</th><th>Date</th></tr></thead><tbody>
@forelse($logs as $log)
<tr><td>{{ $log->user->name ?? 'System' }}</td><td>{{ ucfirst($log->action) }}</td><td>{{ ucfirst($log->module) }}</td><td>{{ $log->description ?? '-' }}</td><td>{{ $log->ip_address ?? '-' }}</td><td>{{ $log->created_at?->format('M d, Y h:i A') }}</td></tr>
@empty <tr><td colspan="6" class="text-center">No records found.</td></tr>
@endforelse
</tbody></table>{{ $logs->links() }}</div></div>
@endsection