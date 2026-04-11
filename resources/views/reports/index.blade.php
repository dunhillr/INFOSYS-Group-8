@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Reports</h3></div></div>
<div class="grid grid-cols-12 gap-6">
<div class="xl:col-span-4 col-span-12"><div class="box"><div class="box-body"><h5 class="mb-2">Sales Report</h5><p class="text-textmuted mb-3">View daily and monthly sales transactions.</p><a href="{{ route('reports.sales') }}" class="ti-btn ti-btn-primary-full">Open Report</a></div></div></div>
<div class="xl:col-span-4 col-span-12"><div class="box"><div class="box-body"><h5 class="mb-2">Production Report</h5><p class="text-textmuted mb-3">Review production outputs by date and batch.</p><a href="{{ route('reports.production') }}" class="ti-btn ti-btn-primary-full">Open Report</a></div></div></div>
<div class="xl:col-span-4 col-span-12"><div class="box"><div class="box-body"><h5 class="mb-2">Inventory Report</h5><p class="text-textmuted mb-3">Track current stock and movement logs.</p><a href="{{ route('reports.inventory') }}" class="ti-btn ti-btn-primary-full">Open Report</a></div></div></div>
<div class="xl:col-span-4 col-span-12"><div class="box"><div class="box-body"><h5 class="mb-2">Delivery Report</h5><p class="text-textmuted mb-3">Check delivery schedules and statuses.</p><a href="{{ route('reports.delivery') }}" class="ti-btn ti-btn-primary-full">Open Report</a></div></div></div>
@if (auth()->user()->user_type === 'owner')
<div class="xl:col-span-4 col-span-12"><div class="box"><div class="box-body"><h5 class="mb-2">Activity Report</h5><p class="text-textmuted mb-3">Owner-only user activity and logs report.</p><a href="{{ route('reports.activity') }}" class="ti-btn ti-btn-primary-full">Open Report</a></div></div></div>
@endif
</div>
@endsection