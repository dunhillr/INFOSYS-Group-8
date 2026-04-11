<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bobong Ice Plant')</title>
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/brand-logos/favicon.ico') }}">
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/libs/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/libs/@simonwep/pickr/themes/nano.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/libs/jsvectormap/css/jsvectormap.min.css') }}">
    @stack('styles')
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
@stack('scripts')
</body>
</html>
