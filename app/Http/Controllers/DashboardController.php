<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Inventory;
use App\Models\Production;
use App\Models\Sale;
use App\Models\SystemNotification;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Per-product inventory (available stock per product)
        $inventories = Inventory::with('product')
            ->whereNotNull('product_id')
            ->get();

        $todayProduction = Production::query()->whereDate('production_date', now()->toDateString())->sum('quantity_produced');
        $todaySales = Sale::query()->whereDate('sale_date', now()->toDateString())->sum('total_amount');
        $pendingDeliveries = Delivery::query()->where('status', 'pending')->count();
        $inTransitDeliveries = Delivery::query()->where('status', 'out_for_delivery')->count();

        // Today's production breakdown per product
        $todayProductionByProduct = Production::query()
            ->with('product')
            ->whereDate('production_date', now()->toDateString())
            ->selectRaw('product_id, SUM(quantity_produced) as total_produced')
            ->groupBy('product_id')
            ->get();

        $notifications = SystemNotification::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        $logs = UserLog::query()->with('user')->latest()->take(10)->get();

        $recentDeliveries = Delivery::with(['customer', 'vehicle'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'inventories',
            'todayProduction',
            'todaySales',
            'pendingDeliveries',
            'inTransitDeliveries',
            'todayProductionByProduct',
            'notifications',
            'logs',
            'recentDeliveries'
        ));
    }
}
