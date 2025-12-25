<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gadgets;
use App\Models\Stocks;
use App\Models\Transactions;
use App\Models\Suppliers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Clear cache if stock was just added (indicated by session message)
        // Wrap in try-catch to handle cases where cache table doesn't exist
        if (session('success') && str_contains(session('success'), 'Stock added')) {
            try {
                Cache::forget('dashboard_stats');
                Cache::forget('dashboard_low_stock');
                Cache::forget('dashboard_recent_transactions');
            } catch (\Exception $cacheException) {
                // If cache clearing fails (e.g., cache table doesn't exist), just log and continue
                \Log::warning('Cache clear failed (non-critical): ' . $cacheException->getMessage());
            }
        }
        
        try {
            // Get dashboard statistics - try cache first, fallback to direct query
            try {
                $stats = Cache::remember('dashboard_stats', 300, function () {
                    return [
                        'total_gadgets' => Gadgets::count() ?? 0,
                        'total_stocks' => Stocks::count() ?? 0,
                        'total_transactions' => Transactions::where('TransactionType', 'IN')->count() ?? 0,
                        'total_suppliers' => Suppliers::count() ?? 0,
                    ];
                });
            } catch (\Exception $cacheException) {
                // If cache fails, get data directly
                \Log::warning('Dashboard cache failed, using direct query: ' . $cacheException->getMessage());
                $stats = [
                    'total_gadgets' => Gadgets::count() ?? 0,
                    'total_stocks' => Stocks::count() ?? 0,
                    'total_transactions' => Transactions::where('TransactionType', 'IN')->count() ?? 0,
                    'total_suppliers' => Suppliers::count() ?? 0,
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard stats error: ' . $e->getMessage());
            // Fallback to default values if database query fails
            $stats = [
                'total_gadgets' => 0,
                'total_stocks' => 0,
                'total_transactions' => 0,
                'total_suppliers' => 0,
            ];
        }

        try {
            // Get low stock items - try cache first, fallback to direct query
            try {
                $lowStockItems = Cache::remember('dashboard_low_stock', 120, function () {
                    return Stocks::with('gadget')
                        ->where('QuantityAdded', '<', 10)
                        ->whereHas('gadget') // Only include stocks with existing (non-deleted) gadgets
                        ->orderBy('QuantityAdded', 'asc')
                        ->limit(5)
                        ->get()
                        ->filter(function($stock) {
                            // Additional safety check to ensure gadget exists and is not null
                            return $stock->gadget !== null;
                        })
                        ->values(); // Re-index the collection
                });
            } catch (\Exception $cacheException) {
                \Log::warning('Dashboard low stock cache failed, using direct query: ' . $cacheException->getMessage());
                $lowStockItems = Stocks::with('gadget')
                    ->where('QuantityAdded', '<', 10)
                    ->whereHas('gadget')
                    ->orderBy('QuantityAdded', 'asc')
                    ->limit(5)
                    ->get()
                    ->filter(function($stock) {
                        return $stock->gadget !== null;
                    })
                    ->values();
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard Low Stock Items Error: ' . $e->getMessage());
            $lowStockItems = collect();
        }

        try {
            // Get recent transactions - try cache first, fallback to direct query
            try {
                $recentTransactions = Cache::remember('dashboard_recent_transactions', 60, function () {
                    return Transactions::with(['admin', 'gadget', 'stock'])
                        ->where('TransactionType', 'IN')
                        ->orderBy('TransactionDate', 'desc')
                        ->limit(5)
                        ->get()
                        ->map(function ($transaction) {
                            // For IN transactions, use cost price
                            if ($transaction->stock) {
                                $transaction->TotalAmount = ($transaction->Quantity ?? 0) * ($transaction->stock->CostPrice ?? 0);
                            } else {
                                $transaction->TotalAmount = 0;
                            }
                            return $transaction;
                        });
                });
            } catch (\Exception $cacheException) {
                \Log::warning('Dashboard recent transactions cache failed, using direct query: ' . $cacheException->getMessage());
                $recentTransactions = Transactions::with(['admin', 'gadget', 'stock'])
                    ->where('TransactionType', 'IN')
                    ->orderBy('TransactionDate', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($transaction) {
                        if ($transaction->stock) {
                            $transaction->TotalAmount = ($transaction->Quantity ?? 0) * ($transaction->stock->CostPrice ?? 0);
                        } else {
                            $transaction->TotalAmount = 0;
                        }
                        return $transaction;
                    });
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard Recent Transactions Error: ' . $e->getMessage());
            $recentTransactions = collect();
        }

        try {
            // Get top purchased gadgets - try cache first, fallback to direct query
            try {
                $topPurchasedGadgets = Cache::remember('dashboard_top_purchased', 300, function () {
                    return DB::table('transactions')
                        ->join('gadgets', function($join) {
                            $join->on('transactions.GadgetID', '=', 'gadgets.GadgetID')
                                 ->whereNull('gadgets.deleted_at'); // Exclude soft-deleted gadgets
                        })
                        ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
                        ->select(
                            'gadgets.GadgetID',
                            'gadgets.GadgetName',
                            DB::raw('SUM(transactions.Quantity) as total_purchased'),
                            DB::raw('SUM(transactions.Quantity * stocks.CostPrice) as total_cost')
                        )
                        ->where('transactions.TransactionType', 'IN')
                        ->groupBy('gadgets.GadgetID', 'gadgets.GadgetName')
                        ->orderByDesc('total_purchased')
                        ->limit(5)
                        ->get();
                });
            } catch (\Exception $cacheException) {
                \Log::warning('Dashboard top purchased cache failed, using direct query: ' . $cacheException->getMessage());
                $topPurchasedGadgets = DB::table('transactions')
                    ->join('gadgets', function($join) {
                        $join->on('transactions.GadgetID', '=', 'gadgets.GadgetID')
                             ->whereNull('gadgets.deleted_at');
                    })
                    ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
                    ->select(
                        'gadgets.GadgetID',
                        'gadgets.GadgetName',
                        DB::raw('SUM(transactions.Quantity) as total_purchased'),
                        DB::raw('SUM(transactions.Quantity * stocks.CostPrice) as total_cost')
                    )
                    ->where('transactions.TransactionType', 'IN')
                    ->groupBy('gadgets.GadgetID', 'gadgets.GadgetName')
                    ->orderByDesc('total_purchased')
                    ->limit(5)
                    ->get();
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard top purchased error: ' . $e->getMessage());
            $topPurchasedGadgets = collect();
        }

        try {
            // Get monthly inventory purchases data - try cache first, fallback to direct query
            try {
                $monthlyPurchases = Cache::remember('dashboard_monthly_purchases', 600, function () {
                    return DB::table('transactions')
                        ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
                        ->selectRaw('
                            YEAR(transactions.TransactionDate) AS year,
                            MONTH(transactions.TransactionDate) AS month,
                            SUM(transactions.Quantity * stocks.CostPrice) AS total
                        ')
                        ->where('transactions.TransactionType', 'IN')
                        ->where('transactions.TransactionDate', '>=', now()->subMonths(6))
                        ->groupBy('year', 'month')
                        ->orderByDesc('year')
                        ->orderByDesc('month')
                        ->get()
                        ->map(function ($purchase) {
                            $purchase->month_name = date('F', mktime(0, 0, 0, $purchase->month, 1));
                            return $purchase;
                        });
                });
            } catch (\Exception $cacheException) {
                \Log::warning('Dashboard monthly purchases cache failed, using direct query: ' . $cacheException->getMessage());
                $monthlyPurchases = DB::table('transactions')
                    ->join('stocks', 'transactions.StockID', '=', 'stocks.StockID')
                    ->selectRaw('
                        YEAR(transactions.TransactionDate) AS year,
                        MONTH(transactions.TransactionDate) AS month,
                        SUM(transactions.Quantity * stocks.CostPrice) AS total
                    ')
                    ->where('transactions.TransactionType', 'IN')
                    ->where('transactions.TransactionDate', '>=', now()->subMonths(6))
                    ->groupBy('year', 'month')
                    ->orderByDesc('year')
                    ->orderByDesc('month')
                    ->get()
                    ->map(function ($purchase) {
                        $purchase->month_name = date('F', mktime(0, 0, 0, $purchase->month, 1));
                        return $purchase;
                    });
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard monthly purchases error: ' . $e->getMessage());
            $monthlyPurchases = collect();
        }

        try {
            // Get suppliers for adding stock
            $suppliers = Suppliers::all();
        } catch (\Exception $e) {
            $suppliers = collect();
        }

        // If this is an API request, return JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'stats' => $stats,
                    'low_stock_items' => $lowStockItems->map(function($item) {
                        return [
                            'StockID' => $item->StockID,
                            'QuantityAdded' => $item->QuantityAdded,
                            'gadget' => $item->gadget ? [
                                'GadgetID' => $item->gadget->GadgetID,
                                'GadgetName' => $item->gadget->GadgetName,
                            ] : null
                        ];
                    }),
                    'recent_transactions' => $recentTransactions->map(function($transaction) {
                        return [
                            'TransactionID' => $transaction->TransactionID,
                            'TransactionDate' => $transaction->TransactionDate,
                            'Quantity' => $transaction->Quantity,
                            'TotalAmount' => $transaction->TotalAmount,
                            'admin' => $transaction->admin ? [
                                'Username' => $transaction->admin->Username,
                            ] : null,
                            'gadget' => $transaction->gadget ? [
                                'GadgetID' => $transaction->gadget->GadgetID,
                                'GadgetName' => $transaction->gadget->GadgetName,
                            ] : null
                        ];
                    }),
                    'top_purchased_gadgets' => $topPurchasedGadgets->map(function($gadget) {
                        return [
                            'GadgetID' => $gadget->GadgetID,
                            'GadgetName' => $gadget->GadgetName,
                            'total_purchased' => $gadget->total_purchased,
                            'total_cost' => $gadget->total_cost,
                        ];
                    }),
                    'monthly_purchases' => $monthlyPurchases->map(function($purchase) {
                        return [
                            'year' => $purchase->year,
                            'month' => $purchase->month,
                            'month_name' => $purchase->month_name,
                            'total' => $purchase->total,
                        ];
                    }),
                ]
            ]);
        }

        return view('dashboard', compact(
            'stats',
            'lowStockItems',
            'recentTransactions',
            'topPurchasedGadgets',
            'monthlyPurchases',
            'suppliers'
        ));
    }
}
