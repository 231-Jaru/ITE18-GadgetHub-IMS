<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Suppliers;
use App\Models\Gadgets;
use App\Models\Stocks;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseOrdersController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'admin', 'items.gadget'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        if (request()->is('api/*')) {
            return response()->json($purchaseOrders, 200);
        }

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create()
    {
        $suppliers = Suppliers::all();
        // Only get non-deleted gadgets
        $gadgets = Gadgets::with(['category', 'brand'])
            ->whereNull('deleted_at')
            ->get();

        return view('purchase-orders.create', compact('suppliers', 'gadgets'));
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'SupplierID' => 'required|exists:suppliers,SupplierID',
            'OrderDate' => 'nullable|date',
            'ExpectedDeliveryDate' => 'nullable|date|after_or_equal:OrderDate',
            'Notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.GadgetID' => [
                'required',
                Rule::exists('gadgets', 'GadgetID')->whereNull('deleted_at')
            ],
            'items.*.Quantity' => 'required|integer|min:1',
            'items.*.UnitCost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generate PO Number
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(PurchaseOrder::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'PONumber' => $poNumber,
                'SupplierID' => $validated['SupplierID'],
                'AdminID' => session('user_id'),
                'Status' => 'DRAFT',
                'OrderDate' => $validated['OrderDate'] ?? now(),
                'ExpectedDeliveryDate' => $validated['ExpectedDeliveryDate'] ?? null,
                'Notes' => $validated['Notes'] ?? null,
                'TotalAmount' => 0,
            ]);

            // Calculate total and create items
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $itemTotal = $item['Quantity'] * $item['UnitCost'];
                $totalAmount += $itemTotal;

                PurchaseOrderItem::create([
                    'PurchaseOrderID' => $purchaseOrder->PurchaseOrderID,
                    'GadgetID' => $item['GadgetID'],
                    'Quantity' => $item['Quantity'],
                    'UnitCost' => $item['UnitCost'],
                    'TotalCost' => $itemTotal,
                    'QuantityReceived' => 0,
                ]);
            }

            // Update total amount
            $purchaseOrder->update(['TotalAmount' => $totalAmount]);

            DB::commit();

            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Purchase order created successfully',
                    'purchase_order' => $purchaseOrder->load('items')
                ], 201);
            }

            return redirect('/purchase-orders/' . $purchaseOrder->PurchaseOrderID)
                ->with('success', 'Purchase order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Failed to create purchase order',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to create purchase order: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified purchase order.
     */
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'admin', 'items.gadget'])->find($id);

        if (!$purchaseOrder) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Purchase order not found'], 404);
            }
            return redirect('/purchase-orders')->with('error', 'Purchase order not found');
        }

        if (request()->is('api/*')) {
            return response()->json($purchaseOrder, 200);
        }

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Update purchase order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::with('items')->find($id);

        if (!$purchaseOrder) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Purchase order not found'], 404);
            }
            return back()->with('error', 'Purchase order not found');
        }

        $validated = $request->validate([
            'Status' => 'required|in:DRAFT,ORDERED,RECEIVED,CANCELLED',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $purchaseOrder->Status;
            $purchaseOrder->update([
                'Status' => $validated['Status'],
                'ReceivedDate' => $validated['Status'] === 'RECEIVED' ? now() : null,
            ]);

            // If status changed to RECEIVED, add stock automatically
            if ($validated['Status'] === 'RECEIVED' && $oldStatus !== 'RECEIVED') {
                foreach ($purchaseOrder->items as $item) {
                    $quantityToAdd = $item->Quantity - $item->QuantityReceived;
                    
                    if ($quantityToAdd > 0) {
                        // Find or create stock entry
                        $stock = Stocks::where('GadgetID', $item->GadgetID)
                            ->where('SupplierID', $purchaseOrder->SupplierID)
                            ->first();

                        if (!$stock) {
                            $stock = Stocks::create([
                                'GadgetID' => $item->GadgetID,
                                'SupplierID' => $purchaseOrder->SupplierID,
                                'QuantityAdded' => 0,
                                'CostPrice' => $item->UnitCost,
                                'PurchaseDate' => now(),
                            ]);
                        }

                        // Update stock
                        $stock->QuantityAdded += $quantityToAdd;
                        $stock->save();

                        // Create transaction
                        Transactions::create([
                            'GadgetID' => $item->GadgetID,
                            'StockID' => $stock->StockID,
                            'AdminID' => session('user_id'),
                            'TransactionType' => 'IN',
                            'Quantity' => $quantityToAdd,
                            'TransactionDate' => now(),
                        ]);

                        // Update received quantity
                        $item->QuantityReceived = $item->Quantity;
                        $item->save();
                    }
                }
            }

            DB::commit();

            // Reload purchase order with relationships
            $purchaseOrder->load(['supplier', 'admin', 'items.gadget']);

            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Purchase order status updated successfully',
                    'purchase_order' => $purchaseOrder
                ], 200);
            }

            return redirect('/purchase-orders/' . $purchaseOrder->PurchaseOrderID)
                ->with('success', 'Purchase order status updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Failed to update status',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified purchase order.
     */
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Purchase order not found'], 404);
            }
            return redirect('/purchase-orders')->with('error', 'Purchase order not found');
        }

        // Only allow deletion of DRAFT orders
        if ($purchaseOrder->Status !== 'DRAFT') {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Only draft purchase orders can be deleted'], 403);
            }
            return back()->with('error', 'Only draft purchase orders can be deleted');
        }

        // Authorization check: Only allow delete if user created the purchase order or is admin
        $currentAdminId = session('user_id');
        if ($purchaseOrder->AdminID && $purchaseOrder->AdminID != $currentAdminId) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Unauthorized. You can only delete purchase orders you created.'], 403);
            }
            return back()->with('error', 'Unauthorized. You can only delete purchase orders you created.');
        }

        $purchaseOrder->delete();

        if (request()->is('api/*')) {
            return response()->json(['message' => 'Purchase order deleted successfully'], 200);
        }

        return redirect('/purchase-orders')
            ->with('success', 'Purchase order deleted successfully!');
    }
}
