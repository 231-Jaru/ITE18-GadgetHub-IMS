<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Stocks;
use App\Models\Gadgets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StockAdjustmentsController extends Controller
{
    /**
     * Display a listing of stock adjustments.
     */
    public function index()
    {
        try {
            $adjustments = StockAdjustment::with(['gadget.category', 'stock', 'admin'])
                ->orderBy('AdjustmentDate', 'desc')
                ->paginate(15);

        if (request()->is('api/*')) {
            return response()->json($adjustments, 200);
        }

        return view('stock-adjustments.index', compact('adjustments'));
        } catch (\Exception $e) {
            \Log::error('Stock Adjustments Index Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('stock-adjustments.index', ['adjustments' => collect()])
                ->with('error', 'Error loading stock adjustments: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new stock adjustment.
     */
    public function create()
    {
        // Only get non-deleted gadgets (soft deletes are excluded by default)
        $gadgets = Gadgets::with(['category', 'brand', 'stocks'])
            ->whereNull('deleted_at')
            ->get();
        
        // Handle gadget parameter from URL (e.g., ?gadget=123)
        $selectedGadget = null;
        if (request()->has('gadget')) {
            $selectedGadget = request()->get('gadget');
            // Validate that the gadget exists and is not deleted
            $gadgetExists = Gadgets::whereNull('deleted_at')
                ->find($selectedGadget);
            if (!$gadgetExists) {
                $selectedGadget = null; // Reset if invalid
            }
        }
        
        return view('stock-adjustments.create', compact('gadgets', 'selectedGadget'));
    }

    /**
     * Store a newly created stock adjustment.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'GadgetID' => [
                    'required',
                    Rule::exists('gadgets', 'GadgetID')->whereNull('deleted_at')
                ],
                'StockID' => 'nullable|exists:stocks,StockID',
                'AdjustmentType' => 'required|in:INCREASE,DECREASE,SET',
                'QuantityChanged' => 'required|integer|min:0',
                'Reason' => 'required|string|max:255',
                'Notes' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        }

        // Only get non-deleted gadgets
        $gadget = Gadgets::with('stocks')
            ->whereNull('deleted_at')
            ->find($validated['GadgetID']);
        if (!$gadget) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Gadget not found or has been deleted'], 404);
            }
            return back()->withErrors(['GadgetID' => 'Gadget not found or has been deleted'])->withInput();
        }
        
        // Reload stocks to ensure we have fresh data
        $gadget->load('stocks');
        $totalStock = $gadget->stocks->sum('QuantityAdded');
        
        // Get admin ID from authenticated user (for API) or session (for web)
        if (request()->is('api/*')) {
            // For API requests with Bearer token authentication (Sanctum)
            // The route is protected by auth:sanctum middleware, so user should be available
            $user = $request->user();
            $adminId = $user ? ($user->AdminID ?? $user->id ?? null) : null;
        } else {
            // For web requests with session authentication
            $adminId = session('user_id') ?? session('admin_id') ?? null;
        }
        
        $validated['AdminID'] = $adminId;
        $validated['QuantityBefore'] = $totalStock;
        $validated['AdjustmentDate'] = now();

        // Calculate new quantity based on adjustment type
        if ($validated['AdjustmentType'] === 'INCREASE') {
            // Validate minimum quantity for increase
            if ($validated['QuantityChanged'] < 1) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => 'Quantity to add must be at least 1.'], 422);
                }
                return back()->withErrors([
                    'QuantityChanged' => 'Quantity to add must be at least 1.'
                ])->withInput();
            }
            $validated['QuantityAfter'] = $totalStock + $validated['QuantityChanged'];
            $validated['QuantityChanged'] = $validated['QuantityChanged']; // Keep positive
        } elseif ($validated['AdjustmentType'] === 'DECREASE') {
            // Validate minimum quantity for decrease
            if ($validated['QuantityChanged'] < 1) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => 'Quantity to remove must be at least 1.'], 422);
                }
                return back()->withErrors([
                    'QuantityChanged' => 'Quantity to remove must be at least 1.'
                ])->withInput();
            }
            // Validate that we're not decreasing more than available
            if ($validated['QuantityChanged'] > $totalStock) {
                if (request()->is('api/*')) {
                    return response()->json(['message' => "Cannot decrease more than current stock ({$totalStock} units available)."], 422);
                }
                return back()->withErrors([
                    'QuantityChanged' => "Cannot decrease more than current stock ({$totalStock} units available)."
                ])->withInput();
            }
            $validated['QuantityAfter'] = max(0, $totalStock - $validated['QuantityChanged']);
            $validated['QuantityChanged'] = -$validated['QuantityChanged']; // Make negative
        } else { // SET
            // For SET, QuantityChanged represents the final quantity
            $finalQuantity = $validated['QuantityChanged'];
            $validated['QuantityAfter'] = $finalQuantity;
            $validated['QuantityChanged'] = $finalQuantity - $totalStock; // Calculate the change
        }

        DB::beginTransaction();
        try {
            $adjustment = StockAdjustment::create($validated);

            // Update stock quantity
            if (isset($validated['StockID']) && !empty($validated['StockID'])) {
                // Adjust specific stock
                $stock = Stocks::find($validated['StockID']);
                if (!$stock) {
                    throw new \Exception('Selected stock not found');
                }
                
                    $currentQty = $stock->QuantityAdded;
                    if ($validated['AdjustmentType'] === 'INCREASE') {
                    // QuantityChanged is positive for INCREASE
                    $stock->QuantityAdded = $currentQty + $validated['QuantityChanged'];
                    } elseif ($validated['AdjustmentType'] === 'DECREASE') {
                    // QuantityChanged is negative for DECREASE, so we use abs()
                        $stock->QuantityAdded = max(0, $currentQty - abs($validated['QuantityChanged']));
                    } else { // SET
                    // For SET, adjust this stock to reach the target total
                    // Get other stocks total for this gadget
                    $otherStocksTotal = Stocks::where('GadgetID', $gadget->GadgetID)
                        ->where('StockID', '!=', $stock->StockID)
                        ->sum('QuantityAdded');
                    $stock->QuantityAdded = max(0, $validated['QuantityAfter'] - $otherStocksTotal);
                    }
                    $stock->save();
            } else {
                // If no specific stock, adjust the first available stock or create new
                // Reload to get fresh data
                $gadget->refresh();
                $gadget->load('stocks');
                $stock = $gadget->stocks->first();
                
                if (!$stock) {
                    // Create a new stock entry
                    $stock = Stocks::create([
                        'GadgetID' => $gadget->GadgetID,
                        'QuantityAdded' => $validated['QuantityAfter'],
                        'CostPrice' => 0,
                        'PurchaseDate' => now(),
                    ]);
                } else {
                    $currentQty = $stock->QuantityAdded;
                    
                    if ($validated['AdjustmentType'] === 'INCREASE') {
                        // QuantityChanged is positive for INCREASE
                        $stock->QuantityAdded = $currentQty + $validated['QuantityChanged'];
                    } elseif ($validated['AdjustmentType'] === 'DECREASE') {
                        // QuantityChanged is negative for DECREASE, so we use abs()
                        $stock->QuantityAdded = max(0, $currentQty - abs($validated['QuantityChanged']));
                    } else { // SET
                        // For SET, adjust this stock to reach the target total
                        // Get other stocks total for this gadget
                        $otherStocksTotal = Stocks::where('GadgetID', $gadget->GadgetID)
                            ->where('StockID', '!=', $stock->StockID)
                            ->sum('QuantityAdded');
                        $stock->QuantityAdded = max(0, $validated['QuantityAfter'] - $otherStocksTotal);
                    }
                    $stock->save();
                }
                
                // Update the adjustment with the StockID
                $adjustment->StockID = $stock->StockID;
                $adjustment->save();
            }

            DB::commit();

            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Stock adjustment created successfully',
                    'adjustment' => $adjustment
                ], 201);
            }

            return redirect('/stock-adjustments')
                ->with('success', 'Stock adjustment created successfully!')
                ->with('adjustment_id', $adjustment->AdjustmentID);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Stock Adjustment Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Failed to create stock adjustment',
                    'error' => $e->getMessage(),
                    'details' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ] : null
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to create stock adjustment: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show($id)
    {
        $adjustment = StockAdjustment::with(['gadget', 'stock', 'admin'])->find($id);

        if (!$adjustment) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Stock adjustment not found'], 404);
            }
            return redirect('/stock-adjustments')->with('error', 'Stock adjustment not found');
        }

        if (request()->is('api/*')) {
            return response()->json($adjustment, 200);
        }

        return view('stock-adjustments.show', compact('adjustment'));
    }

    /**
     * Remove the specified stock adjustment.
     */
    public function destroy($id)
    {
        $adjustment = StockAdjustment::find($id);

        if (!$adjustment) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Stock adjustment not found'], 404);
            }
            return redirect('/stock-adjustments')->with('error', 'Stock adjustment not found');
        }

        // Authorization check: Only allow delete if user created the adjustment or is admin
        $currentAdminId = session('user_id');
        if ($adjustment->AdminID && $adjustment->AdminID != $currentAdminId) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Unauthorized. You can only delete adjustments you created.'], 403);
            }
            return redirect('/stock-adjustments')->with('error', 'Unauthorized. You can only delete adjustments you created.');
        }

        $adjustment->delete();

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Stock adjustment deleted successfully'], 200);
        }

        return redirect('/stock-adjustments')
            ->with('success', 'Stock adjustment deleted successfully!');
    }
}
