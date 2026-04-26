<div class="main-sidebar-header bg-white border-b border-gray-200">

    <a href="{{ route('dashboard') }}" class="header-logo flex items-center px-4 py-3 gap-2">

        <!-- LOGO -->
        <img src="{{ asset('images/logo.png') }}" 
             alt="logo" 
             class="h-8 w-8 object-contain">

        <!-- BRAND NAME -->
        <span class="text-blue-600 font-bold text-sm">
            Bobong Ice Plant
        </span>

    </a>

</div>

<div class="main-sidebar bg-white" id="sidebar-scroll">

    <nav class="main-menu-container nav nav-pills flex-column sub-open">

        <ul class="main-menu space-y-1 px-2 py-3">

            <!-- DASHBOARD -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-home text-lg text-blue-500"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- PRODUCTS -->
            <li>
                <a href="{{ route('products.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-box text-lg text-blue-500"></i>
                    <span>Products</span>
                </a>
            </li>

            <!-- CUSTOMERS -->
            <li>
                <a href="{{ route('customers.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-group text-lg text-blue-500"></i>
                    <span>Customers</span>
                </a>
            </li>

            <!-- PRODUCTIONS -->
            <li>
                <a href="{{ route('productions.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-cog text-lg text-blue-500"></i>
                    <span>Productions</span>
                </a>
            </li>

            <!-- SALES -->
            <li>
                <a href="{{ route('sales.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-cart text-lg text-blue-500"></i>
                    <span>Sales</span>
                </a>
            </li>

            <!-- SALES HISTORY -->
            <li>
                <a href="{{ route('sales.history') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition ml-2">
                    <i class="bx bx-history text-lg text-blue-500"></i>
                    <span>Sales History</span>
                </a>
            </li>

            <!-- VEHICLES -->
            <li>
                <a href="{{ route('vehicles.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-car text-lg text-blue-500"></i>
                    <span>Vehicles</span>
                </a>
            </li>

            <!-- NOTIFICATIONS -->
            <li>
                <a href="{{ route('notifications.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-bell text-lg text-blue-500"></i>
                    <span>Notifications</span>
                </a>
            </li>

            <!-- REPORTS -->
            <li>
                <a href="{{ route('reports.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-bar-chart-alt-2 text-lg text-blue-500"></i>
                    <span>Reports</span>
                </a>
            </li>

            <!-- OWNER ONLY -->
            @if(auth()->user()->user_type === 'owner')

            <li class="mt-3 border-t pt-3">

                <a href="{{ route('users.index') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-user text-lg text-blue-500"></i>
                    <span>Users</span>
                </a>

            </li>

            <li>

                <a href="{{ route('reports.activity') }}" 
                   class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="bx bx-history text-lg text-blue-500"></i>
                    <span>Activity Logs</span>
                </a>

            </li>

            @endif

        </ul>

    </nav>

</div>