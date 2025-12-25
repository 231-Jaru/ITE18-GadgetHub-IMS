<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use App\Models\Gadgets;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransactionsController extends Controller
{
    // Display all transactions with relationships.
    public function index()
    {
        $transactions = Transactions::with([
            'admin',
            'stock',
            'gadget'
        ])->where('TransactionType', 'IN')->orderBy('TransactionDate', 'desc')->paginate(15);
        
        // Only return JSON for actual API routes (starts with /api/)
        if (request()->is('api/*')) {
            return response()->json($transactions, 200);
        }
        
        return view('transactions.index', compact('transactions'));
    }


    // Display a single transaction by ID.
    public function show($id)
    {
        $transaction = Transactions::with([
            'admin',
            'stock',
            'gadget'
        ])->where('TransactionType', 'IN')->find($id);

        if (!$transaction) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }
            return redirect('/transactions')->with('error', 'Transaction not found');
        }

        if (request()->is('api/*')) {
            return response()->json($transaction, 200);
        }

        return view('transactions.show', compact('transaction'));
    }

    // Show create form
    public function create()
    {
        $gadgets = \App\Models\Gadgets::with(['category', 'brand'])->get();
        $admins = \App\Models\Admins::all();
        $stocks = \App\Models\Stocks::with(['gadget'])->get();
        return view('transactions.create', compact('gadgets', 'admins', 'stocks'));
    }

    // Store a newly created transaction (IN type only - inventory restock).
    public function store(Request $request)
    {
        // Get current logged-in admin from session
        $currentAdminId = session('user_id');
        
        $validated = $request->validate([
            'GadgetID'        => [
                'required',
                Rule::exists('gadgets', 'GadgetID')->whereNull('deleted_at')
            ],
            'AdminID'         => 'nullable|exists:admins,AdminID',
            'StockID'         => 'required|exists:stocks,StockID',
            'TransactionType' => 'required|in:IN',
            'Quantity'        => 'required|integer|min:1',
        ]);

        // Set AdminID to current logged-in admin if not provided
        if (empty($validated['AdminID'])) {
            $validated['AdminID'] = $currentAdminId;
        }

        // Force TransactionType to IN (inventory management only)
        $validated['TransactionType'] = 'IN';
        $validated['TransactionDate'] = now();

        DB::beginTransaction();
        try {
            // Create transaction record
        $transaction = Transactions::create($validated);

            // Update stock quantity to reflect the transaction
            $stock = Stocks::find($validated['StockID']);
            if ($stock) {
                $stock->QuantityAdded += $validated['Quantity'];
                $stock->save();
            }

            DB::commit();

        if (request()->is('api/*')) {
            return response()->json([
                'message'     => 'Transaction created successfully!',
                'transaction' => $transaction
            ], 201);
        }

            return redirect('/transactions')->with('success', 'Transaction created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction creation failed: ' . $e->getMessage());
            
            if (request()->is('api/*')) {
                return response()->json([
                    'error' => 'Failed to create transaction: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create transaction. Please try again.')->withInput();
        }
    }

    // Show edit form
    public function edit($id)
    {
        $transaction = Transactions::where('TransactionType', 'IN')->find($id);
        
        if (!$transaction) {
            return redirect('/transactions')->with('error', 'Transaction not found');
        }
        
        $gadgets = \App\Models\Gadgets::with(['category', 'brand'])->get();
        $admins = \App\Models\Admins::all();
        $stocks = \App\Models\Stocks::with(['gadget'])->get();
        return view('transactions.edit', compact('transaction', 'gadgets', 'admins', 'stocks'));
    }

    // Update an existing transaction.
    public function update(Request $request, $id)
    {
        $transaction = Transactions::where('TransactionType', 'IN')->find($id);

        if (!$transaction) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }
            return redirect('/transactions')->with('error', 'Transaction not found');
        }

        // Authorization check: Only allow update if user created the transaction or is admin
        $currentAdminId = session('user_id');
        if ($transaction->AdminID && $transaction->AdminID != $currentAdminId) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Unauthorized. You can only update transactions you created.'], 403);
            }
            return redirect('/transactions')->with('error', 'Unauthorized. You can only update transactions you created.');
        }

        $validated = $request->validate([
            'AdminID'         => 'sometimes|exists:admins,AdminID',
            'StockID'         => 'sometimes|exists:stocks,StockID',
            'TransactionType' => 'sometimes|in:IN',
            'Quantity'        => 'sometimes|integer|min:1',
            'TransactionDate' => 'sometimes|date',
        ]);

        // Force TransactionType to IN
        $validated['TransactionType'] = 'IN';
        $transaction->update($validated);

        if (request()->is('api/*')) {
            return response()->json([
                'message'     => 'Transaction updated successfully!',
                'transaction' => $transaction->load(['admin', 'stock', 'gadget'])
            ], 200);
        }

        return redirect('/transactions')->with('success', 'Transaction updated successfully!');
    }

    // Delete a transaction.
    public function destroy($id)
    {
        $transaction = Transactions::find($id);

        if (!$transaction) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }
            return redirect('/transactions')->with('error', 'Transaction not found');
        }

        // Authorization check: Only allow delete if user created the transaction or is admin
        $currentAdminId = session('user_id');
        if ($transaction->AdminID && $transaction->AdminID != $currentAdminId) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Unauthorized. You can only delete transactions you created.'], 403);
            }
            return redirect('/transactions')->with('error', 'Unauthorized. You can only delete transactions you created.');
        }

        $transaction->delete();

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Transaction deleted successfully.'], 200);
        }

        return redirect('/transactions')->with('success', 'Transaction deleted successfully!');
    }


    // Get monthly inventory purchases summary.
    public function getMonthlyPurchases()
    {
        $monthlyPurchases = DB::table('transactions')
            ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
            ->selectRaw('
                YEAR(transactions.TransactionDate) AS year,
                MONTH(transactions.TransactionDate) AS month,
                SUM(stocks.CostPrice * transactions.Quantity) AS total
            ')
            ->where('transactions.TransactionType', 'IN')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->map(function ($purchase) {
                $purchase->month_name = date('F', mktime(0, 0, 0, $purchase->month, 1));
                return $purchase;
            });

        return response()->json([
            'message' => 'Monthly inventory purchases summary retrieved successfully.',
            'data'    => $monthlyPurchases
        ]);
    }

    // Inventory Purchases Report View
    public function salesReport()
    {
        // Get monthly purchases data
        $monthlyPurchasesResponse = $this->getMonthlyPurchases();
        $monthlyPurchasesDataRaw = $monthlyPurchasesResponse->getData()->data ?? [];
        // Ensure it's a collection
        $monthlyPurchasesData = is_array($monthlyPurchasesDataRaw) 
            ? collect($monthlyPurchasesDataRaw) 
            : $monthlyPurchasesDataRaw;

        // Get total inventory value
        $totalInventoryValue = DB::table('stocks')
            ->selectRaw('SUM(CostPrice * QuantityAdded) as total')
            ->first();
        $totalValue = $totalInventoryValue->total ?? 0;

        // Get total transactions (IN only)
        $totalTransactions = Transactions::where('TransactionType', 'IN')->count();
        
        // Get recent transactions
        $recentTransactions = Transactions::with(['admin', 'gadget', 'stock'])
            ->where('TransactionType', 'IN')
            ->orderBy('TransactionDate', 'desc')
            ->limit(5)
            ->get();

        // Prepare monthly purchases data for chart (renamed to monthlySalesData for view compatibility)
        $monthlySalesData = [
            'labels' => $monthlyPurchasesData->pluck('month_name')->toArray(),
            'data' => $monthlyPurchasesData->pluck('total')->toArray()
        ];

        // Get purchases by category
        $categoryPurchases = DB::table('transactions')
            ->join('gadgets', 'transactions.GadgetID', '=', 'gadgets.GadgetID')
            ->join('categories', 'gadgets.CategoryID', '=', 'categories.CategoryID')
            ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
            ->select(
                'categories.CategoryName',
                DB::raw('SUM(stocks.CostPrice * transactions.Quantity) as total_purchases')
            )
            ->where('transactions.TransactionType', 'IN')
            ->groupBy('categories.CategoryName')
            ->orderByDesc('total_purchases')
            ->get();

        $categoryData = [
            'labels' => $categoryPurchases->pluck('CategoryName')->toArray(),
            'data' => $categoryPurchases->pluck('total_purchases')->toArray()
        ];

        // Calculate total sales (total purchase value)
        $totalSales = $totalValue;

        // Calculate average order value
        $averageOrder = $totalTransactions > 0 ? $totalValue / $totalTransactions : 0;

        // Calculate growth percentage (compare last 2 months)
        $growthPercentage = 0;
        if ($monthlyPurchasesData->count() >= 2) {
            $lastMonth = $monthlyPurchasesData->first()->total ?? 0;
            $previousMonth = $monthlyPurchasesData->skip(1)->first()->total ?? 0;
            if ($previousMonth > 0) {
                $growthPercentage = (($lastMonth - $previousMonth) / $previousMonth) * 100;
            }
        }

        // Get top purchased products (not selling, but most purchased)
        $topSellingProducts = DB::table('transactions')
            ->join('gadgets', 'transactions.GadgetID', '=', 'gadgets.GadgetID')
            ->leftJoin('categories', 'gadgets.CategoryID', '=', 'categories.CategoryID')
            ->select(
                'gadgets.GadgetID',
                'gadgets.GadgetName',
                'categories.CategoryID',
                DB::raw('SUM(transactions.Quantity) as total_sold')
            )
            ->where('transactions.TransactionType', 'IN')
            ->groupBy('gadgets.GadgetID', 'gadgets.GadgetName', 'categories.CategoryID')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $gadget = Gadgets::with('category')->find($item->GadgetID);
                $item->category = $gadget->category ?? null;
                return $item;
            });

        return view('reports.sales', compact(
            'totalSales',
            'totalValue',
            'totalTransactions', 
            'recentTransactions',
            'categoryData',
            'averageOrder',
            'growthPercentage',
            'topSellingProducts',
            'monthlySalesData'
        ));
    }

    // Get purchase report data (for Vue frontend - matches Blade view calculation)
    public function getPurchaseReportData()
    {
        // Get monthly purchases data
        $monthlyPurchasesResponse = $this->getMonthlyPurchases();
        $monthlyPurchasesDataRaw = $monthlyPurchasesResponse->getData()->data ?? [];
        // Ensure it's a collection
        $monthlyPurchasesData = is_array($monthlyPurchasesDataRaw) 
            ? collect($monthlyPurchasesDataRaw) 
            : $monthlyPurchasesDataRaw;

        // Get total inventory value (matching Blade calculation)
        $totalInventoryValue = DB::table('stocks')
            ->selectRaw('SUM(CostPrice * QuantityAdded) as total')
            ->first();
        $totalValue = $totalInventoryValue->total ?? 0;

        // Get total transactions (IN only)
        $totalTransactions = Transactions::where('TransactionType', 'IN')->count();
        
        // Get recent transactions with admin relationship
        $recentTransactions = Transactions::with(['admin', 'gadget', 'stock'])
            ->where('TransactionType', 'IN')
            ->orderBy('TransactionDate', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                // Ensure admin relationship is properly loaded
                if ($transaction->AdminID && !$transaction->relationLoaded('admin')) {
                    $transaction->load('admin');
                }
                return $transaction;
            });

        // Prepare monthly purchases data for chart (matching Blade calculation)
        $monthlySalesData = [
            'labels' => $monthlyPurchasesData->pluck('month_name')->toArray(),
            'data' => $monthlyPurchasesData->pluck('total')->toArray()
        ];

        // Get purchases by category (matching Blade calculation exactly)
        $categoryPurchases = DB::table('transactions')
            ->join('gadgets', 'transactions.GadgetID', '=', 'gadgets.GadgetID')
            ->join('categories', 'gadgets.CategoryID', '=', 'categories.CategoryID')
            ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
            ->select(
                'categories.CategoryName',
                DB::raw('SUM(stocks.CostPrice * transactions.Quantity) as total_purchases')
            )
            ->where('transactions.TransactionType', 'IN')
            ->groupBy('categories.CategoryName')
            ->orderByDesc('total_purchases')
            ->get();

        $categoryData = [
            'labels' => $categoryPurchases->pluck('CategoryName')->toArray(),
            'data' => $categoryPurchases->pluck('total_purchases')->toArray()
        ];

        // Calculate total purchase value (matching Blade)
        $totalPurchaseValue = $totalValue;

        // Calculate average order value (matching Blade)
        $averageOrder = $totalTransactions > 0 ? $totalValue / $totalTransactions : 0;

        // Calculate growth percentage (compare last 2 months) - matching Blade
        $growthPercentage = 0;
        if ($monthlyPurchasesData->count() >= 2) {
            $lastMonth = $monthlyPurchasesData->first()->total ?? 0;
            $previousMonth = $monthlyPurchasesData->skip(1)->first()->total ?? 0;
            if ($previousMonth > 0) {
                $growthPercentage = (($lastMonth - $previousMonth) / $previousMonth) * 100;
            }
        }

        // Get top purchased products (matching Blade calculation)
        $topPurchasedProducts = DB::table('transactions')
            ->join('gadgets', 'transactions.GadgetID', '=', 'gadgets.GadgetID')
            ->leftJoin('categories', 'gadgets.CategoryID', '=', 'categories.CategoryID')
            ->select(
                'gadgets.GadgetID',
                'gadgets.GadgetName',
                'categories.CategoryID',
                DB::raw('SUM(transactions.Quantity) as total_sold')
            )
            ->where('transactions.TransactionType', 'IN')
            ->groupBy('gadgets.GadgetID', 'gadgets.GadgetName', 'categories.CategoryID')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $gadget = Gadgets::with('category')->find($item->GadgetID);
                $item->category = $gadget->category ?? null;
                return $item;
            });

        // Ensure admin relationship is properly serialized for recent transactions
        $recentTransactionsFormatted = $recentTransactions->map(function ($transaction) {
            $transactionArray = $transaction->toArray();
            // Ensure admin data is included
            if ($transaction->admin) {
                $transactionArray['admin'] = [
                    'AdminID' => $transaction->admin->AdminID,
                    'Username' => $transaction->admin->Username,
                ];
            }
            return $transactionArray;
            });

        return response()->json([
            'totalPurchaseValue' => $totalPurchaseValue,
            'totalTransactions' => $totalTransactions,
            'averageOrder' => $averageOrder,
            'growthPercentage' => $growthPercentage,
            'monthlyPurchases' => $monthlyPurchasesData,
            'monthlyChartData' => $monthlySalesData,
            'categoryPurchases' => $categoryPurchases,
            'categoryChartData' => $categoryData,
            'topPurchasedProducts' => $topPurchasedProducts,
            'recentTransactions' => $recentTransactionsFormatted
        ], 200);
    }
}
