@extends('layouts.app')
@section('title', 'Deliveries Control Center')

@section('content')

<!-- PAGE HEADER -->
<div class="flex justify-between items-center mt-4 mb-6">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Deliveries Control Center</h3>
            <p class="text-sm text-gray-500">Monitor and manage your fleet logistics in real-time</p>
        </div>
    </div>
</div>

<div class="box mb-4">
    <div class="box-body p-4 bg-gray-50 border-b border-gray-100 rounded-t-lg">
        <form action="{{ route('deliveries.index') }}" method="GET" class="grid grid-cols-12 gap-4">
            <div class="xl:col-span-4 col-span-12">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Sale No., Customer, or Destination...">
            </div>
            <div class="xl:col-span-3 col-span-12">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="xl:col-span-3 col-span-12">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="xl:col-span-2 col-span-12 flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex-1">
                    Filter
                </button>
                <a href="{{ route('deliveries.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition" title="Reset Filters">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="box">
    <div class="box-body">
        <div class="overflow-auto">
            <table class="table min-w-full whitespace-nowrap table-bordered">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Sale No.</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Customer</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Vehicle</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Destination</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase text-center">Status</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">Latest Update</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($deliveries as $delivery)
                    <tr class="hover:bg-gray-50 transition {{ !$delivery->is_opened ? 'bg-blue-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-800">{{ $delivery->sale->sale_number ?? 'DET-'.$delivery->id }}</span>
                            @if(!$delivery->is_opened)
                                <span class="badge bg-blue-600 text-white text-[10px] px-1.5 ms-1">NEW</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $delivery->customer->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-800">{{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $delivery->vehicle->plate_number ?? '' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-[200px] truncate" title="{{ $delivery->destination }}">
                            {{ $delivery->destination }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusMap = [
                                    'pending' => ['bg-yellow-100 text-yellow-800', 'PENDING'],
                                    'out_for_delivery' => ['bg-blue-100 text-blue-800', 'IN TRANSIT'],
                                    'delivered' => ['bg-green-100 text-green-800', 'DELIVERED'],
                                    'cancelled' => ['bg-red-100 text-red-800', 'CANCELLED'],
                                ];
                                [$color, $label] = $statusMap[$delivery->status] ?? ['bg-gray-100 text-gray-800', strtoupper($delivery->status)];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider {{ $color }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php $latestLog = $delivery->logs->first(); @endphp
                            @if($latestLog)
                                <div class="text-xs truncate max-w-[200px]" title="{{ $latestLog->notes }}">
                                    {{ $latestLog->notes ?: 'Status changed to '.str_replace('_', ' ', $latestLog->status) }}
                                </div>
                                <div class="text-[9px] text-gray-400">{{ $latestLog->created_at->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-400 text-xs italic">No updates</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center items-center gap-2">
                                {{-- ── STATUS-BASED ACTIONS ── --}}
                                @if($delivery->status === 'pending')
                                    <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="out_for_delivery">
                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:bg-blue-700 transition">
                                            🚚 Dispatch
                                        </button>
                                    </form>
                                @elseif($delivery->status === 'out_for_delivery')
                                    <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST" class="inline-block">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:bg-green-700 transition">
                                            ✅ Complete
                                        </button>
                                    </form>
                                    <form action="{{ route('deliveries.updateStatus', $delivery) }}" method="POST" class="inline-block">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                                            ❌ Cancel
                                        </button>
                                    </form>
                                @endif

                                {{-- ── ALWAYS SHOW TRACK ACTION ── --}}
                                <a href="{{ route('deliveries.edit', $delivery) }}" class="text-blue-500 hover:text-blue-700 transition p-1 bg-blue-50 rounded-lg flex items-center gap-1 px-3 py-1.5 text-xs font-bold" title="Track & Update">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Track
                                </a>

                                {{-- Owner Delete Action (Optional, kept subtle) --}}
                                @if(Auth::user()->user_type === 'owner')
                                <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Delete this record?')" class="ms-1">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-300 hover:text-red-500 transition p-1" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400 italic">No delivery records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $deliveries->links() }}
        </div>
    </div>
</div>

@endsection