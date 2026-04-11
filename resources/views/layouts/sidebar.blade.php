<div class="main-sidebar-header"><a href="{{ route('dashboard') }}" class="header-logo"><img src="{{ asset('backend/assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="desktop-logo"><span class="text-white font-semibold ms-2">Bobong Ice Plant</span></a></div>
<div class="main-sidebar" id="sidebar-scroll"><nav class="main-menu-container nav nav-pills flex-column sub-open"><ul class="main-menu">
<li class="slide"><a href="{{ route('dashboard') }}" class="side-menu__item"><i class="bx bx-home side-menu__icon"></i><span class="side-menu__label">Dashboard</span></a></li>
<li class="slide"><a href="{{ route('products.index') }}" class="side-menu__item"><i class="bx bx-box side-menu__icon"></i><span class="side-menu__label">Products</span></a></li>
<li class="slide"><a href="{{ route('customers.index') }}" class="side-menu__item"><i class="bx bx-group side-menu__icon"></i><span class="side-menu__label">Customers</span></a></li>
<li class="slide"><a href="{{ route('productions.index') }}" class="side-menu__item"><i class="bx bx-cog side-menu__icon"></i><span class="side-menu__label">Productions</span></a></li>
<li class="slide"><a href="{{ route('sales.index') }}" class="side-menu__item"><i class="bx bx-cart side-menu__icon"></i><span class="side-menu__label">Sales</span></a></li>
<li class="slide"><a href="{{ route('vehicles.index') }}" class="side-menu__item"><i class="bx bx-car side-menu__icon"></i><span class="side-menu__label">Vehicles</span></a></li>
<li class="slide"><a href="{{ route('deliveries.index') }}" class="side-menu__item"><i class="bx bx-map side-menu__icon"></i><span class="side-menu__label">Deliveries</span></a></li>
<li class="slide"><a href="{{ route('notifications.index') }}" class="side-menu__item"><i class="bx bx-bell side-menu__icon"></i><span class="side-menu__label">Notifications</span></a></li>
<li class="slide"><a href="{{ route('reports.index') }}" class="side-menu__item"><i class="bx bx-bar-chart-alt-2 side-menu__icon"></i><span class="side-menu__label">Reports</span></a></li>
@if(auth()->user()->user_type === 'owner')
<li class="slide"><a href="{{ route('users.index') }}" class="side-menu__item"><i class="bx bx-user side-menu__icon"></i><span class="side-menu__label">Users</span></a></li>
<li class="slide"><a href="{{ route('reports.activity') }}" class="side-menu__item"><i class="bx bx-history side-menu__icon"></i><span class="side-menu__label">Activity Logs</span></a></li>
@endif
</ul></nav></div>