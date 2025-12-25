<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .page-header h1 {
            position: relative;
            z-index: 1;
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .badge-draft { background-color: #6b7280; }
        .badge-ordered { background-color: #3b82f6; }
        .badge-received { background-color: #10b981; }
        .badge-cancelled { background-color: #ef4444; }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-shopping-cart me-2"></i>Purchase Orders
                    </h1>
                    <p class="mb-0 opacity-75">Manage your purchase orders from suppliers</p>
                </div>
                <a href="/purchase-orders/create" class="btn btn-light">
                    <i class="fas fa-plus me-2"></i>New Purchase Order
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($purchaseOrders->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Purchase Orders Yet</h5>
                    <p class="text-muted">Create your first purchase order to get started.</p>
                    <a href="/purchase-orders/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Purchase Order
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrders as $po)
                                    <tr>
                                        <td><strong>{{ $po->PONumber }}</strong></td>
                                        <td>{{ $po->supplier->SupplierName }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $po->items->count() }} item(s)</span>
                                        </td>
                                        <td><strong>{{ \App\Helpers\CurrencyHelper::formatPhp($po->TotalAmount) }}</strong></td>
                                        <td>
                                            <span class="badge badge-{{ strtolower($po->Status) }} text-white">
                                                {{ $po->Status }}
                                            </span>
                                        </td>
                                        <td>{{ $po->OrderDate ? $po->OrderDate->format('M d, Y') : '-' }}</td>
                                        <td>
                                            <a href="/purchase-orders/{{ $po->PurchaseOrderID }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($purchaseOrders->hasPages())
                        <div class="card-footer bg-light py-3">
                            @include('partials.simple-pagination', ['paginator' => $purchaseOrders])
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
</body>
</html>

