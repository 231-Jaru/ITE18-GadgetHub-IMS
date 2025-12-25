<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Details - Gadgethub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ========================================
           CONSISTENT DASHBOARD WIDGET DESIGN SYSTEM
           ======================================== */
        
        /* Base Widget Styles - Applied to ALL widgets */
        .dashboard-widget {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        
        .dashboard-widget:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 32px rgba(0,0,0,0.15);
            z-index: 10;
        }
        
        /* Statistics Cards - Consistent styling */
        .stats-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            z-index: 5;
        }
        
        .stats-card:hover i {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        /* Stocks-specific enhancements */
        .stocks-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 12px;
        }
        
        .stocks-hero h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stocks-hero p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Stock status badges */
        .stock-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .stock-status.in-stock {
            background-color: #d4edda;
            color: #155724;
        }
        
        .stock-status.low-stock {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .stock-status.out-of-stock {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>
                        <i class="fas fa-box"></i> Stock Details
                    </h1>
                    <a href="/stocks" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Stocks
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Stock Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Stock ID:</th>
                                                <td>{{ $stock->StockID }}</td>
                                            </tr>
                                            <tr>
                                                <th>Gadget:</th>
                                                <td>
                                                    <strong>{{ $stock->gadget->GadgetName ?? 'N/A' }}</strong>
                                                    @if($stock->gadget)
                                                        <br><small class="text-muted">
                                                            {{ $stock->gadget->category->CategoryName ?? 'N/A' }} - 
                                                            {{ $stock->gadget->brand->BrandName ?? 'N/A' }}
                                                        </small>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Supplier:</th>
                                                <td>{{ $stock->supplier->SupplierName ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Quantity:</th>
                                                <td>
                                                    <span class="badge bg-{{ $stock->QuantityAdded < 10 ? 'warning' : ($stock->QuantityAdded == 0 ? 'danger' : 'success') }} fs-6">
                                                        {{ $stock->QuantityAdded }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Cost Price:</th>
                                                <td><strong>{{ \App\Helpers\CurrencyHelper::formatPhp($stock->CostPrice) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Purchase Date:</th>
                                                <td>{{ $stock->PurchaseDate ? $stock->PurchaseDate->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="/stocks/{{ $stock->StockID }}/edit" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit Stock
                                    </a>
                                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#restockModal">
                                        <i class="fas fa-plus"></i> Restock
                                    </button>
                                    <form action="/stocks/{{ $stock->StockID }}" method="POST" class="d-inline delete-form" data-item-name="stock record" onsubmit="return confirm('Are you sure you want to delete this stock record? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-trash"></i> Delete Stock
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if($stock->gadget && $stock->gadget->stocks->count() > 1)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Other Stock for this Gadget</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @foreach($stock->gadget->stocks->where('StockID', '!=', $stock->StockID) as $otherStock)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $otherStock->supplier->SupplierName ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">Qty: {{ $otherStock->QuantityAdded }}</small>
                                        </div>
                                        <a href="/stocks/{{ $otherStock->StockID }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restock Modal -->
    <div class="modal fade" id="restockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restock Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/api/stocks/{{ $stock->StockID }}/restock" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="QuantityAdded" class="form-label">Quantity to Add</label>
                            <input type="number" class="form-control" id="QuantityAdded" name="QuantityAdded" min="1" required>
                        </div>
                        <p class="text-muted">Current stock: <strong>{{ $stock->QuantityAdded }}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Restock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
</body>
</html>
