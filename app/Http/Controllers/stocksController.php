<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use App\Models\Gadgets;
use App\Models\Suppliers;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StocksController extends Controller
{
    // Get all stocks with gadget & supplier info
    public function index()
    {
        // HYBRID APPROACH: Separate stats from paginated display
        // This allows accurate stats while maintaining performance for large datasets
        
        // Get ALL stocks for stats and filtered sections (needed for accurate counts)
        // Note: This excludes soft-deleted records by default (consistent with Eloquent behavior)
        $allStocks = Stocks::with(['gadget.category', 'gadget.brand', 'supplier'])->get();
        
        // Calculate stats from ALL active stocks (excludes soft-deleted records)
        // This ensures consistency: the count matches what's actually displayed in the system
        $stats = [
            'total_stocks' => $allStocks->count(),
            'well_stocked' => $allStocks->where('QuantityAdded', '>', 10)->count(),
            'low_stock' => $allStocks->where('QuantityAdded', '>', 0)->where('QuantityAdded', '<=', 10)->count(),
            'unique_gadgets' => $allStocks->unique('GadgetID')->count(),
        ];
        
        // Get filtered sections from ALL stocks (needed for separate display sections)
        // These already exclude soft-deleted records (default Eloquent behavior)
        $lowStockItems = $allStocks->where('QuantityAdded', '>', 0)->where('QuantityAdded', '<=', 10)->values();
        $outOfStockItems = $allStocks->where('QuantityAdded', 0)->values();
        
        // Paginate stocks for main table display (performance optimization)
        $stocks = Stocks::with(['gadget.category', 'gadget.brand', 'supplier'])
            ->orderBy('StockID', 'desc')
            ->paginate(15);
        
        // Get all gadgets (including those without stock) - no pagination needed for dropdown
        $allGadgets = Gadgets::with(['category', 'brand', 'stocks'])->get();
        
        // Get gadgets without stock
        $gadgetsWithoutStock = $allGadgets->filter(function($gadget) {
            return $gadget->stocks->isEmpty();
        });
        
        // Get suppliers for adding stock
        $suppliers = Suppliers::all();
        
        // Only return JSON for actual API routes (starts with /api/)
        if (request()->is('api/*')) {
            return response()->json([
                'stocks' => $stocks,
                'stats' => $stats,
                'low_stock_items' => $lowStockItems,
                'out_of_stock_items' => $outOfStockItems,
                'gadgets_without_stock' => $gadgetsWithoutStock,
                'suppliers' => $suppliers
            ], 200);
        }
        
        // Check if a specific gadget is requested via query parameter
        $selectedGadget = null;
        if (request()->has('gadget')) {
            $gadgetId = request()->get('gadget');
            $selectedGadget = Gadgets::with(['category', 'brand'])->find($gadgetId);
        }
        
        return view('stocks.index', compact('stocks', 'stats', 'gadgetsWithoutStock', 'suppliers', 'lowStockItems', 'outOfStockItems', 'selectedGadget'));
    }


    // Get single stock by ID
    public function show($id)
    {
        $stock = Stocks::with(['gadget.category', 'gadget.brand', 'gadget.stocks', 'supplier'])->find($id);

        if (!$stock) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Stock not found'], 404);
            }
            return redirect('/stocks')->with('error', 'Stock not found');
        }

        if (request()->expectsJson()) {
            return response()->json($stock, 200);
        }

        return view('stocks.show', compact('stock'));
    }

    // Show form to create new stock
    public function create()
    {
        $gadgets = Gadgets::with(['category', 'brand'])->get();
        $suppliers = Suppliers::all();
        
        // Check if a specific gadget is requested via query parameter
        $selectedGadget = null;
        if (request()->has('gadget')) {
            $gadgetId = request()->get('gadget');
            $selectedGadget = Gadgets::with(['category', 'brand'])->find($gadgetId);
        }
        
        if (request()->is('api/*') || request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'gadgets' => $gadgets,
                'suppliers' => $suppliers,
                'selected_gadget' => $selectedGadget
            ], 200);
        }
        
        return view('stocks.create', compact('gadgets', 'suppliers', 'selectedGadget'));
    }

    // Show edit form
    public function edit($id)
    {
        $stock = Stocks::find($id);
        
        if (!$stock) {
            if (request()->expectsJson() || request()->is('api/*')) {
                return response()->json(['message' => 'Stock not found'], 404);
            }
            return redirect('/stocks')->with('error', 'Stock not found');
        }
        
        $gadgets = Gadgets::with(['category', 'brand'])->get();
        $suppliers = Suppliers::all();
        
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'stock' => $stock,
                'gadgets' => $gadgets,
                'suppliers' => $suppliers
            ], 200);
        }
        
        return view('stocks.edit', compact('stock', 'gadgets', 'suppliers'));
    }

    // Update existing stock
    public function update(Request $request, $id)
    {
        $stock = Stocks::find($id);

        if (!$stock) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Stock not found'], 404);
            }
            return redirect('/stocks')->with('error', 'Stock not found');
        }

        $validated = $request->validate([
            'GadgetID'      => [
                'sometimes',
                Rule::exists('gadgets', 'GadgetID')->whereNull('deleted_at')
            ],
            'SupplierID'    => 'sometimes|exists:suppliers,SupplierID',
            'CostPrice'     => 'sometimes|numeric|min:0',
            'PurchaseDate'  => 'sometimes|date',
        ]);

        // Prevent direct quantity editing - use stock adjustments instead
        // QuantityAdded is intentionally excluded to maintain audit trail
        if ($request->has('QuantityAdded')) {
            return back()->withErrors([
                'QuantityAdded' => 'Quantity cannot be edited directly. Please use Stock Adjustments to change quantity for proper audit trail.'
            ])->withInput();
        }

        $stock->update($validated);
        
        if (request()->expectsJson()) {
            return response()->json($stock->load(['gadget', 'supplier']), 200);
        }
        
        // Redirect to stocks index (not dashboard)
        return redirect('/stocks')->with('success', 'Stock updated successfully!');
    }

    // Delete a stock record
    public function destroy($id)
    {
        $stock = Stocks::with('transactions')->find($id);
        if (!$stock) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Stock not found'], 404);
            }
            return redirect('/stocks')->with('error', 'Stock not found');
        }

        // Check if stock has transactions
        $transactionCount = $stock->transactions()->count();
        
        DB::beginTransaction();
        try {
            // Soft delete the stock (preserves data, just marks as deleted)
            // Transactions are preserved - StockID will be set to null by database constraint
            // This maintains the audit trail while removing the stock from active inventory
            $stock->delete();
            
            DB::commit();
        
        if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Stock deleted successfully.',
                    'transactions_preserved' => $transactionCount
                ], 200);
        }
        
        return redirect('/stocks')->with('success', 'Stock deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting stock: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Failed to delete stock'], 500);
            }
            
            return redirect('/stocks')->with('error', 'Failed to delete stock. Please try again.');
        }
    }

    
    // Get all stocks (simple)
    public function getStocks()
    {
        $stocks = Stocks::all();

        if ($stocks->isEmpty()) {
            return response()->json(['message' => 'No stocks available.'], 404);
        }

        return response()->json($stocks, 200);
    }

    // Get low-stock gadgets (default threshold = 6)
    public function getLowStock(Request $request)
    {
        $threshold = $request->query('threshold', 6);

        $stocks = Stocks::with('gadget')
            ->where('QuantityAdded', '<', $threshold)
            ->get();

        if ($stocks->isEmpty()) {
            return response()->json([
                'message' => 'No low-stock items found',
                'threshold_used' => $threshold
            ], 200);
        }

        return response()->json([
            'threshold_used' => $threshold,
            'total_low_stock' => $stocks->count(),
            'low_stock_items' => $stocks
        ], 200);
    }

    // Get out-of-stock gadgets
    public function getOutOfStock()
    {
        $stocks = Stocks::with('gadget')
            ->where('QuantityAdded', '=', 0)
            ->get();

        if ($stocks->isEmpty()) {
            return response()->json(['message' => 'No out-of-stock items found'], 200);
        }

        return response()->json([
            'total_out_of_stock' => $stocks->count(),
            'out_of_stock_items' => $stocks
        ], 200);
    }

    // Get total stock value
    public function getTotalStockValue()
    {
        $total = DB::table('stocks')
            ->sum(DB::raw('QuantityAdded * CostPrice'));

        return response()->json([
            'total_stock_value' => round($total, 2)
        ], 200);
    }


    // ===== INVENTORY MANAGEMENT METHODS =====
    
    public function lowStock()
    {
        $lowStockItems = Stocks::with(['gadget.category', 'gadget.brand', 'supplier'])
            ->where('QuantityAdded', '<', 10)
            ->orderBy('QuantityAdded', 'asc')
            ->get();
        
        // Get suppliers for adding stock
        $suppliers = Suppliers::all();
        
        // Only return JSON for actual API routes (starts with /api/)
        if (request()->is('api/*')) {
            return response()->json([
                'threshold_used' => 10,
                'total_low_stock' => $lowStockItems->count(),
                'low_stock_items' => $lowStockItems
            ], 200);
        }
            
        return view('stocks.low-stock', compact('lowStockItems', 'suppliers'));
    }

    public function restock(Request $request, $id)
    {
        $request->validate([
            'QuantityAdded' => 'required|integer|min:1'
        ]);

        $stock = Stocks::find($id);
        
        if (!$stock) {
            return redirect()->back()->with('error', 'Stock not found');
        }

        DB::beginTransaction();
        try {
            $stock->QuantityAdded += $request->QuantityAdded;
            $stock->save();

            // Log the restock transaction
            // Get current logged-in admin from session
            $currentAdminId = session('user_id');
            Transactions::create([
                'GadgetID' => $stock->GadgetID,
                'AdminID' => $currentAdminId, // Set AdminID to current logged-in admin
                'StockID' => $stock->StockID,
                'TransactionType' => 'IN',
                'Quantity' => $request->QuantityAdded,
                'TransactionDate' => now()
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Stock restocked successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stock restock failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restock. Please try again.');
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    private function getInventorySummary()
    {
        return [
            'total_items' => Gadgets::count(),
            'total_stock' => Stocks::sum('QuantityAdded'),
            'low_stock_count' => Stocks::where('QuantityAdded', '<', 10)->count(),
            'out_of_stock_count' => Stocks::where('QuantityAdded', 0)->count(),
            'total_value' => DB::table('stocks')->sum(DB::raw('QuantityAdded * CostPrice'))
        ];
    }

    private function getLowStockAlerts()
    {
        return Stocks::with(['gadget', 'supplier'])
            ->where('QuantityAdded', '<', 10)
            ->orderBy('QuantityAdded', 'asc')
            ->limit(10)
            ->get();
    }

    private function getStockMovements()
    {
        return Transactions::with(['gadget', 'admin'])
            ->where('TransactionType', 'IN')
            ->orderBy('TransactionDate', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($transaction) {
                return (object) [
                    'id' => $transaction->TransactionID,
                    'gadget' => $transaction->gadget,
                    'movement_type' => 'in',
                    'quantity_change' => $transaction->Quantity,
                    'previous_quantity' => 0, // This would need to be calculated based on your business logic
                    'new_quantity' => $transaction->Quantity,
                    'user_name' => $transaction->admin ? $transaction->admin->Username : 'System',
                    'created_at' => $transaction->TransactionDate
                ];
            });
    }

    private function getInventoryValue()
    {
        return [
            'total_cost_value' => DB::table('stocks')->sum(DB::raw('QuantityAdded * CostPrice'))
        ];
    }

    private function getStockByCategory()
    {
        return DB::table('stocks')
            ->join('gadgets', 'stocks.GadgetID', '=', 'gadgets.GadgetID')
            ->join('categories', 'gadgets.CategoryID', '=', 'categories.CategoryID')
            ->select(
                'categories.CategoryName',
                DB::raw('SUM(stocks.QuantityAdded) as total_quantity'),
                DB::raw('SUM(stocks.QuantityAdded * stocks.CostPrice) as total_cost_value')
            )
            ->groupBy('categories.CategoryID', 'categories.CategoryName')
            ->orderByDesc('total_cost_value')
            ->get();
    }

    private function getStockBySupplier()
    {
        return DB::table('stocks')
            ->join('suppliers', 'stocks.SupplierID', '=', 'suppliers.SupplierID')
            ->select(
                'suppliers.SupplierName',
                DB::raw('SUM(stocks.QuantityAdded) as total_quantity'),
                DB::raw('SUM(stocks.QuantityAdded * stocks.CostPrice) as total_cost_value')
            )
            ->groupBy('suppliers.SupplierID', 'suppliers.SupplierName')
            ->orderByDesc('total_cost_value')
            ->get();
    }



    // Create new stock entry
    public function store(Request $request)
    {
        $validated = $request->validate([
            'GadgetID' => [
                'required',
                \Illuminate\Validation\Rule::exists('gadgets', 'GadgetID')->whereNull('deleted_at')
            ],
            'SupplierID' => 'nullable|exists:suppliers,SupplierID',
            'QuantityAdded' => 'required|integer|min:1',
            'CostPrice' => 'required|numeric|min:0',
            'PurchaseDate' => 'required|date'
        ]);

        // Convert date string to timestamp for database
        $validated['PurchaseDate'] = \Carbon\Carbon::parse($validated['PurchaseDate'])->format('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            $stock = Stocks::create($validated);
            
            // Create transaction record for stock addition
            // Get current logged-in admin from session
            $currentAdminId = session('user_id');
            Transactions::create([
                'GadgetID' => $validated['GadgetID'],
                'AdminID' => $currentAdminId, // Set AdminID to current logged-in admin
                'StockID' => $stock->StockID,
                'TransactionType' => 'IN',
                'Quantity' => $validated['QuantityAdded'],
                'TransactionDate' => now()
            ]);
            
            DB::commit();
            
            if (request()->is('api/*') || $request->expectsJson()) {
                return response()->json($stock, 201);
            }
            
            return redirect('/stocks')
                ->with('success', 'Stock added successfully!')
                ->with('stock_id', $stock->StockID);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stock creation failed: ' . $e->getMessage());
            
            if (request()->is('api/*') || $request->expectsJson()) {
                return response()->json(['error' => 'Failed to create stock: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create stock. Please try again.')->withInput();
        }
    }

    // Add stock to specific gadget
    public function addStockToGadget(Request $request, $gadgetId)
    {
        try {
            // Debug: Log request data
            \Log::info('Add Stock Request:', [
                'gadgetId' => $gadgetId,
                'request_data' => $request->all(),
                'headers' => $request->headers->all(),
                'request_method' => $request->method(),
                'request_url' => $request->url()
            ]);

            // Validate gadget exists and is not soft-deleted
            $gadget = Gadgets::whereNull('deleted_at')->find($gadgetId);
            
            if (!$gadget) {
                \Log::error('Gadget not found or deleted:', ['gadgetId' => $gadgetId]);
                if (request()->expectsJson()) {
                    return response()->json(['message' => 'Gadget not found or has been deleted'], 404);
                }
                return redirect('/stocks')->with('error', 'Gadget not found or has been deleted');
            }

            $validated = $request->validate([
                'SupplierID' => 'nullable|exists:suppliers,SupplierID',
                'QuantityAdded' => 'required|integer|min:1',
                'CostPrice' => 'required|numeric|min:0',
                'PurchaseDate' => 'required|date'
            ]);

            // Convert date string to timestamp for database
            $validated['GadgetID'] = $gadgetId;
            $validated['PurchaseDate'] = \Carbon\Carbon::parse($validated['PurchaseDate'])->format('Y-m-d H:i:s');
            
            DB::beginTransaction();
            try {
                $stock = Stocks::create($validated);
                
                // Create transaction record for stock addition
                // Get current logged-in admin from session
                $currentAdminId = session('user_id');
                Transactions::create([
                    'GadgetID' => $gadgetId,
                    'AdminID' => $currentAdminId, // Set AdminID to current logged-in admin
                    'StockID' => $stock->StockID,
                    'TransactionType' => 'IN',
                    'Quantity' => $validated['QuantityAdded'],
                    'TransactionDate' => now()
                ]);
                
                DB::commit();
                
                \Log::info('Stock created successfully:', ['stock' => $stock]);
                
                // Set session flash message for dashboard display after reload
                session()->flash('success', 'Stock added successfully!');
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'StockID' => $stock->StockID,
                        'message' => 'Stock added successfully!'
                    ], 201);
                }
                
                return redirect('/stocks')
                    ->with('success', 'Stock added successfully!')
                    ->with('stock_id', $stock->StockID);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', ['errors' => $e->errors()]);
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Stock creation failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add stock: ' . $e->getMessage()
                ], 500);
            }
            return redirect('/stocks')->with('error', 'Failed to add stock: ' . $e->getMessage());
        }
    }

}
