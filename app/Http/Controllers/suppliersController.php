<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuppliersController extends Controller
{
    // Get all suppliers with their stocks
    public function index()
    {
        // Load suppliers with stocks and their associated gadgets (with category and brand)
        $suppliers = Suppliers::with(['stocks.gadget.category', 'stocks.gadget.brand'])->get();
        
        // Only return JSON for actual API routes (starts with /api/)
        if (request()->is('api/*')) {
            return response()->json($suppliers, 200);
        }
        
        return view('suppliers.index', compact('suppliers'));
    }


    // Get single supplier by ID
    public function show($id)
    {
        // Load supplier with stocks and their associated gadgets (with category and brand)
        $supplier = Suppliers::with(['stocks.gadget.category', 'stocks.gadget.brand'])->find($id);
        
        if (!$supplier) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Supplier not found'], 404);
            }
            return redirect('/suppliers')->with('error', 'Supplier not found');
        }
        
        if (request()->is('api/*')) {
            return response()->json($supplier, 200);
        }
        
        return view('suppliers.show', compact('supplier'));
    }

    // Show create form
    public function create()
    {
        return view('suppliers.create');
    }

    // Create new supplier
    public function store(Request $request)
    {
        $validated = $request->validate([
            'SupplierName' => 'required|string|max:100',
            'ContactPerson' => 'nullable|string|max:100',
            'Phone' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100|unique:suppliers,Email',
        ]);

        $supplier = Suppliers::create($validated);

        if (request()->is('api/*')) {
            return response()->json($supplier, 201);
        }

        return redirect('/suppliers')->with('success', 'Supplier created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $supplier = Suppliers::find($id);
        
        if (!$supplier) {
            return redirect('/suppliers')->with('error', 'Supplier not found');
        }
        
        return view('suppliers.edit', compact('supplier'));
    }

    // Update supplier
    public function update(Request $request, $id)
    {
        $supplier = Suppliers::find($id);
        
        if (!$supplier) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Supplier not found'], 404);
            }
            return redirect('/suppliers')->with('error', 'Supplier not found');
        }

        $validated = $request->validate([
            'SupplierName' => 'required|string|max:100',
            'ContactPerson' => 'nullable|string|max:100',
            'Phone' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100|unique:suppliers,Email,' . $id . ',SupplierID',
        ]);

        $supplier->update($validated);

        if (request()->is('api/*')) {
            return response()->json($supplier, 200);
        }

        return redirect('/suppliers')->with('success', 'Supplier updated successfully!');
    }

    // Delete supplier
    public function destroy($id)
    {
        $supplier = Suppliers::find($id);
        
        if (!$supplier) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Supplier not found'], 404);
            }
            return redirect('/suppliers')->with('error', 'Supplier not found');
        }

        $supplier->delete();
        
        if (request()->is('api/*')) {
            return response()->json(['message' => 'Supplier deleted successfully'], 200);
        }

        return redirect('/suppliers')->with('success', 'Supplier deleted successfully!');
    }


    // Get supplier performance (top suppliers by number of supplies)
    public function getSupplierPerformance()
    {
        $suppliers = DB::table('stocks')
            ->join('suppliers', 'stocks.SupplierID', '=', 'suppliers.SupplierID')
            ->select(
                'suppliers.SupplierID',
                'suppliers.SupplierName',
                DB::raw('COUNT(stocks.StockID) as total_supplies'),
                DB::raw('SUM(stocks.QuantityAdded) as total_quantity_supplied')
            )
            ->groupBy('suppliers.SupplierID', 'suppliers.SupplierName')
            ->orderByDesc('total_supplies')
            ->get();

        return response()->json([
            'message' => 'Supplier performance retrieved successfully.',
            'data' => $suppliers
        ], 200);
    }

    // Get supplier report data (for Vue frontend - matches Blade view calculation)
    public function getSupplierReportData()
    {
        // Get total suppliers
        $totalSuppliers = Suppliers::count();
        
        // Get total stock value from suppliers (matching Blade calculation)
        $totalStockValue = DB::table('stocks')->sum(DB::raw('QuantityAdded * CostPrice'));
        
        // Get active suppliers (those with stock)
        $activeSuppliers = Suppliers::whereHas('stocks')->count();
        
        // Get suppliers with low performance (no recent stock)
        $lowPerformanceSuppliers = Suppliers::whereDoesntHave('stocks', function($query) {
            $query->where('updated_at', '>=', now()->subDays(30));
        })->count();

        // Get supplier performance data (matching Blade calculation)
        $supplierPerformance = DB::table('suppliers')
            ->leftJoin('stocks', 'suppliers.SupplierID', '=', 'stocks.SupplierID')
            ->select(
                'suppliers.SupplierID',
                'suppliers.SupplierName',
                DB::raw('COUNT(stocks.StockID) as total_stocks'),
                DB::raw('COALESCE(SUM(stocks.QuantityAdded), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(stocks.QuantityAdded * stocks.CostPrice), 0) as total_value')
            )
            ->groupBy('suppliers.SupplierID', 'suppliers.SupplierName')
            ->orderByDesc('total_value')
            ->get();

        // Get stock value by supplier (matching Blade calculation exactly)
        $stockValueBySupplier = DB::table('suppliers')
            ->leftJoin('stocks', 'suppliers.SupplierID', '=', 'stocks.SupplierID')
            ->select(
                'suppliers.SupplierID',
                'suppliers.SupplierName',
                DB::raw('COALESCE(SUM(stocks.QuantityAdded * stocks.CostPrice), 0) as total_value')
            )
            ->groupBy('suppliers.SupplierID', 'suppliers.SupplierName')
            ->having('total_value', '>', 0)
            ->orderByDesc('total_value')
            ->get();

        $stockValueBySupplierData = [
            'labels' => $stockValueBySupplier->pluck('SupplierName')->toArray(),
            'data' => $stockValueBySupplier->pluck('total_value')->toArray()
        ];

        // Get recent stock additions
        $recentStockAdditions = Stocks::with(['supplier', 'gadget'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'totalSuppliers' => $totalSuppliers,
            'totalStockValue' => $totalStockValue,
            'activeSuppliers' => $activeSuppliers,
            'lowPerformanceSuppliers' => $lowPerformanceSuppliers,
            'supplierPerformance' => $supplierPerformance,
            'stockValueBySupplierData' => $stockValueBySupplierData,
            'recentStockAdditions' => $recentStockAdditions
        ], 200);
    }

    // Supplier Report View
    public function supplierReport()
    {
        // Use existing API method for supplier performance
        $supplierPerformanceResponse = $this->getSupplierPerformance();
        $supplierPerformanceDataRaw = $supplierPerformanceResponse->getData()->data ?? [];
        // Ensure it's a collection
        $supplierPerformanceData = is_array($supplierPerformanceDataRaw) 
            ? collect($supplierPerformanceDataRaw) 
            : $supplierPerformanceDataRaw;

        // Get total suppliers
        $totalSuppliers = Suppliers::count();
        
        // Get total stock value from suppliers
        $totalStockValue = DB::table('stocks')->sum(DB::raw('QuantityAdded * CostPrice'));
        
        // Get active suppliers (those with stock)
        $activeSuppliers = Suppliers::whereHas('stocks')->count();
        
        // Get suppliers with low performance (no recent stock)
        $lowPerformanceSuppliers = Suppliers::whereDoesntHave('stocks', function($query) {
            $query->where('updated_at', '>=', now()->subDays(30));
        })->count();

        // Get supplier performance data
        $supplierPerformance = DB::table('suppliers')
            ->leftJoin('stocks', 'suppliers.SupplierID', '=', 'stocks.SupplierID')
            ->select(
                'suppliers.SupplierName',
                DB::raw('COUNT(stocks.StockID) as total_stocks'),
                DB::raw('COALESCE(SUM(stocks.QuantityAdded), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(stocks.QuantityAdded * stocks.CostPrice), 0) as total_value')
            )
            ->groupBy('suppliers.SupplierID', 'suppliers.SupplierName')
            ->orderByDesc('total_value')
            ->get();

        // Get stock value by supplier
        $stockValueBySupplier = DB::table('suppliers')
            ->leftJoin('stocks', 'suppliers.SupplierID', '=', 'stocks.SupplierID')
            ->select(
                'suppliers.SupplierName',
                DB::raw('COALESCE(SUM(stocks.QuantityAdded * stocks.CostPrice), 0) as total_value')
            )
            ->groupBy('suppliers.SupplierID', 'suppliers.SupplierName')
            ->having('total_value', '>', 0)
            ->orderByDesc('total_value')
            ->get();

        $stockValueBySupplierData = [
            'labels' => $stockValueBySupplier->pluck('SupplierName')->toArray(),
            'data' => $stockValueBySupplier->pluck('total_value')->toArray()
        ];

        // Get recent stock additions
        $recentStockAdditions = Stocks::with(['supplier', 'gadget'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('reports.suppliers', compact(
            'totalSuppliers',
            'totalStockValue',
            'activeSuppliers',
            'lowPerformanceSuppliers',
            'supplierPerformance',
            'stockValueBySupplierData',
            'recentStockAdditions'
        ) + ['supplierPerformanceData' => $supplierPerformanceData]);
    }

}