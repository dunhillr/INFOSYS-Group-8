@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Dashboard</h3></div></div>
<div class="grid grid-cols-12 gap-6">
<div class="xl:col-span-3 col-span-12"><div class="box"><div class="box-body"><p class="mb-2 text-textmuted">Current Stock</p><h4 class="font-semibold">{{ number_format($inventory?->current_stock ?? 0, 2) }}</h4></div></div></div>
<div class="xl:col-span-3 col-span-12"><div class="box"><div class="box-body"><p class="mb-2 text-textmuted">Today Production</p><h4 class="font-semibold">{{ number_format($todayProduction, 2) }}</h4></div></div></div>
<div class="xl:col-span-3 col-span-12"><div class="box"><div class="box-body"><p class="mb-2 text-textmuted">Today Sales</p><h4 class="font-semibold">{{ number_format($todaySales, 2) }}</h4></div></div></div>
<div class="xl:col-span-3 col-span-12"><div class="box"><div class="box-body"><p class="mb-2 text-textmuted">Pending Deliveries</p><h4 class="font-semibold">{{ $pendingDeliveries }}</h4></div></div></div>
</div>
<div class="grid grid-cols-12 gap-6 mt-6">
<div class="xl:col-span-6 col-span-12"><div class="box"><div class="box-header justify-between"><div class="box-title">Recent Notifications</div></div><div class="box-body">@forelse($notifications as $notification)<div class="mb-3 border-b pb-3"><h6 class="font-semibold mb-1">{{ $notification->title }}</h6><p class="text-textmuted mb-0">{{ $notification->message }}</p></div>@empty <p class="text-textmuted mb-0">No notifications found.</p>@endforelse</div></div></div>
<div class="xl:col-span-6 col-span-12"><div class="box"><div class="box-header justify-between"><div class="box-title">Recent Activity Logs</div></div><div class="box-body">@forelse($logs as $log)<div class="mb-3 border-b pb-3"><h6 class="font-semibold mb-1">{{ $log->user?->name ?? 'System' }}</h6><p class="text-textmuted mb-0">{{ $log->action }} {{ $log->module }} - {{ $log->description }}</p></div>@empty <p class="text-textmuted mb-0">No activity logs found.</p>@endforelse</div></div></div>
</div>
@endsection