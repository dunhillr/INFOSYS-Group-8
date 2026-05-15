@extends('driver.layout')
@section('title', 'My Deliveries')

@section('content')
<div class="dp-container">

    {{-- ── GREETING HEADER ── --}}
    <div style="padding: 20px 0 12px;">
        <div style="font-size: 13px; color: #6b7280; font-weight: 500;">
            {{ now()->timezone('Asia/Manila')->format('l, F j, Y') }}
        </div>
        <h1 style="font-size: 22px; font-weight: 800; color: #111827; margin-top: 2px;">
            Magandang {{ now()->timezone('Asia/Manila')->hour < 12 ? 'Umaga' : (now()->timezone('Asia/Manila')->hour < 18 ? 'Tanghali' : 'Gabi') }},
            {{ Str::words(Auth::user()->name, 1, '') }}! 👋
        </h1>
    </div>

    {{-- ── STATS ── --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
        <div class="dp-card" style="padding: 14px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #1d4ed8;">{{ $deliveries->count() }}</div>
            <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Dapat Dalhin</div>
        </div>
        <div class="dp-card" style="padding: 14px; text-align: center;">
            <div style="font-size: 28px; font-weight: 800; color: #16a34a;">{{ $completedToday }}</div>
            <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Natapos Ngayon</div>
        </div>
    </div>

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
            <div style="border-top: 1px solid #f3f4f6;">
                <div class="dp-row">
                    <div class="dp-row-label">👤 Customer</div>
                    <div class="dp-row-value">{{ $delivery->customer->customer_name ?? '—' }}</div>
                </div>
                <div class="dp-row">
                    <div class="dp-row-label">📍 Destination</div>
                    <div class="dp-row-value">{{ $delivery->destination }}</div>
                </div>
                <div class="dp-row">
                    <div class="dp-row-label">🚛 Sasakyan</div>
                    <div class="dp-row-value">
                        {{ $delivery->vehicle->vehicle_name ?? 'Unassigned' }}
                        @if($delivery->vehicle?->plate_number)
                            <span style="color: #6b7280; font-weight: 400; font-size: 11px;"> · {{ $delivery->vehicle->plate_number }}</span>
                        @endif
                    </div>
                </div>
                <div class="dp-row">
                    <div class="dp-row-label">📅 Petsa</div>
                    <div class="dp-row-value">{{ $delivery->delivery_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($delivery->delivery_time)->format('h:i A') }}</div>
                </div>
            </div>

            {{-- Items summary --}}
            @if($delivery->sale?->saleItems->isNotEmpty())
            <div style="padding: 10px 16px; background: #f8fafc; border-top: 1px solid #f3f4f6;">
                <div style="font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;">Mga Item</div>
                @foreach($delivery->sale->saleItems as $item)
                    <div style="font-size: 13px; color: #374151; display: flex; justify-content: space-between;">
                        <span>{{ $item->product->product_name ?? '—' }}</span>
                        <span style="font-weight: 700;">× {{ $item->quantity }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- CTA --}}
            <div style="padding: 14px 16px;">
                <a href="{{ route('driver.show', $delivery) }}"
                   style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #1d4ed8; color: #fff; border-radius: 14px; padding: 14px; font-size: 15px; font-weight: 700; text-decoration: none; transition: filter .15s;">
                    @if($isPending)
                        🚀 I-start ang Biyahe
                    @else
                        📸 I-confirm ang Delivery
                    @endif
                </a>
            </div>
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
