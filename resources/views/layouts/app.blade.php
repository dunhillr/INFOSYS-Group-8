<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bobong Ice Plant')</title>
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/brand-logos/favicon.ico') }}">
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/libs/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/libs/@simonwep/pickr/themes/nano.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/libs/jsvectormap/css/jsvectormap.min.css') }}">
    @stack('styles')

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
<div id="loader"><img src="{{ asset('backend/assets/images/media/loader.svg') }}" alt=""></div>
<div class="page">
<header class="app-header">@include('layouts.navbar')</header>
<aside class="app-sidebar" id="sidebar">@include('layouts.sidebar')</aside>
<div class="content"><div class="main-content"><div class="container-fluid">@include('layouts.flash') @yield('content')</div></div></div>
@include('layouts.footer')
</div>
<div id="responsive-overlay"></div>
<script src="{{ asset('backend/assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/defaultmenu.js') }}"></script>
<script src="{{ asset('backend/assets/js/switch.js') }}"></script>
<script src="{{ asset('backend/assets/js/sticky.js') }}"></script>
<script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/preline/preline.js') }}"></script>
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('backend/assets/js/us-merc-en.js') }}"></script>
<script src="{{ asset('backend/assets/js/custom-switcher.js') }}"></script>
<script src="{{ asset('backend/assets/js/custom.js') }}"></script>

{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

{{-- ══════════════════════════════════════════════════════
     GLOBAL: SweetAlert2 Delete Confirmation Interceptor
     Add data-confirm-delete to any <form> to use this.
     Optional attributes:
       data-confirm-title   — dialog title  (default: "Sigurado ka ba?")
       data-confirm-text    — dialog body   (default: generic message)
       data-confirm-item    — item name shown in body
     ══════════════════════════════════════════════════════ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Delete Confirmation ──────────────────────────────
    document.querySelectorAll('form[data-confirm-delete]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const title = form.dataset.confirmTitle  || 'Are you sure?';
            const item  = form.dataset.confirmItem   || '';
            const text  = form.dataset.confirmText   ||
                (item
                    ? `The <strong>${item}</strong> will be permanently deleted. This action cannot be undone.`
                    : 'This record will be permanently deleted. This action cannot be undone.');

            Swal.fire({
                title: title,
                html: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor:  '#6b7280',
                confirmButtonText: 'Confirm',
                cancelButtonText:  'Cancel',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup:         'rounded-2xl shadow-2xl',
                    confirmButton: 'rounded-lg px-5 py-2 text-sm font-bold',
                    cancelButton:  'rounded-lg px-5 py-2 text-sm font-semibold',
                },
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // ── Cancel Delivery Confirmation ─────────────────────
    document.querySelectorAll('form[data-confirm-cancel]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Cancel Delivery?',
                html: 'The system will return the items to the inventory. <br><span class="text-xs text-gray-500">This action cannot be undone once confirmed.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor:  '#6b7280',
                confirmButtonText: 'Confirm',
                cancelButtonText:  'Cancel',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup:         'rounded-2xl shadow-2xl',
                    confirmButton: 'rounded-lg px-5 py-2 text-sm font-bold',
                    cancelButton:  'rounded-lg px-5 py-2 text-sm font-semibold',
                },
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // ── Success Toast (fires on session success) ─────────
    @if(session('success'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        customClass: { popup: 'rounded-xl shadow-lg text-sm' },
    });
    @endif

    // ── Error Toast (fires on session error) ─────────────
    @if(session('error'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: @json(session('error')),
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-xl shadow-lg text-sm' },
    });
    @endif

});
</script>

@stack('scripts')
</body>
</html>
