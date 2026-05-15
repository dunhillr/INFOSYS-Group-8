<div class="main-header-container container-fluid">
<div class="header-content-left"><div class="header-element"><a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a></div></div>
<div class="header-content-right">
<div class="header-element">
    @php
        $hasUnread = \App\Models\SystemNotification::where('user_id', auth()->id())->where('is_read', false)->exists();
    @endphp
    <a href="{{ route('notifications.index') }}" class="header-link relative">
        <i class="bx bx-bell header-link-icon"></i>
        @if($hasUnread)
            <span class="absolute top-2 right-2 block h-2 w-2 rounded-full bg-yellow-400 ring-2 ring-white"></span>
        @endif
    </a>
</div>
<div class="header-element"><div class="hs-dropdown ti-dropdown"><a href="javascript:void(0);" class="flex items-center hs-dropdown-toggle ti-dropdown-toggle header-link"><span class="hidden xl:block leading-none px-2"><span class="block font-semibold text-sm">{{ auth()->user()->name }}</span><span class="block text-xs text-[#8c9097]">{{ strtoupper(auth()->user()->user_type) }}</span></span></a><ul class="hs-dropdown-menu ti-dropdown-menu hidden"><li><a class="ti-dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li><li><form action="{{ route('logout') }}" method="POST">@csrf <button type="submit" class="ti-dropdown-item w-full text-start">Logout</button></form></li></ul></div></div>
</div></div>