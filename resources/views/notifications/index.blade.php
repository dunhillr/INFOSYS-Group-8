@extends('layouts.app')
@section('title', 'Notifications')

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
                Notifications
            </h3>
            <p class="text-sm text-gray-500">
                Manage system notifications
            </p>
        </div>

    </div>

    <!-- RIGHT: MARK ALL AS READ -->
    <form action="{{ route('notifications.readAll') }}" method="POST">
        @csrf
        <button class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
            Mark All as Read
        </button>
    </form>

</div>

<!-- NOTIFICATIONS CARD -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

    <!-- HEADER -->
    <div class="bg-blue-600 text-white px-5 py-3 font-semibold">
        Notification List
    </div>

    <!-- BODY -->
    <div class="p-5 space-y-4">

        @forelse ($notifications as $notification)

        <div class="border rounded-lg p-4 flex justify-between items-start 
                    {{ $notification->is_read ? 'bg-gray-50' : 'bg-white border-l-4 border-blue-500' }}">

            <!-- LEFT CONTENT -->
            <div>

                <h6 class="font-semibold text-gray-800 flex items-center gap-2">

                    {{ $notification->title }}

                    @if(!$notification->is_read)
                        <span class="px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">
                            New
                        </span>
                    @endif

                </h6>

                <p class="text-gray-500 text-sm mt-1">
                    {{ $notification->message }}
                </p>

                <small class="text-gray-400 text-xs">
                    {{ ucfirst(str_replace('_', ' ', $notification->type)) }} • 
                    {{ $notification->created_at?->diffForHumans() }}
                </small>

            </div>

            <!-- ACTION -->
            <div>

                @if(!$notification->is_read)
                <form action="{{ route('notifications.read', $notification) }}" method="POST">
                    @csrf
                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition">
                        Mark as Read
                    </button>
                </form>
                @endif

            </div>

        </div>

        @empty

        <p class="text-center text-gray-400 py-6">
            No notifications found.
        </p>

        @endforelse

    </div>

    <!-- PAGINATION -->
    <div class="p-4 border-t">
        {{ $notifications->links() }}
    </div>

</div>

@endsection