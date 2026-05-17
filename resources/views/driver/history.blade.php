@extends('driver.layout')
@section('title', 'History')

@section('content')
<div class="dp-container">

    {{-- ── BACK & HEADER ── --}}
    <div style="padding: 16px 0; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('driver.index') }}" class="dp-btn dp-btn-back" style="width: auto; display: inline-flex; padding: 10px 16px; font-size: 13px; border-radius: 10px; text-decoration: none;">
            ←  Back to Deliveries
        </a>
        <h1 style="font-size: 20px; font-weight: 800; color: #111827; margin: 0;">📋 History</h1>
    </div>

    {{-- ── DELIVERY CARDS ── --}}
    @forelse($deliveries as $delivery)
        @php
            $isDelivered = $delivery->status === 'delivered';
            $statusLabel = $isDelivered ? 'Delivered' : 'Cancelled';
            $statusClass = $isDelivered ? 'badge-delivered' : 'badge-cancelled';
            $statusIcon  = $isDelivered ? '✅' : '❌';
        @endphp

        <div class="dp-card" style="margin-bottom: 12px; opacity: {{ $isDelivered ? '1' : '0.8' }};">
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
                    <div class="dp-row-label">📅 Date of Delivery</div>
                    <div class="dp-row-value">{{ $delivery->updated_at->timezone('Asia/Manila')->format('M d, Y · h:i A') }}</div>
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

            {{-- CTA to View Details --}}
            <div style="padding: 14px 16px; border-top: 1px solid #f3f4f6;">
                <a href="{{ route('driver.show', $delivery) }}"
                   style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #f3f4f6; color: #374151; border-radius: 14px; padding: 12px; font-size: 14px; font-weight: 700; text-decoration: none; transition: background .15s;">
                    🔍 View Details
                </a>
            </div>
        </div>
    @empty
        <div class="dp-card" style="padding: 40px 20px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 12px;">📭</div>
            <div style="font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 6px;">No History</div>
            <div style="font-size: 13px; color: #6b7280;">You don't have any completed delivery records yet.</div>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($deliveries->hasPages())
        <div style="margin-top: 20px; padding-bottom: 20px;">
            {{ $deliveries->links('pagination::tailwind') }}
        </div>
    @endif

</div>
@endsection
