@extends('layouts.app')
@section('title', 'Reports')

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
                Reports
            </h3>
            <p class="text-sm text-gray-500">
                Generate and view system reports
            </p>
        </div>

    </div>

</div>

<!-- REPORT CARDS -->
<div class="grid grid-cols-12 gap-6">

    <!-- SALES -->
    <div class="xl:col-span-4 col-span-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">

            <h5 class="text-lg font-semibold text-gray-800 mb-2">
                Sales Report
            </h5>

            <p class="text-sm text-gray-500 mb-4">
                View daily and monthly sales transactions.
            </p>

            <a href="{{ route('reports.sales') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition inline-block">
                Open Report
            </a>

        </div>
    </div>

    <!-- PRODUCTION -->
    <div class="xl:col-span-4 col-span-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">

            <h5 class="text-lg font-semibold text-gray-800 mb-2">
                Production Report
            </h5>

            <p class="text-sm text-gray-500 mb-4">
                Review production outputs by date and batch.
            </p>

            <a href="{{ route('reports.production') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition inline-block">
                Open Report
            </a>

        </div>
    </div>

    <!-- INVENTORY -->
    <div class="xl:col-span-4 col-span-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">

            <h5 class="text-lg font-semibold text-gray-800 mb-2">
                Inventory Report
            </h5>

            <p class="text-sm text-gray-500 mb-4">
                Track current stock and movement logs.
            </p>

            <a href="{{ route('reports.inventory') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition inline-block">
                Open Report
            </a>

        </div>
    </div>

    <!-- DELIVERY -->
    <div class="xl:col-span-4 col-span-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">

            <h5 class="text-lg font-semibold text-gray-800 mb-2">
                Delivery Report
            </h5>

            <p class="text-sm text-gray-500 mb-4">
                Check delivery schedules and statuses.
            </p>

            <a href="{{ route('reports.delivery') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition inline-block">
                Open Report
            </a>

        </div>
    </div>

    <!-- ACTIVITY (OWNER ONLY) -->
    @if (auth()->user()->user_type === 'owner')
    <div class="xl:col-span-4 col-span-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">

            <h5 class="text-lg font-semibold text-gray-800 mb-2">
                Activity Report
            </h5>

            <p class="text-sm text-gray-500 mb-4">
                Owner-only user activity and logs report.
            </p>

            <a href="{{ route('reports.activity') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition inline-block">
                Open Report
            </a>

        </div>
    </div>
    @endif

</div>

@endsection