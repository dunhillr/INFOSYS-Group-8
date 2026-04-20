@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<!-- PAGE HEADER -->
<div class="flex justify-between items-center mt-4 mb-6">

    <!-- LEFT: LOGO + TITLE -->
    <div class="flex items-center gap-3">

        <img src="{{ asset('images/logo.png') }}" 
             alt="Logo" 
             class="h-10 w-auto object-contain">

        <div>
            <h3 class="text-2xl font-bold text-gray-800">
                Dashboard
            </h3>
            <p class="text-sm text-gray-500">
                Overview of your system performance
            </p>
        </div>

    </div>

</div>

<!-- STATS CARDS -->
<div class="grid grid-cols-12 gap-6">

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-blue-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Current Stock</p>
            <h4 class="text-2xl font-bold text-blue-600">
                {{ number_format($inventory?->current_stock ?? 0, 2) }}
            </h4>
        </div>
    </div>

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-blue-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Today Production</p>
            <h4 class="text-2xl font-bold text-blue-600">
                {{ number_format($todayProduction, 2) }}
            </h4>
        </div>
    </div>

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-blue-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Today Sales</p>
            <h4 class="text-2xl font-bold text-blue-600">
                {{ number_format($todaySales, 2) }}
            </h4>
        </div>
    </div>

    <div class="xl:col-span-3 col-span-12">
        <div class="bg-white border-l-4 border-blue-500 rounded-xl shadow-sm p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Pending Deliveries</p>
            <h4 class="text-2xl font-bold text-blue-600">
                {{ $pendingDeliveries }}
            </h4>
        </div>
    </div>

</div>

<!-- CONTENT SECTION -->
<div class="grid grid-cols-12 gap-6 mt-8">

    <!-- NOTIFICATIONS -->
    <div class="xl:col-span-6 col-span-12">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <!-- HEADER -->
            <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
                Recent Notifications
            </div>

            <div class="p-5 space-y-4">

                @forelse($notifications as $notification)
                    <div class="border-b border-gray-100 pb-3 last:border-none">
                        <h6 class="font-semibold text-gray-800">
                            {{ $notification->title }}
                        </h6>
                        <p class="text-gray-500 text-sm">
                            {{ $notification->message }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No notifications found.</p>
                @endforelse

            </div>
        </div>
    </div>

    <!-- ACTIVITY LOGS -->
    <div class="xl:col-span-6 col-span-12">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <!-- HEADER -->
            <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
                Recent Activity Logs
            </div>

            <div class="p-5 space-y-4">

                @forelse($logs as $log)
                    <div class="border-b border-gray-100 pb-3 last:border-none">
                        <h6 class="font-semibold text-gray-800">
                            {{ $log->user?->name ?? 'System' }}
                        </h6>
                        <p class="text-gray-500 text-sm">
                            {{ $log->action }} {{ $log->module }} - {{ $log->description }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No activity logs found.</p>
                @endforelse

            </div>

        </div>
    </div>

</div>

@endsection