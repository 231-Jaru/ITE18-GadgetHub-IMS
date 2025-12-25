<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Controllers
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\GadgetsController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseOrdersController;
use App\Http\Controllers\StockAdjustmentsController;

// Test route
Route::get('/ping', fn () => response()->json(['message' => 'API is working!']));
    // curl -X GET http://127.0.0.1:8000/api/ping \
    //     -H "Content-Type: application/json"

/*
||----------------------------
||  AUTHENTICATION ROUTES
||----------------------------
*/
Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
    // --------------- Register and Login ----------------//
    Route::post('register', 'AuthenticationController@register')->name('api.register');
        // curl -X POST http://127.0.0.1:8000/api/register \
        //     -H "Content-Type: application/json" \
        //     -d '{
        //         "name": "test-user-name",
        //         "email": "test@example.com",
        //         "password": "test-password"
        //     }'
    
    Route::post('login', 'AuthenticationController@login')->name('api.login');
        // curl -X POST http://127.0.0.1:8000/api/login \
        //     -H "Content-Type: application/json" \
        //     -d '{
        //         "email": "test@example.com",
        //         "password": "test-password"
        //     }'
    
    // Handle GET requests to login endpoint with helpful error message
    // This is only for direct API access via browser - web forms use AuthController
    Route::get('login', function () {
        return response()->json([
            'response_code' => 405,
            'status' => 'error',
            'message' => 'Method Not Allowed. The login endpoint only accepts POST requests.',
            'hint' => 'Use POST method with email and password in the request body. For web login, use /login instead of /api/login.'
        ], 405);
    });
        // curl -X GET http://127.0.0.1:8000/api/login \
        //     -H "Content-Type: application/json"
    
    // Handle GET requests to register endpoint
    Route::get('register', function () {
        return response()->json([
            'response_code' => 405,
            'status' => 'error',
            'message' => 'Method Not Allowed. The register endpoint only accepts POST requests.',
            'hint' => 'Use POST method with name, email, and password in the request body. For web registration, use /register instead of /api/register.'
        ], 405);
    });
        // curl -X GET http://127.0.0.1:8000/api/register \
        //     -H "Content-Type: application/json"
    
    // ------------------ Get Data ----------------------//
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('get-user', 'AuthenticationController@userInfo')->name('get-user');
            // curl -X GET http://127.0.0.1:8000/api/get-user \
            //     -H "Content-Type: application/json" \
            //     -H "Authorization: Bearer {your-token-here}"
        
        // Logout Route
        Route::post('logout', 'AuthenticationController@logOut')->name('logout');
            // curl -X POST http://127.0.0.1:8000/api/logout \
            //     -H "Content-Type: application/json" \
            //     -H "Authorization: Bearer {your-token-here}"
    });
});

// Protected API Routes (outside namespace group to avoid namespace conflicts)
Route::middleware('auth:sanctum')->group(function () {
    // Stocks Management API Routes
    Route::get('/stocks/low', [StocksController::class, 'lowStock']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/low \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/stocks/out', [StocksController::class, 'outOfStock']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/out \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/stocks/value', [StocksController::class, 'stockValue']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/value \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/stocks/inventory', [StocksController::class, 'inventory']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/inventory \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/stocks/low-stock', [StocksController::class, 'lowStock']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/low-stock \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/stocks/out-of-stock', [StocksController::class, 'outOfStock']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/out-of-stock \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/stocks/stock-value', [StocksController::class, 'stockValue']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/stock-value \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/stocks/stock-movement', [StocksController::class, 'stockMovement']);
        // curl -X GET http://127.0.0.1:8000/api/stocks/stock-movement \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::put('/stocks/{id}/restock', [StocksController::class, 'restock']);
        // curl -X PUT http://127.0.0.1:8000/api/stocks/1/restock \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "quantity": 100
        //     }'
    
    Route::post('/stocks/add-to-gadget/{gadgetId}', [StocksController::class, 'addStockToGadget']);
        // curl -X POST http://127.0.0.1:8000/api/stocks/add-to-gadget/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "quantity": 50,
        //         "supplier_id": 1
        //     }'

    // Transactions Summary & Reports
    Route::get('/transactions/purchases/monthly', [TransactionsController::class, 'getMonthlyPurchases']);
        // curl -X GET http://127.0.0.1:8000/api/transactions/purchases/monthly \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/transactions/purchase-report-data', [TransactionsController::class, 'getPurchaseReportData']);
        // curl -X GET http://127.0.0.1:8000/api/transactions/purchase-report-data \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/gadgets/top-selling', [GadgetsController::class, 'getTopSelling']);
        // curl -X GET http://127.0.0.1:8000/api/gadgets/top-selling \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/suppliers/performance', [SuppliersController::class, 'getSupplierPerformance']);
        // curl -X GET http://127.0.0.1:8000/api/suppliers/performance \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/suppliers/report-data', [SuppliersController::class, 'getSupplierReportData']);
        // curl -X GET http://127.0.0.1:8000/api/suppliers/report-data \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/categories/{id}/gadgets', [CategoriesController::class, 'getGadgetsByCategory']);
        // curl -X GET http://127.0.0.1:8000/api/categories/1/gadgets \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"

    // Dashboard Analytics API Routes
    Route::get('/dashboard/analytics', [DashboardController::class, 'index']);
        // curl -X GET http://127.0.0.1:8000/api/dashboard/analytics \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::get('/dashboard/summary', [DashboardController::class, 'index']);
        // curl -X GET http://127.0.0.1:8000/api/dashboard/summary \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"

    /*
    |-----------------------------------
    |  RESOURCE ROUTES
    |-----------------------------------
    */
    Route::resource('admins', AdminsController::class);
        // GET    /api/admins          - List all admins
        // curl -X GET http://127.0.0.1:8000/api/admins \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/admins          - Create new admin
        // curl -X POST http://127.0.0.1:8000/api/admins \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Admin Name",
        //         "email": "admin@example.com",
        //         "password": "password"
        //     }'
        //
        // GET    /api/admins/{id}     - Get specific admin
        // curl -X GET http://127.0.0.1:8000/api/admins/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // PUT    /api/admins/{id}     - Update admin
        // curl -X PUT http://127.0.0.1:8000/api/admins/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Updated Admin Name",
        //         "email": "updated@example.com"
        //     }'
        //
        // DELETE /api/admins/{id}     - Delete admin
        // curl -X DELETE http://127.0.0.1:8000/api/admins/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::resource('categories', CategoriesController::class);
        // GET    /api/categories          - List all categories
        // curl -X GET http://127.0.0.1:8000/api/categories \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/categories          - Create new category
        // curl -X POST http://127.0.0.1:8000/api/categories \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Category Name",
        //         "description": "Category Description"
        //     }'
        //
        // GET    /api/categories/{id}     - Get specific category
        // curl -X GET http://127.0.0.1:8000/api/categories/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // PUT    /api/categories/{id}     - Update category
        // curl -X PUT http://127.0.0.1:8000/api/categories/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Updated Category Name",
        //         "description": "Updated Description"
        //     }'
        //
        // DELETE /api/categories/{id}     - Delete category
        // curl -X DELETE http://127.0.0.1:8000/api/categories/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::resource('brands', BrandsController::class);
        // GET    /api/brands          - List all brands
        // curl -X GET http://127.0.0.1:8000/api/brands \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/brands          - Create new brand
        // curl -X POST http://127.0.0.1:8000/api/brands \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Brand Name",
        //         "description": "Brand Description"
        //     }'
        //
        // GET    /api/brands/{id}     - Get specific brand
        // curl -X GET http://127.0.0.1:8000/api/brands/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // PUT    /api/brands/{id}     - Update brand
        // curl -X PUT http://127.0.0.1:8000/api/brands/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Updated Brand Name",
        //         "description": "Updated Description"
        //     }'
        //
        // DELETE /api/brands/{id}     - Delete brand
        // curl -X DELETE http://127.0.0.1:8000/api/brands/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::resource('gadgets', GadgetsController::class);
        // GET    /api/gadgets          - List all gadgets
        // curl -X GET http://127.0.0.1:8000/api/gadgets \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/gadgets          - Create new gadget
        // curl -X POST http://127.0.0.1:8000/api/gadgets \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Gadget Name",
        //         "description": "Gadget Description",
        //         "price": 999.99,
        //         "category_id": 1,
        //         "brand_id": 1
        //     }'
        //
        // GET    /api/gadgets/{id}     - Get specific gadget
        // curl -X GET http://127.0.0.1:8000/api/gadgets/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // PUT    /api/gadgets/{id}     - Update gadget
        // curl -X PUT http://127.0.0.1:8000/api/gadgets/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Updated Gadget Name",
        //         "price": 899.99
        //     }'
        //
        // DELETE /api/gadgets/{id}     - Delete gadget
        // curl -X DELETE http://127.0.0.1:8000/api/gadgets/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::resource('stocks', StocksController::class)->except(['create', 'store']);
        // GET    /api/stocks          - List all stocks
        // curl -X GET http://127.0.0.1:8000/api/stocks \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // GET    /api/stocks/{id}     - Get specific stock
        // curl -X GET http://127.0.0.1:8000/api/stocks/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // PUT    /api/stocks/{id}     - Update stock
        // curl -X PUT http://127.0.0.1:8000/api/stocks/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "quantity": 150
        //     }'
        //
        // DELETE /api/stocks/{id}     - Delete stock
        // curl -X DELETE http://127.0.0.1:8000/api/stocks/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::resource('suppliers', SuppliersController::class);
        // GET    /api/suppliers          - List all suppliers
        // curl -X GET http://127.0.0.1:8000/api/suppliers \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/suppliers          - Create new supplier
        // curl -X POST http://127.0.0.1:8000/api/suppliers \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Supplier Name",
        //         "email": "supplier@example.com",
        //         "phone": "1234567890",
        //         "address": "Supplier Address"
        //     }'
        //
        // GET    /api/suppliers/{id}     - Get specific supplier
        // curl -X GET http://127.0.0.1:8000/api/suppliers/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // PUT    /api/suppliers/{id}     - Update supplier
        // curl -X PUT http://127.0.0.1:8000/api/suppliers/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "name": "Updated Supplier Name",
        //         "email": "updated@example.com"
        //     }'
        //
        // DELETE /api/suppliers/{id}     - Delete supplier
        // curl -X DELETE http://127.0.0.1:8000/api/suppliers/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::resource('transactions', TransactionsController::class);
        // GET    /api/transactions          - List all transactions
        // curl -X GET http://127.0.0.1:8000/api/transactions \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/transactions          - Create new transaction
        // curl -X POST http://127.0.0.1:8000/api/transactions \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "type": "purchase",
        //         "gadget_id": 1,
        //         "quantity": 10,
        //         "price": 999.99,
        //         "supplier_id": 1
        //     }'
        //
        // GET    /api/transactions/{id}     - Get specific transaction
        // curl -X GET http://127.0.0.1:8000/api/transactions/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // PUT    /api/transactions/{id}     - Update transaction
        // curl -X PUT http://127.0.0.1:8000/api/transactions/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "quantity": 15,
        //         "price": 899.99
        //     }'
        //
        // DELETE /api/transactions/{id}     - Delete transaction
        // curl -X DELETE http://127.0.0.1:8000/api/transactions/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::resource('purchase-orders', PurchaseOrdersController::class)->except(['create', 'edit']);
        // GET    /api/purchase-orders          - List all purchase orders
        // curl -X GET http://127.0.0.1:8000/api/purchase-orders \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/purchase-orders          - Create new purchase order
        // curl -X POST http://127.0.0.1:8000/api/purchase-orders \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "SupplierID": 1,
        //         "OrderDate": "2024-01-15",
        //         "ExpectedDeliveryDate": "2024-01-30",
        //         "Notes": "Urgent order",
        //         "items": [
        //             {
        //                 "GadgetID": 1,
        //                 "Quantity": 10,
        //                 "UnitCost": 99.99
        //             },
        //             {
        //                 "GadgetID": 2,
        //                 "Quantity": 5,
        //                 "UnitCost": 149.99
        //             }
        //         ]
        //     }'
        //
        // GET    /api/purchase-orders/{id}     - Get specific purchase order
        // curl -X GET http://127.0.0.1:8000/api/purchase-orders/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // DELETE /api/purchase-orders/{id}     - Delete purchase order (only DRAFT status)
        // curl -X DELETE http://127.0.0.1:8000/api/purchase-orders/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
    
    Route::put('/purchase-orders/{id}/status', [PurchaseOrdersController::class, 'updateStatus']);
        // PUT    /api/purchase-orders/{id}/status     - Update purchase order status
        // curl -X PUT http://127.0.0.1:8000/api/purchase-orders/1/status \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "Status": "RECEIVED"
        //     }'
    
    Route::resource('stock-adjustments', StockAdjustmentsController::class)->except(['create', 'edit', 'update']);
        // GET    /api/stock-adjustments          - List all stock adjustments
        // curl -X GET http://127.0.0.1:8000/api/stock-adjustments \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // POST   /api/stock-adjustments          - Create new stock adjustment
        // curl -X POST http://127.0.0.1:8000/api/stock-adjustments \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}" \
        //     -d '{
        //         "GadgetID": 1,
        //         "StockID": 1,
        //         "AdjustmentType": "INCREASE",
        //         "QuantityChanged": 10,
        //         "Reason": "Found inventory",
        //         "Notes": "Additional stock found in warehouse"
        //     }'
        //
        // GET    /api/stock-adjustments/{id}     - Get specific stock adjustment
        // curl -X GET http://127.0.0.1:8000/api/stock-adjustments/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
        //
        // DELETE /api/stock-adjustments/{id}     - Delete stock adjustment
        // curl -X DELETE http://127.0.0.1:8000/api/stock-adjustments/1 \
        //     -H "Content-Type: application/json" \
        //     -H "Authorization: Bearer {your-token-here}"
});

