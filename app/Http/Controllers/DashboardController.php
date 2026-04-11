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
        $inventory = Inventory::first();

        $todayProduction = Production::query()->whereDate('production_date', now()->toDateString())->sum('quantity_produced');
        $todaySales = Sale::query()->whereDate('sale_date', now()->toDateString())->sum('total_amount');
        $pendingDeliveries = Delivery::query()->where('status', 'pending')->count();

        $notifications = SystemNotification::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        $logs = UserLog::query()->with('user')->latest()->take(10)->get();

        return view('dashboard.index', compact(
            'inventory',
            'todayProduction',
            'todaySales',
            'pendingDeliveries',
            'notifications',
            'logs'
        ));
    }
}
