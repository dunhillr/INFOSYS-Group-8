@extends('driver.layout')
@section('title', 'My Deliveries')

@section('content')
<div class="dp-container">

    {{-- ── GREETING HEADER ── --}}
    <div style="padding: 20px 0 12px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 13px; color: #6b7280; font-weight: 500;">
                {{ now()->timezone('Asia/Manila')->format('l, F j, Y') }}
            </div>
            <h1 style="font-size: 22px; font-weight: 800; color: #111827; margin-top: 2px;">
                Good {{ now()->timezone('Asia/Manila')->hour < 12 ? 'Morning' : (now()->timezone('Asia/Manila')->hour < 18 ? 'Afternoon' : 'Evening') }},
                {{ Str::words(Auth::user()->name, 1, '') }}! 👋
            </h1>
        </div>
        <a href="{{ route('driver.history') }}" style="background: #e5e7eb; color: #374151; padding: 10px 14px; border-radius: 12px; font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px;">
            📋 History
        </a>
    </div>

    {{-- ── ASSIGNED VEHICLE(S) INFO ── --}}
    @if($assignedVehicles->isNotEmpty())
        <div class="dp-card" style="margin-bottom: 20px; border-left: 4px solid #1d4ed8; background: #eff6ff;">
            <div style="padding: 16px;">
                <div style="font-size: 11px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;">
                    🚛 Assigned Vehicle
                </div>
                @foreach($assignedVehicles as $v)
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: {{ $loop->last ? '0' : '8px' }};">
                        <div>
                            <div style="font-size: 16px; font-weight: 800; color: #1e3a8a;">{{ $v->vehicle_name }}</div>
                            <div style="font-size: 13px; color: #3b82f6; font-family: monospace; font-weight: 600;">{{ $v->plate_number }}</div>
                        </div>
                        <div style="font-size: 24px;">🔑</div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="dp-card" style="margin-bottom: 20px; border-left: 4px solid #ef4444; background: #fef2f2;">
            <div style="padding: 16px;">
                <div style="font-size: 13px; font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 8px;">
                    ⚠️ You don't have an assigned vehicle today.
                </div>
                <div style="font-size: 12px; color: #b91c1c; margin-top: 4px;">
                    Contact the staff to assign you to a truck.
                </div>
            </div>
        </div>
    @endif

    {{-- ── STATS ── --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
        <div class="dp-card" style="padding: 14px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #1d4ed8;">{{ $deliveries->count() }}</div>
            <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Deliveries</div>
        </div>
        <div class="dp-card" style="padding: 14px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #16a34a;">{{ $completedToday }}</div>
            <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Completed Today</div>
        </div>
    </div>

    {{-- ── GLOBAL START TRIP BUTTON ── --}}
    @if($deliveries->contains('status', 'pending'))
    <form action="{{ route('driver.startTrip') }}" method="POST" style="margin-bottom: 20px;">
        @csrf
        <button type="submit" onclick="return confirm('Start the whole trip? All pending deliveries will become In Transit.')" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #16a34a; color: #fff; border-radius: 14px; padding: 16px; font-size: 18px; font-weight: 800; text-decoration: none; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            🚚 Start Delivery 
        </button>
    </form>
    @endif

    {{-- ── DELIVERY CARDS ── --}}
    @forelse($deliveries as $delivery)
        @php
            $isPending   = $delivery->status === 'pending';
            $isInTransit = $delivery->status === 'out_for_delivery';
            $statusLabel = $isPending ? 'Pending' : 'In Transit';
            $statusClass = $isPending ? 'badge-pending' : 'badge-in_transit';
            $statusIcon  = $isPending ? '🕐' : '🚚';
        @endphp

        <div class="dp-card" style="margin-bottom: 12px;">
            {{-- Card Header --}}
            <div style="padding: 14px 16px 10px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 12px; color: #6b7280; font-weight: 500;">Sale No.</div>
                    <div style="font-size: 17px; font-weight: 800; color: #111827;">
                        {{ $delivery->sale->sale_number ?? 'DEL-'.$delivery->id }}
                    </div>
                </div>
                <span class="dp-badge {{ $statusClass }}">{{ $statusIcon }} {{ $statusLabel }}</span>
            </div>

            {{-- Details --}}
            <div style="border-top: 1px solid #f3f4f6; padding-top: 10px;">
                @php
                    $totalWeight = $delivery->sale?->saleItems->sum(function($item) {
                        return $item->quantity * ($item->product->weight_kg ?? 0);
                    }) ?? 0;
                @endphp
                <div style="padding: 0 16px 10px;">
                    <div style="font-size: 16px; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 6px;">
                        👤 {{ $delivery->customer->customer_name ?? 'Walk-in / None' }}
                    </div>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; display: flex; flex-wrap: wrap; gap: 12px;">
                        <span><strong style="color:#374151;">Weight:</strong> {{ number_format($totalWeight, 2) }} kg</span>
                        <span>
                            🚚 <strong style="color:#374151;">Vehicle:</strong> {{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }} 
                            @if($delivery->vehicle?->plate_number)
                                ({{ $delivery->vehicle->plate_number }})
                            @endif
                        </span>
                    </div>
                </div>

                <div class="dp-row">
                    <div class="dp-row-label">📍 Destination</div>
                    <div class="dp-row-value">{{ $delivery->destination }}</div>
                </div>

                <div class="dp-row">
                    <div class="dp-row-label">📅 Date and Time</div>
                    <div class="dp-row-value">{{ $delivery->delivery_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($delivery->delivery_time)->format('h:i A') }}</div>
                </div>
            </div>

            {{-- Items summary --}}
            @if($delivery->sale?->saleItems->isNotEmpty())
            <div style="padding: 10px 16px; background: #f8fafc; border-top: 1px solid #f3f4f6;">
                <div style="font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;">Items</div>
                @foreach($delivery->sale->saleItems as $item)
                    <div style="font-size: 13px; color: #374151; display: flex; justify-content: space-between;">
                        <span>{{ $item->product->product_name ?? '—' }}</span>
                        <span style="font-weight: 700;">× {{ $item->quantity }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- CTA --}}
            @if(!$isPending)
            <div style="padding: 14px 16px;">
                <a href="{{ route('driver.show', $delivery) }}"
                   style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #1d4ed8; color: #fff; border-radius: 14px; padding: 14px; font-size: 15px; font-weight: 700; text-decoration: none; transition: filter .15s;">
                    📸 Mark as Delivered
                </a>
            </div>
            @endif
        </div>
    @empty
        <div class="dp-card" style="padding: 40px 20px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 12px;">🎉</div>
            <div style="font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 6px;">Wala nang Deliveries!</div>
            <div style="font-size: 13px; color: #6b7280;">Lahat ng deliveries para sa araw na ito ay natapos na o wala pang na-assign sa iyo.</div>
        </div>
    @endforelse

</div>
@endsection
