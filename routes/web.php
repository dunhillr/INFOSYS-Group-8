<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class)->except('show');
    Route::resource('customers', CustomerController::class)->except('show');
    Route::get('productions/product-parent/{product}', [ProductionController::class, 'getProductParent'])->name('productions.product-parent');
    Route::resource('productions', ProductionController::class)->except('show');
    Route::resource('sales', SaleController::class)->except('show');
    Route::get('/sales-history', [SaleController::class, 'history'])->name('sales.history');
    Route::resource('vehicles', VehicleController::class)->except('show');
    Route::resource('deliveries', DeliveryController::class)->except('show');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('production', [ReportController::class, 'production'])->name('production');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('delivery', [ReportController::class, 'delivery'])->name('delivery');
    });

    Route::middleware('role:owner')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::get('reports/activity', [ReportController::class, 'activity'])->name('reports.activity');
    });
});

require __DIR__.'/auth.php';