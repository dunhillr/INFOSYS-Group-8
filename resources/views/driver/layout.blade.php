<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1d4ed8">
    <title>@yield('title', 'Driver Portal') — Bobong Ice Plant</title>
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/brand-logos/favicon.ico') }}">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:   #1d4ed8;
            --blue2:  #2563eb;
            --blue-l: #eff6ff;
            --green:  #16a34a;
            --green-l:#f0fdf4;
            --orange: #ea580c;
            --orange-l:#fff7ed;
            --red:    #dc2626;
            --red-l:  #fef2f2;
            --gray:   #6b7280;
            --gray-l: #f9fafb;
            --border: #e5e7eb;
            --text:   #111827;
            --radius: 16px;
        }

        html, body { height: 100%; background: #f1f5f9; font-family: 'Inter', sans-serif; color: var(--text); }

        /* ── TOP NAV ── */
        .dp-nav {
            position: sticky; top: 0; z-index: 100;
            background: var(--blue);
            padding: 14px 16px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 12px rgba(29,78,216,.35);
        }
        .dp-nav-brand { display: flex; align-items: center; gap: 10px; }
        .dp-nav-brand img { height: 30px; width: 30px; object-fit: contain; filter: brightness(0) invert(1); }
        .dp-nav-title { font-size: 15px; font-weight: 700; color: #fff; line-height: 1.1; }
        .dp-nav-sub   { font-size: 11px; color: rgba(255,255,255,.75); }
        .dp-nav-right { display: flex; align-items: center; gap: 8px; }
        .dp-nav-driver { background: rgba(255,255,255,.15); border-radius: 50px; padding: 4px 12px 4px 6px; display: flex; align-items: center; gap: 6px; }
        .dp-nav-avatar { width: 26px; height: 26px; border-radius: 50%; background: rgba(255,255,255,.3); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; }
        .dp-nav-name   { font-size: 12px; font-weight: 600; color: #fff; }

        /* ── FLASH ── */
        .dp-flash { margin: 12px 16px 0; border-radius: 12px; padding: 12px 16px; font-size: 13px; font-weight: 500; }
        .dp-flash-success { background: var(--green-l); color: var(--green); border: 1px solid #bbf7d0; }
        .dp-flash-error   { background: var(--red-l);   color: var(--red);   border: 1px solid #fecaca; }

        /* ── CONTAINER ── */
        .dp-container { padding: 16px; max-width: 540px; margin: 0 auto; }

        /* ── BOTTOM LOGOUT BAR ── */
        .dp-bottom-bar {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 100;
            background: #fff; border-top: 1px solid var(--border);
            padding: 10px 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .dp-logout-btn {
            display: flex; align-items: center; gap: 6px;
            background: none; border: 1px solid var(--border);
            border-radius: 50px; padding: 8px 20px;
            font-size: 13px; font-weight: 600; color: var(--gray);
            cursor: pointer; text-decoration: none;
        }

        /* Give page bottom padding so content doesn't hide behind bottom bar */
        .dp-page-body { padding-bottom: 80px; }

        /* ── CARD ── */
        .dp-card {
            background: #fff; border-radius: var(--radius);
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .dp-card + .dp-card { margin-top: 12px; }

        /* ── STATUS BADGE ── */
        .badge-pending     { background: #fef9c3; color: #854d0e; }
        .badge-in_transit  { background: #dbeafe; color: #1e40af; }
        .badge-delivered   { background: #dcfce7; color: #166534; }
        .badge-cancelled   { background: #fee2e2; color: #991b1b; }

        .dp-badge {
            display: inline-flex; align-items: center; gap: 4px;
            border-radius: 50px; padding: 4px 10px; font-size: 11px; font-weight: 700;
            letter-spacing: .4px; text-transform: uppercase;
        }

        /* ── ACTION BUTTONS ── */
        .dp-btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 16px; border-radius: 14px;
            font-size: 15px; font-weight: 700; cursor: pointer; border: none;
            transition: filter .15s, transform .1s;
        }
        .dp-btn:active { transform: scale(.97); filter: brightness(.93); }
        .dp-btn-start   { background: var(--blue);  color: #fff; }
        .dp-btn-confirm { background: var(--green); color: #fff; }
        .dp-btn-back    { background: var(--gray-l); color: var(--gray); border: 1px solid var(--border); }

        /* ── LABEL/VALUE ROWS ── */
        .dp-row { display: flex; padding: 10px 16px; border-bottom: 1px solid var(--border); }
        .dp-row:last-child { border-bottom: none; }
        .dp-row-label { width: 110px; font-size: 12px; color: var(--gray); font-weight: 500; flex-shrink: 0; padding-top: 1px; }
        .dp-row-value { font-size: 13px; font-weight: 600; color: var(--text); flex: 1; }
    </style>
</head>
<body>

{{-- TOP NAV --}}
<nav class="dp-nav">
    <div class="dp-nav-brand">
        <img src="{{ asset('images/logo.png') }}" alt="logo">
        <div>
            <div class="dp-nav-title">Driver Portal</div>
            <div class="dp-nav-sub">Bobong Ice Plant</div>
        </div>
    </div>
    <div class="dp-nav-right">
        <div class="dp-nav-driver">
            <div class="dp-nav-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <span class="dp-nav-name">{{ Str::words(Auth::user()->name, 1, '') }}</span>
        </div>
    </div>
</nav>

{{-- FLASH MESSAGES --}}
@if(session('success'))
    <div class="dp-flash dp-flash-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="dp-flash dp-flash-error">⚠️ {{ session('error') }}</div>
@endif

{{-- PAGE CONTENT --}}
<div class="dp-page-body">
    @yield('content')
</div>

{{-- BOTTOM BAR: LOGOUT --}}
<div class="dp-bottom-bar">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="dp-logout-btn">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
            Logout
        </button>
    </form>
</div>

@stack('scripts')
</body>
</html>
