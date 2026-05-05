@extends('layouts.app')
@section('title', 'Edit Delivery')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Edit Delivery</h3></div></div>
<div class="box"><div class="box-body"><form action="{{ route('deliveries.update', $delivery) }}" method="POST">@csrf @method('PUT') @include('deliveries._form')<div class="mt-4"><button class="ti-btn ti-btn-primary-full">Update</button><a href="{{ route('deliveries.index') }}" class="ti-btn ti-btn-light">Cancel</a></div></form></div></div>

<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Delivery Tracking History</h3></div></div>
<div class="box">
    <div class="box-body">
        <ul class="list-none space-y-4">
            @forelse ($delivery->logs as $log)
                <li class="relative pl-6 pb-4 border-l-2 border-primary/20 last:border-0 last:pb-0">
                    <span class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-primary flex items-center justify-center">
                        <span class="w-2 h-2 rounded-full bg-white"></span>
                    </span>
                    <div class="flex flex-col">
                        <span class="font-semibold text-primary uppercase text-xs">{{ str_replace('_', ' ', $log->status) }}</span>
                        <span class="text-xs text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                        @if ($log->notes)
                            <p class="text-sm mt-1 text-defaulttextcolor italic">"{{ $log->notes }}"</p>
                        @endif
                    </div>
                </li>
            @empty
                <li class="text-center text-muted">No tracking history recorded yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection