@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Notifications</h3></div><div><form action="{{ route('notifications.readAll') }}" method="POST" class="inline-block">@csrf <button class="ti-btn ti-btn-success-full">Mark All as Read</button></form></div></div>
<div class="box"><div class="box-body">
@forelse ($notifications as $notification)
<div class="border rounded p-4 mb-3 {{ $notification->is_read ? 'bg-light' : '' }}">
    <div class="flex items-start justify-between">
        <div>
            <h6 class="font-semibold mb-1">{{ $notification->title }} @if (! $notification->is_read)<span class="badge bg-danger ms-2">New</span>@endif</h6>
            <p class="text-textmuted mb-1">{{ $notification->message }}</p>
            <small class="text-textmuted">{{ ucfirst(str_replace('_', ' ', $notification->type)) }} | {{ $notification->created_at?->diffForHumans() }}</small>
        </div>
        @if (! $notification->is_read)
        <form action="{{ route('notifications.read', $notification) }}" method="POST">@csrf <button class="ti-btn ti-btn-primary-full ti-btn-sm">Mark as Read</button></form>
        @endif
    </div>
</div>
@empty <p class="text-textmuted mb-0">No notifications found.</p>
@endforelse
{{ $notifications->links() }}
</div></div>
@endsection
