<?php

use Illuminate\Support\Facades\Route;

// Include test routes (remove in production)
if (file_exists(__DIR__ . '/test-routes.php')) {
    require __DIR__ . '/test-routes.php';
}
use App\Http\Controllers\GadgetsController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StockAdjustmentsController;
use App\Http\Controllers\PurchaseOrdersController;

Route::get('/', function () {
    // If user is authenticated, redirect to dashboard
    if (session('api_token') || session('user_id')) {
        return redirect('/dashboard');
    }
    // Otherwise redirect to login
    return redirect('/login');
});

// Authentication Routes (no auth middleware required)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('no.cache');
Route::post('/login', [AuthController::class, 'login']);

// Logout routes - must be accessible without authentication (placed before middleware groups)
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');



// Public route for adding stock to gadgets (no authentication required)
Route::post('/stocks/add-to-gadget/{gadgetId}', [StocksController::class, 'addStockToGadget'])->name('stocks.add-to-gadget');

// Protected Routes (require authentication - admin only)
Route::middleware(['no.cache', 'session.timeout', 'web.auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gadgets
    Route::resource('gadgets', GadgetsController::class);
    Route::get('gadgets/deleted/list', [GadgetsController::class, 'deleted'])->name('gadgets.deleted');
    Route::post('gadgets/{id}/restore', [GadgetsController::class, 'restore'])->name('gadgets.restore');
    Route::delete('gadgets/{id}/permanent-delete', [GadgetsController::class, 'permanentDelete'])->name('gadgets.permanent-delete');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () {
            return view('reports.index');
        })->name('index');
        Route::get('/sales', [App\Http\Controllers\TransactionsController::class, 'salesReport'])->name('sales');
        Route::get('/suppliers', [App\Http\Controllers\SuppliersController::class, 'supplierReport'])->name('suppliers');
    });
});

// Admin-only Routes (require admin privileges)
Route::middleware(['no.cache', 'session.timeout', 'web.auth', 'admin'])->group(function () {
    // Stocks - Admin only (index, show, and CRUD)
    Route::get('/stocks', [StocksController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/{id}', [StocksController::class, 'show'])->name('stocks.show');
    Route::get('/stocks/create', [StocksController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [StocksController::class, 'store'])->name('stocks.store');
    Route::get('/stocks/{id}/edit', [StocksController::class, 'edit'])->name('stocks.edit');
    Route::put('/stocks/{id}', [StocksController::class, 'update'])->name('stocks.update');
    Route::patch('/stocks/{id}', [StocksController::class, 'update']);
    Route::delete('/stocks/{id}', [StocksController::class, 'destroy'])->name('stocks.destroy');
    
    // Transactions - Admin only (index, show, and CRUD)
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [TransactionsController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/create', [TransactionsController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionsController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{id}/edit', [TransactionsController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{id}', [TransactionsController::class, 'update'])->name('transactions.update');
    Route::patch('/transactions/{id}', [TransactionsController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionsController::class, 'destroy'])->name('transactions.destroy');
    
    // Suppliers - Admin only (index, show, and CRUD)
    // IMPORTANT: More specific routes (create, edit) must come BEFORE parameterized routes ({id})
    Route::get('/suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SuppliersController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{id}/edit', [SuppliersController::class, 'edit'])->name('suppliers.edit');
    Route::get('/suppliers/{id}', [SuppliersController::class, 'show'])->name('suppliers.show');
    Route::put('/suppliers/{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
    Route::patch('/suppliers/{id}', [SuppliersController::class, 'update']);
    Route::delete('/suppliers/{id}', [SuppliersController::class, 'destroy'])->name('suppliers.destroy');
    
    Route::resource('categories', CategoriesController::class);
    Route::resource('brands', BrandsController::class);
    Route::resource('admins', AdminsController::class);

    // Simple Stock Management Routes
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/low-stock', [StocksController::class, 'lowStock'])->name('low-stock');
    });

    // Stock Adjustments (Simple: Add/Remove/Set stock)
    Route::resource('stock-adjustments', StockAdjustmentsController::class)->except(['edit', 'update']);

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrdersController::class)->except(['edit', 'update']);
    Route::post('purchase-orders/{id}/update-status', [PurchaseOrdersController::class, 'updateStatus'])->name('purchase-orders.update-status');

});

