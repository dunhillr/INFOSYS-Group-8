<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\InventoryLog;
use App\Models\Production;
use App\Models\Sale;
use App\Models\UserLog;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function sales(): View
    {
        $sales = Sale::with(['saleItems.product', 'customer', 'user'])->latest()->paginate(20);
        return view('reports.sales', compact('sales'));
    }

    public function production(): View
    {
        $productions = Production::with(['product', 'user'])->latest()->paginate(20);
        return view('reports.production', compact('productions'));
    }

    public function inventory(): View
    {
        $inventoryLogs = InventoryLog::with(['inventory', 'creator'])->latest()->paginate(20);
        return view('reports.inventory', compact('inventoryLogs'));
    }

    public function delivery(): View
    {
        $deliveries = Delivery::with(['sale', 'customer', 'vehicle.driver'])->latest()->paginate(20);
        return view('reports.delivery', compact('deliveries'));
    }

    public function activity(): View
    {
        $logs = UserLog::with('user')->latest()->paginate(20);
        return view('reports.activity', compact('logs'));
    }
}
