<?php

namespace App\Http\Controllers;

use App\Models\Gadgets;
use App\Models\Stocks;
use App\Models\Suppliers;
use App\Models\Transactions;
use App\Helpers\CurrencyHelper;
use App\Repositories\GadgetsRepository;
use App\Http\Controllers\Traits\HandlesAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GadgetsController extends Controller
{
    use HandlesAuthorization;

    protected $repository;

    public function __construct(GadgetsRepository $repository)
    {
        $this->repository = $repository;
    }
    // Get all gadgets with brand, category, and stock info
    public function index()
    {
        // For web view: Get all gadgets (needed for grouping, filtering, and stats)
        // This works well for small to medium datasets (< 1000 gadgets)
        // For larger datasets, consider implementing:
        // 1. Separate stats queries (cached)
        // 2. AJAX-based filtering/pagination
        // 3. Server-side filtering
        
        $gadgets = Gadgets::with(['category', 'brand', 'stocks'])->get();
        
        // Only return JSON for actual API routes (starts with /api/)
        if (request()->is('api/*')) {
            // Check if pagination is requested (for list views)
            // If 'all' parameter is present or no page parameter, return all gadgets (for dropdowns)
            if (request()->has('all') || !request()->has('page')) {
                // Return all gadgets for dropdowns and forms
                $gadgets = Gadgets::with(['category', 'brand', 'stocks'])->get();
                return response()->json($gadgets, 200);
            } else {
                // Use pagination for list views
                $gadgets = Gadgets::with(['category', 'brand', 'stocks'])->paginate(15);
                return response()->json($gadgets, 200);
            }
        }
        
        return view('gadgets.index', compact('gadgets'));
    }

    // Show form to create new gadget
    public function create()
    {
        $categories = \App\Models\Categories::all();
        $brands = \App\Models\Brands::all();
        
        if (request()->is('api/*') || request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'categories' => $categories,
                'brands' => $brands
            ], 200);
        }
        
        return view('gadgets.create', compact('categories', 'brands'));
    }

    // Get single gadget by ID
    public function show($id)
    {
        try {
            // Check if request wants JSON (API route or AJAX request with Accept: application/json)
            $wantsJson = request()->is('api/*') || 
                        request()->expectsJson() || 
                        request()->wantsJson() ||
                        request()->header('Accept') === 'application/json' ||
                        request()->ajax();
            
            // Use repository to get gadget with relationships
            $gadget = $this->repository->findWithRelations($id, ['category', 'brand', 'stocks.supplier']);
            
            if (!$gadget) {
                if ($wantsJson) {
                    return response()->json(['message' => 'Gadget not found'], 404);
                }
                return redirect('/gadgets')->with('error', 'Gadget not found');
            }
            
            if ($wantsJson) {
                // Convert prices to PHP for JSON response
                $gadgetData = $gadget->toArray();
                if (isset($gadgetData['stocks']) && is_array($gadgetData['stocks'])) {
                    foreach ($gadgetData['stocks'] as &$stock) {
                        if (isset($stock['CostPrice'])) {
                            $stock['CostPrice'] = CurrencyHelper::getPhpPrice($stock['CostPrice']);
                        }
                    }
                }
                return response()->json($gadgetData, 200);
            }
            
            return view('gadgets.show', compact('gadget'));
        } catch (\Exception $e) {
            \Log::error('Gadgets show error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $wantsJson = request()->is('api/*') || 
                        request()->expectsJson() || 
                        request()->wantsJson() ||
                        request()->header('Accept') === 'application/json' ||
                        request()->ajax();
            
            if ($wantsJson) {
                return response()->json([
                    'error' => 'Failed to load gadget',
                    'message' => $e->getMessage()
                ], 500);
            }
            
            return redirect('/gadgets')->with('error', 'Failed to load gadget. Please try again.');
        }
    }

    // Show form to edit gadget
    public function edit($id)
    {
        try {
            $gadget = $this->repository->findWithRelations($id, ['category', 'brand']);
            
            if (!$gadget) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => 'Gadget not found'], 404);
                }
                return redirect('/gadgets')->with('error', 'Gadget not found');
            }
            
            $categories = \App\Models\Categories::all();
            $brands = \App\Models\Brands::all();
            
            if (request()->is('api/*')) {
                return response()->json([
                    'gadget' => $gadget,
                    'categories' => $categories,
                    'brands' => $brands
                ], 200);
            }
            
            return view('gadgets.edit', compact('gadget', 'categories', 'brands'));
        } catch (\Exception $e) {
            \Log::error('Gadgets edit error: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json(['error' => 'Failed to load edit form'], 500);
            }
            
            return redirect('/gadgets')->with('error', 'Failed to load edit form. Please try again.');
        }
    }


    // Create new gadget
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'GadgetName' => 'required|string|max:100',
                'CategoryID' => 'required|exists:categories,CategoryID',
                'BrandID' => 'required|exists:brands,BrandID',
                'ReorderPoint' => 'nullable|integer|min:0',
            ]);

            DB::beginTransaction();
            try {
                $gadget = $this->repository->create($validated);
            
            // Don't create default stock entry - stock will be created when:
            // 1. Stock is added via "Add Stock" button
            // 2. Purchase order is received
            // 3. Stock adjustment is made
            // This keeps the stocks list clean and only shows actual inventory
                
                DB::commit();
            
            if (request()->is('api/*')) {
                return response()->json($gadget, 201);
            }
            
                return redirect('/gadgets')->with('success', 'Gadget created successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->is('api/*')) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Gadgets store error: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json(['error' => 'Failed to create gadget'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create gadget. Please try again.')->withInput();
        }
    }

    // Create default stock entry for new gadget
    private function createDefaultStock($gadget)
    {
        try {
            // Check if stock already exists for this gadget
            $existingStock = Stocks::where('GadgetID', $gadget->GadgetID)->first();
            if ($existingStock) {
                \Log::info('Stock already exists for gadget:', [
                    'gadget_id' => $gadget->GadgetID,
                    'existing_stock_id' => $existingStock->StockID
                ]);
                return;
            }
            
            // Create default stock entry WITHOUT a supplier
            // Supplier will be assigned when stock is actually added/ordered
            // Use DB::table to ensure no model defaults interfere
            $stockId = DB::table('stocks')->insertGetId([
                'GadgetID' => $gadget->GadgetID,
                'SupplierID' => null, // Explicitly null - no default supplier
                'QuantityAdded' => 0, // Start with 0 stock - needs to be restocked
                'CostPrice' => 0.00, // Default cost price
                'PurchaseDate' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Get the created stock for transaction record
            $stock = Stocks::find($stockId);
            
            // Create transaction record for the initial stock entry
            // Get current logged-in admin from session
            $currentAdminId = session('user_id');
            Transactions::create([
                'GadgetID' => $gadget->GadgetID,
                'AdminID' => $currentAdminId, // Set AdminID to current logged-in admin
                'StockID' => $stock->StockID,
                'TransactionType' => 'IN',
                'Quantity' => 0, // Initial quantity is 0
                'TransactionDate' => now()
            ]);
            
            \Log::info('Default stock created for gadget:', [
                'gadget_id' => $gadget->GadgetID,
                'gadget_name' => $gadget->GadgetName,
                'stock_id' => $stock->StockID
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create default stock for gadget:', [
                'gadget_id' => $gadget->GadgetID,
                'error' => $e->getMessage()
            ]);
        }
    }


    // Update gadget
    public function update(Request $request, $id)
    {
        try {
            $gadget = $this->repository->findWithRelations($id);
            
            if (!$gadget) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => 'Gadget not found'], 404);
                }
                return redirect('/gadgets')->with('error', 'Gadget not found');
            }

            $validated = $request->validate([
                'GadgetName' => 'sometimes|string|max:100',
                'CategoryID' => 'sometimes|exists:categories,CategoryID',
                'BrandID' => 'sometimes|exists:brands,BrandID',
                'ReorderPoint' => 'nullable|integer|min:0',
            ]);

            $this->repository->update($id, $validated);
            
            if (request()->is('api/*')) {
                return response()->json($gadget, 200);
            }
            
            // Redirect to gadgets index (not dashboard)
            return redirect('/gadgets')->with('success', 'Gadget updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->is('api/*')) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Gadgets update error: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json(['error' => 'Failed to update gadget'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update gadget. Please try again.')->withInput();
        }
    }

    // Delete gadget
    public function destroy($id)
    {
        try {
            $gadget = $this->repository->findWithRelations($id);
            
            if (!$gadget) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => 'Gadget not found'], 404);
                }
                return redirect('/gadgets')->with('error', 'Gadget not found');
            }

            $this->repository->delete($id);
            
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Gadget deleted successfully'], 200);
            }
            
            return redirect('/gadgets')->with('success', 'Gadget deleted successfully! You can restore it from the deleted gadgets page.');
        } catch (\Exception $e) {
            \Log::error('Gadgets destroy error: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json(['error' => 'Failed to delete gadget'], 500);
            }
            
            return redirect('/gadgets')->with('error', 'Failed to delete gadget. Please try again.');
        }
    }

    // Restore deleted gadget
    public function restore($id)
    {
        try {
            if (!$this->repository->restore($id)) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => 'Deleted gadget not found or not deleted'], 404);
                }
                return redirect('/gadgets/deleted/list')->with('error', 'Deleted gadget not found or not deleted');
            }
            
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Gadget restored successfully'], 200);
            }
            
            return redirect('/gadgets')->with('success', 'Gadget restored successfully!');
        } catch (\Exception $e) {
            \Log::error('Gadgets restore error: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json(['error' => 'Failed to restore gadget'], 500);
            }
            
            return redirect('/gadgets/deleted/list')->with('error', 'Failed to restore gadget. Please try again.');
        }
    }

    // Permanently delete gadget
    public function permanentDelete($id)
    {
        try {
            $gadget = Gadgets::withTrashed()->find($id);
            
            if (!$gadget || !$gadget->trashed()) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => 'Gadget not found or not deleted'], 404);
                }
                return redirect('/gadgets/deleted/list')->with('error', 'Gadget not found or not deleted');
            }

            $gadgetName = $gadget->GadgetName;
            $this->repository->forceDelete($id);
            
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Gadget permanently deleted'], 200);
            }
            
            return redirect('/gadgets/deleted/list')->with('success', "Gadget '{$gadgetName}' has been permanently deleted and cannot be restored.");
        } catch (\Exception $e) {
            \Log::error('Gadgets permanent delete error: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json(['error' => 'Failed to permanently delete gadget'], 500);
            }
            
            return redirect('/gadgets/deleted/list')->with('error', 'Failed to permanently delete gadget. Please try again.');
        }
    }

    // View deleted gadgets
    public function deleted()
    {
        try {
            $deletedGadgets = $this->repository->getDeleted(['category', 'brand']);
            
            if (request()->is('api/*')) {
                return response()->json($deletedGadgets, 200);
            }
            
            return view('gadgets.deleted', compact('deletedGadgets'));
        } catch (\Exception $e) {
            \Log::error('Gadgets deleted view error: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json(['error' => 'Failed to load deleted gadgets'], 500);
            }
            
            return redirect('/gadgets')->with('error', 'Failed to load deleted gadgets.');
        }
    }

    // Get top-purchased gadgets (by quantity and cost)
    public function getTopSelling()
    {
        $gadgets = \DB::table('transactions')
            ->join('gadgets', 'transactions.GadgetID', '=', 'gadgets.GadgetID')
            ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
            ->select(
                'gadgets.GadgetID',
                'gadgets.GadgetName',
                \DB::raw('SUM(transactions.Quantity) as total_purchased'),
                \DB::raw('SUM(transactions.Quantity * stocks.CostPrice) as total_cost')
            )
            ->where('transactions.TransactionType', 'IN')
            ->groupBy('gadgets.GadgetID', 'gadgets.GadgetName')
            ->orderByDesc('total_purchased')
            ->limit(5)
            ->get();

        return response()->json([
            'message' => 'Top-purchased gadgets retrieved successfully.',
            'data' => $gadgets
        ], 200);
    }

}
