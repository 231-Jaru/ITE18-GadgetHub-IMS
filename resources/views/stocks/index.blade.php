<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stocks - Inventory System</title>
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
        
        /* Table Widgets - Consistent styling */
        .table-widget {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table-widget:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 5;
        }
        
        /* Card Headers - Consistent styling */
        .dashboard-widget .card-header {
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.25rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .dashboard-widget:hover .card-header {
            background-color: rgba(0,0,0,0.05);
        }
        
        .dashboard-widget .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .dashboard-widget .card-header i {
            margin-right: 0.5rem;
            transition: transform 0.3s ease;
        }
        
        .dashboard-widget:hover .card-header i {
            transform: scale(1.1);
        }
        
        /* Card Body - Consistent styling */
        .dashboard-widget .card-body {
            padding: 1.25rem;
        }
        
        /* Button hover effects for action buttons */
        .dashboard-widget .btn {
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .dashboard-widget .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Table styling within widgets */
        .dashboard-widget .table {
            margin-bottom: 0;
        }
        
        .dashboard-widget .table th {
            border-top: none;
            font-weight: 600;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .dashboard-widget .table td {
            border-top: 1px solid #f8f9fa;
            vertical-align: middle;
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
        
        /* Enhanced table styling for stocks */
        .table-widget .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .table-widget .table td {
            vertical-align: middle;
            border-top: 1px solid #f1f3f4;
        }
        
        .table-widget .table tbody tr:hover {
            background-color: #f8f9fa;
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
        
        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard-widget:hover {
                transform: translateY(-3px) scale(1.01);
            }
            
            .stats-card:hover {
                transform: translateY(-2px) scale(1.02);
            }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <!-- Hero Section -->
        <div class="stocks-hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1>
                            <i class="fas fa-boxes"></i> Stock Management
                        </h1>
                        <p>Comprehensive stock tracking and inventory management system</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <a href="/stock-adjustments/create" class="btn btn-warning btn-lg">
                            <i class="fas fa-adjust me-2"></i> Create Adjustment
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-primary text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $stats['total_stocks'] ?? $stocks->total() }}</h4>
                                <p class="card-text mb-0 small">Total Stock Entries</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-boxes fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-success text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $stats['well_stocked'] ?? 0 }}</h4>
                                <p class="card-text mb-0 small">Well Stocked</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-warning text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $stats['low_stock'] ?? 0 }}</h4>
                                <p class="card-text mb-0 small">Low Stock</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-info text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $stats['unique_gadgets'] ?? 0 }}</h4>
                                <p class="card-text mb-0 small">Unique Gadgets</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-mobile-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div class="flex-grow-1">
                            <strong>{{ session('success') }}</strong>
                            @if(session('stock_id'))
                                <div class="mt-2">
                                    <small class="d-block mb-2">What would you like to do next?</small>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('stocks.show', session('stock_id')) }}" class="btn btn-outline-success">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </a>
                                        <a href="{{ route('purchase-orders.create') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-shopping-cart me-1"></i> Create Purchase Order
                                        </a>
                                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-info">
                                            <i class="fas fa-list me-1"></i> View Transactions
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card dashboard-widget table-widget">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list"></i> Stock Inventory
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Gadget</th>
                                        <th>Supplier</th>
                                        <th>Quantity</th>
                                        <th>Cost Price</th>
                                        <th>Purchase Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocks as $stock)
                                    <tr style="cursor: pointer;" onclick="window.location.href='{{ route('stocks.show', $stock->StockID) }}'">
                                        <td>{{ $stock->StockID }}</td>
                                        <td>
                                            <strong>{{ $stock->gadget->GadgetName ?? 'N/A' }}</strong>
                                            @if($stock->gadget)
                                                <br><small class="text-muted">{{ $stock->gadget->category->CategoryName ?? 'N/A' }} - {{ $stock->gadget->brand->BrandName ?? 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $stock->supplier->SupplierName ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $stock->QuantityAdded < 10 ? 'warning' : ($stock->QuantityAdded == 0 ? 'danger' : 'success') }}">
                                                {{ $stock->QuantityAdded }}
                                            </span>
                                        </td>
                                        <td>{{ \App\Helpers\CurrencyHelper::formatPhp($stock->CostPrice) }}</td>
                                        <td>{{ $stock->PurchaseDate ? $stock->PurchaseDate->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown{{ $stock->StockID }}" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $stock->StockID }}">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('stocks.edit', $stock->StockID) }}">
                                                            <i class="fas fa-edit text-primary me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="/stocks/{{ $stock->StockID }}" method="POST" class="d-inline delete-form" data-item-name="stock record" onsubmit="return confirm('Are you sure you want to delete this stock record? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="event.stopPropagation();">
                                                                <i class="fas fa-trash me-2"></i> Delete
                                                    </button>
                                                </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No stock records found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($stocks->hasPages())
                            <div class="card-footer bg-light py-3">
                                @include('partials.simple-pagination', ['paginator' => $stocks])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Gadgets Without Stock Section -->
        @if(isset($gadgetsWithoutStock) && $gadgetsWithoutStock->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-widget table-widget">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Gadgets Without Stock
                        </h5>
                        <span class="badge bg-dark">{{ $gadgetsWithoutStock->count() }} gadgets</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Gadget</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gadgetsWithoutStock as $gadget)
                                    <tr @if(isset($selectedGadget) && $selectedGadget && $selectedGadget->GadgetID == $gadget->GadgetID) class="table-warning" @endif>
                                        <td>
                                            <strong>{{ $gadget->GadgetName }}</strong>
                                            @if(isset($selectedGadget) && $selectedGadget && $selectedGadget->GadgetID == $gadget->GadgetID)
                                                <span class="badge bg-warning text-dark ms-2">Selected</span>
                                            @endif
                                        </td>
                                        <td>{{ $gadget->category->CategoryName ?? 'N/A' }}</td>
                                        <td>{{ $gadget->brand->BrandName ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> No Stock
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="openAddStockModal({{ $gadget->GadgetID }}, '{{ $gadget->GadgetName }}')">
                                                <i class="fas fa-plus me-1"></i> Add Stock
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Low Stock Items Section -->
        @if(isset($lowStockItems) && $lowStockItems->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card dashboard-widget table-widget">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Items (≤10 units)
                        </h5>
                        <span class="badge bg-light text-dark">{{ $lowStockItems->count() }} items</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Gadget</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Current Stock</th>
                                        <th>Supplier</th>
                                        <th>Last Purchase</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockItems as $stock)
                                    <tr style="cursor: pointer;" onclick="window.location.href='{{ route('stocks.show', $stock->StockID) }}'">
                                        <td>
                                            <strong>{{ $stock->gadget->GadgetName ?? 'N/A' }}</strong>
                                            @if($stock->gadget)
                                                <br><small class="text-muted">{{ $stock->gadget->category->CategoryName ?? 'N/A' }} - {{ $stock->gadget->brand->BrandName ?? 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $stock->gadget->category->CategoryName ?? 'N/A' }}</td>
                                        <td>{{ $stock->gadget->brand->BrandName ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-warning fs-6">{{ $stock->QuantityAdded }}</span>
                                        </td>
                                        <td>{{ $stock->supplier->SupplierName ?? 'N/A' }}</td>
                                        <td>{{ $stock->PurchaseDate ? $stock->PurchaseDate->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); openAddStockModal({{ $stock->gadget->GadgetID }}, '{{ $stock->gadget->GadgetName }}')">
                                                <i class="fas fa-plus me-1"></i> Restock
                                                </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Out of Stock Items Section -->
        @if(isset($outOfStockItems) && $outOfStockItems->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card dashboard-widget table-widget">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-times-circle me-2"></i>Out of Stock Items (0 units)
                        </h5>
                        <span class="badge bg-light text-dark">{{ $outOfStockItems->count() }} items</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-danger">
                                    <tr>
                                        <th>Gadget</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Current Stock</th>
                                        <th>Supplier</th>
                                        <th>Last Purchase</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($outOfStockItems as $stock)
                                    <tr>
                                        <td>
                                            <strong>{{ $stock->gadget->GadgetName ?? 'N/A' }}</strong>
                                            @if($stock->gadget)
                                                <br><small class="text-muted">{{ $stock->gadget->category->CategoryName ?? 'N/A' }} - {{ $stock->gadget->brand->BrandName ?? 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $stock->gadget->category->CategoryName ?? 'N/A' }}</td>
                                        <td>{{ $stock->gadget->brand->BrandName ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-danger fs-6">{{ $stock->QuantityAdded }}</span>
                                        </td>
                                        <td>{{ $stock->supplier->SupplierName ?? 'N/A' }}</td>
                                        <td>{{ $stock->PurchaseDate ? $stock->PurchaseDate->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); openAddStockModal({{ $stock->gadget->GadgetID }}, '{{ $stock->gadget->GadgetName }}')">
                                                <i class="fas fa-plus me-1"></i> Restock
                                                </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addStockModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add Stock to Gadget
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStockForm">
                        @csrf
                        <input type="hidden" id="gadgetId" name="gadgetId">
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-mobile-alt me-2"></i>Gadget
                            </label>
                            <input type="text" class="form-control" id="gadgetName" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="supplierId" class="form-label">
                                <i class="fas fa-truck me-2"></i>Supplier
                            </label>
                            <select class="form-select" id="supplierId" name="SupplierID">
                                <option value="">Select Supplier</option>
                                @if(isset($suppliers))
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->SupplierID }}">{{ $supplier->SupplierName }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantityAdded" class="form-label">
                                    <i class="fas fa-boxes me-2"></i>Quantity
                                </label>
                                <input type="number" class="form-control" id="quantityAdded" name="QuantityAdded" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="purchaseDate" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Purchase Date
                                </label>
                                <input type="date" class="form-control" id="purchaseDate" name="PurchaseDate" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="costPrice" class="form-label">
                                    <i class="fas fa-dollar-sign me-2"></i>Cost Price
                                </label>
                                <input type="number" class="form-control" id="costPrice" name="CostPrice" step="0.01" min="0" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveStockBtn">
                        <i class="fas fa-save me-2"></i>Add Stock
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        // Global modal instance for reuse
        let addStockModalInstance = null;
        let isModalOpening = false;

        // Open Add Stock Modal - Optimized for immediate opening
        function openAddStockModal(gadgetId, gadgetName) {
            // Prevent multiple simultaneous calls
            if (isModalOpening) {
                return;
            }
            
            isModalOpening = true;
            
            // Get modal element
            const modalElement = document.getElementById('addStockModal');
            if (!modalElement) {
                console.error('Modal element not found');
                isModalOpening = false;
                return;
            }
            
            // Reuse or create modal instance
            if (!addStockModalInstance) {
                addStockModalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true
                });
            }
            
            // Open modal immediately
            addStockModalInstance.show();
            
            // Set form values after modal starts opening (non-blocking)
            requestAnimationFrame(() => {
                try {
                    const gadgetIdInput = document.getElementById('gadgetId');
                    const gadgetNameInput = document.getElementById('gadgetName');
                    const purchaseDateInput = document.getElementById('purchaseDate');
                    
                    if (gadgetIdInput) gadgetIdInput.value = gadgetId;
                    if (gadgetNameInput) gadgetNameInput.value = gadgetName || '';
                    if (purchaseDateInput) purchaseDateInput.value = new Date().toISOString().split('T')[0];
                } catch (error) {
                    console.error('Error setting form values:', error);
                }
                
                // Reset flag after a short delay
                setTimeout(() => {
                    isModalOpening = false;
                }, 300);
            });
        }

        // Auto-open modal if a specific gadget is selected
        @if(isset($selectedGadget) && $selectedGadget)
            document.addEventListener('DOMContentLoaded', function() {
                openAddStockModal({{ $selectedGadget->GadgetID }}, '{{ $selectedGadget->GadgetName }}');
            });
        @endif

        // Simple event listeners initialization
        function initializeEventListeners() {
            // Re-attach event listeners for buttons
            const addStockButtons = document.querySelectorAll('[onclick*="openAddStockModal"]');
            addStockButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const gadgetId = this.getAttribute('onclick').match(/(\d+)/)[1];
                    const gadgetName = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                    openAddStockModal(gadgetId, gadgetName);
                });
            });
        }

        // Initialize functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize event listeners
            initializeEventListeners();
        });

        // Add Stock Functionality - Optimized with loading protection
        const saveStockBtn = document.getElementById('saveStockBtn');
        let isSubmitting = false;
        
        if (saveStockBtn) {
            saveStockBtn.addEventListener('click', function() {
                // Prevent double submission
                if (isSubmitting) {
                    return;
                }
                
            const form = document.getElementById('addStockForm');
                if (!form) {
                    console.error('Form not found');
                    return;
                }
                
            const formData = new FormData(form);
                const gadgetId = document.getElementById('gadgetId')?.value;
                
                if (!gadgetId) {
                    showNotification('Gadget ID is missing', 'error');
                    return;
                }
            
            // Check if all required fields have values
                const requiredFields = ['QuantityAdded', 'CostPrice', 'PurchaseDate'];
            const missingFields = [];
            requiredFields.forEach(field => {
                if (!formData.get(field) || formData.get(field) === '') {
                    missingFields.push(field);
                }
            });
            
            if (missingFields.length > 0) {
                showNotification('Please fill in all required fields: ' + missingFields.join(', '), 'error');
                return;
            }
            
                // Set submitting flag
                isSubmitting = true;
                
                // Show loading state immediately
                const originalHTML = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
            this.disabled = true;

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    isSubmitting = false;
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                    return;
                }

                // Send AJAX request with timeout
                const fetchPromise = fetch(`/stocks/add-to-gadget/${gadgetId}`, {
                method: 'POST',
                headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
                });
                
                const timeoutPromise = new Promise((_, reject) => 
                    setTimeout(() => reject(new Error('Request timeout')), 10000)
                );
                
                Promise.race([fetchPromise, timeoutPromise])
                    .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                            // Close modal immediately
                            if (addStockModalInstance) {
                                addStockModalInstance.hide();
                            }
                    
                    // Show success message
                    showNotification(data.message || 'Stock added successfully!', 'success');
                    
                            // Reload page after short delay (reduced from 1500ms to 800ms)
                    setTimeout(() => {
                        window.location.reload();
                            }, 800);
                } else {
                    throw new Error(data.message || 'Failed to add stock');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                        showNotification('Failed to add stock: ' + error.message, 'error');
                        isSubmitting = false;
                        this.innerHTML = originalHTML;
                this.disabled = false;
            });
        });
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Reset modal when closed
        const addStockModal = document.getElementById('addStockModal');
        if (addStockModal) {
            addStockModal.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addStockForm');
                if (form) {
                    form.reset();
                }
            const btn = document.getElementById('saveStockBtn');
                if (btn) {
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Add Stock';
            btn.disabled = false;
                }
                // Reset submitting flag
                isSubmitting = false;
                isModalOpening = false;
            });
                        }

    </script>


</body>
</html>
