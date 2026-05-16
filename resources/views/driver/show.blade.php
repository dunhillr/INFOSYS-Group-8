@extends('driver.layout')
@section('title', 'Delivery Detail')

@section('content')
<div class="dp-container">

    {{-- ── BACK ── --}}
    <div style="padding: 16px 0 8px;">
        <a href="{{ route('driver.index') }}" class="dp-btn dp-btn-back" style="width: auto; display: inline-flex; padding: 10px 16px; font-size: 13px; border-radius: 10px; text-decoration: none;">
            ← Bumalik sa Listahan
        </a>
    </div>

    @php
        $isPending   = $delivery->status === 'pending';
        $isInTransit = $delivery->status === 'out_for_delivery';
        $isDelivered = $delivery->status === 'delivered';

        $statusMap = [
            'pending'          => ['badge-pending',    '🕐 Pending'],
            'out_for_delivery' => ['badge-in_transit',  '🚚 In Transit'],
            'delivered'        => ['badge-delivered',   '✅ Delivered'],
            'cancelled'        => ['badge-cancelled',   '❌ Cancelled'],
        ];
        [$statusClass, $statusLabel] = $statusMap[$delivery->status] ?? ['', $delivery->status];
    @endphp

    {{-- ── DELIVERY INFO CARD ── --}}
    <div class="dp-card" style="margin-bottom: 12px;">
        <div style="padding: 16px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing:.5px;">Sale Number</div>
                <div style="font-size: 20px; font-weight: 800; color: #111827;">{{ $delivery->sale->sale_number ?? 'DEL-'.$delivery->id }}</div>
            </div>
            <span class="dp-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>

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
                    <br><span style="color:#6b7280; font-size:11px; font-weight:400;">{{ $delivery->vehicle->plate_number }}</span>
                @endif
            </div>
        </div>
        <div class="dp-row">
            <div class="dp-row-label">📅 Petsa</div>
            <div class="dp-row-value">{{ $delivery->delivery_date->format('F j, Y') }}</div>
        </div>
        <div class="dp-row">
            <div class="dp-row-label">🕐 Oras</div>
            <div class="dp-row-value">{{ \Carbon\Carbon::parse($delivery->delivery_time)->format('h:i A') }}</div>
        </div>
    </div>

    {{-- ── ITEMS CARD ── --}}
    @if($delivery->sale?->saleItems->isNotEmpty())
    <div class="dp-card" style="margin-bottom: 12px;">
        <div style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;">
            <div style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing:.5px;">📦 Mga Ire-deliver</div>
        </div>
        @foreach($delivery->sale->saleItems as $item)
        <div class="dp-row">
            <div class="dp-row-value">{{ $item->product->product_name ?? '—' }}</div>
            <div style="font-size: 15px; font-weight: 800; color: #1d4ed8;">× {{ $item->quantity }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── ACTION AREA ── --}}
    {{-- ── ACTION AREA ── --}}
    @if($isInTransit)
    {{-- CONFIRM DELIVERY --}}
    <div class="dp-card" style="padding: 20px; margin-bottom: 12px;">
        <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 6px;">📸 Proof of Delivery</div>
        <div style="font-size: 12px; color: #6b7280; margin-bottom: 16px;">
            Kumuha ng litrato ng <strong>pinirmahang resibo</strong> o ng <strong>na-deliver na yelo</strong> bago i-confirm. Required ito.
        </div>

        <form method="POST" action="{{ route('driver.confirm', $delivery) }}" enctype="multipart/form-data" id="confirmForm">
            @csrf

            {{-- Photo Upload --}}
            <label for="proof_photo" style="display: block; margin-bottom: 12px;">
                <div id="upload-area" style="border: 2px dashed #d1d5db; border-radius: 14px; padding: 24px 16px; text-align: center; cursor: pointer; transition: border-color .2s; background: #f9fafb;">
                    <div style="font-size: 36px; margin-bottom: 8px;" id="upload-icon">📷</div>
                    <div style="font-size: 14px; font-weight: 700; color: #374151;" id="upload-title">I-tap para kumuha ng litrato</div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;" id="upload-sub">o pumili ng file mula sa gallery</div>
                </div>
                <input type="file" name="proof_of_delivery" id="proof_photo"
                       accept="image/*" capture="environment"
                       style="display:none" required>
            </label>

            {{-- Preview --}}
            <div id="preview-wrap" style="display:none; margin-bottom: 14px; border-radius: 14px; overflow: hidden; border: 2px solid #16a34a;">
                <img id="preview-img" src="" alt="Preview" style="width:100%; display:block; max-height: 280px; object-fit: cover;">
                <div style="padding: 8px 12px; background: #f0fdf4; font-size: 12px; color: #16a34a; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                    ✅ <span id="preview-name">Larawan napili</span>
                </div>
            </div>

            @error('proof_of_delivery')
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 10px 14px; color: #dc2626; font-size: 12px; font-weight: 600; margin-bottom: 12px;">
                    ⚠️ {{ $message }}
                </div>
            @enderror

            <button type="submit" class="dp-btn dp-btn-confirm" id="submitBtn" disabled
                style="opacity: .4; cursor: not-allowed;"
                onclick="return confirm('I-confirm na ang delivery? Tiyakin na tama ang lahat bago i-submit.')">
                ✅ I-confirm ang Delivery
            </button>
        </form>
    </div>

    @elseif($isDelivered)
    {{-- ALREADY DELIVERED --}}
    <div class="dp-card" style="padding: 20px; text-align: center; margin-bottom: 12px;">
        <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
        <div style="font-size: 16px; font-weight: 800; color: #16a34a;">Nai-deliver na!</div>
        <div style="font-size: 13px; color: #6b7280; margin-top: 6px;">Ang delivery na ito ay natapos na. Salamat sa mahusay na trabaho!</div>

        @if($delivery->proof_of_delivery)
        <div style="margin-top: 14px; border-radius: 12px; overflow: hidden; border: 2px solid #bbf7d0;">
            <img src="{{ asset('storage/'.$delivery->proof_of_delivery) }}" alt="Proof of Delivery"
                 style="width: 100%; max-height: 220px; object-fit: cover; display: block;">
            <div style="padding: 8px 12px; background: #f0fdf4; font-size: 11px; color: #16a34a; font-weight: 600;">
                📸 Proof of Delivery
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ── TRACKING LOG ── --}}
    @if($delivery->logs->isNotEmpty())
    <div class="dp-card" style="margin-bottom: 12px;">
        <div style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;">
            <div style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing:.5px;">📋 History</div>
        </div>
        @foreach($delivery->logs as $log)
            @php
                [$cls, $lbl] = $statusMap[$log->status] ?? ['', $log->status];
            @endphp
            <div style="padding: 12px 16px; border-bottom: 1px solid #f9fafb; display: flex; align-items: flex-start; gap: 10px;">
                <span class="dp-badge {{ $cls }}" style="flex-shrink:0; font-size:10px;">{{ $lbl }}</span>
                <div style="flex:1; min-width:0;">
                    @if($log->notes)
                        <div style="font-size: 12px; color: #374151;">{{ $log->notes }}</div>
                    @endif
                    <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">
                        {{ $log->created_at->timezone('Asia/Manila')->format('M d · h:i A') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

</div>

@push('scripts')
<script>
const fileInput   = document.getElementById('proof_photo');
const uploadArea  = document.getElementById('upload-area');
const previewWrap = document.getElementById('preview-wrap');
const previewImg  = document.getElementById('preview-img');
const previewName = document.getElementById('preview-name');
const submitBtn   = document.getElementById('submitBtn');
const uploadIcon  = document.getElementById('upload-icon');
const uploadTitle = document.getElementById('upload-title');

if (fileInput) {
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            previewName.textContent = file.name;
            previewWrap.style.display = 'block';

            uploadIcon.textContent  = '✅';
            uploadTitle.textContent = 'Larawan napili — i-tap para palitan';
            uploadArea.style.borderColor = '#16a34a';
            uploadArea.style.background  = '#f0fdf4';

            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor  = 'pointer';
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
@endsection
