<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gadget->GadgetName }} - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .gadget-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 16px;
            color: white;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .gadget-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .gadget-header h1 {
            position: relative;
            z-index: 1;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .info-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .info-card .card-body {
            padding: 1.5rem;
        }

        .info-card i {
            margin-bottom: 0.75rem;
        }

        .info-card h4 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .stock-item {
            border-left: 4px solid var(--primary-color);
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .stock-item:hover {
            background: #f1f5f9;
            transform: translateX(5px);
        }

        .badge-custom {
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <!-- Header Section -->
        <div class="gadget-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>
                        <i class="fas fa-mobile-alt me-2"></i>{{ $gadget->GadgetName }}
                    </h1>
                    <div class="d-flex gap-3 mb-3" style="position: relative; z-index: 1;">
                        <span class="badge badge-custom">
                            <i class="fas fa-tag me-1"></i>{{ $gadget->category->CategoryName ?? 'No Category' }}
                        </span>
                        <span class="badge badge-custom">
                            <i class="fas fa-star me-1"></i>{{ $gadget->brand->BrandName ?? 'No Brand' }}
                        </span>
                    </div>
                    <p class="mb-0 opacity-75" style="position: relative; z-index: 1;">Gadget ID: {{ $gadget->GadgetID }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex gap-2 justify-content-end" style="position: relative; z-index: 1;">
                        <a href="/gadgets/{{ $gadget->GadgetID }}/edit" class="btn btn-light">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="/gadgets" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                        <h4>{{ $gadget->stocks->sum('QuantityAdded') }}</h4>
                        <p class="text-muted mb-0">Total Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <i class="fas fa-peso-sign fa-2x text-success mb-2"></i>
                        <h4>{{ \App\Helpers\CurrencyHelper::formatPhp($gadget->stocks->sum(function($stock) { return $stock->QuantityAdded * $stock->CostPricePhp; })) }}</h4>
                        <p class="text-muted mb-0">Total Value</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <i class="fas fa-warehouse fa-2x text-info mb-2"></i>
                        <h4>{{ $gadget->stocks->count() }}</h4>
                        <p class="text-muted mb-0">Stock Entries</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                        <h4>{{ $gadget->stocks->groupBy('SupplierID')->count() }}</h4>
                        <p class="text-muted mb-0">Suppliers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reorder Point Alert -->
        @php
            $totalStock = $gadget->stocks->sum('QuantityAdded');
            $reorderPoint = $gadget->ReorderPoint ?? 10;
        @endphp
        @if($totalStock <= $reorderPoint)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Low Stock Alert!</strong> This gadget has {{ $totalStock }} units remaining (Alert level: {{ $reorderPoint }}).
            <a href="{{ route('stocks.create') }}?gadget={{ $gadget->GadgetID }}" class="alert-link">Restock now</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Stock Information -->
        <div class="row">
            <div class="col-12">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-warehouse me-2"></i>Stock Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($gadget->stocks as $stock)
                        <div class="stock-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h6 class="mb-1">
                                        <i class="fas fa-boxes me-1 text-primary"></i>
                                        Quantity: <span class="text-primary fw-bold">{{ $stock->QuantityAdded }}</span>
                                    </h6>
                                    <small class="text-muted">Stock ID: {{ $stock->StockID }}</small>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">
                                        <i class="fas fa-tag me-1 text-success"></i>
                                        Cost Price: <span class="text-success fw-bold">{{ \App\Helpers\CurrencyHelper::formatPhp($stock->CostPrice) }}</span>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $stock->PurchaseDate ? $stock->PurchaseDate->format('M d, Y') : 'N/A' }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">
                                        <i class="fas fa-calculator me-1 text-info"></i>
                                        Total Value: <span class="text-info fw-bold">{{ \App\Helpers\CurrencyHelper::formatPhp($stock->QuantityAdded * $stock->CostPricePhp) }}</span>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-truck me-1"></i>
                                        {{ $stock->supplier->SupplierName ?? 'Unknown Supplier' }}
                                    </small>
                                </div>
                                <div class="col-md-3 text-end">
                                    <span class="badge bg-{{ $stock->QuantityAdded > 10 ? 'success' : ($stock->QuantityAdded > 0 ? 'warning' : 'danger') }} px-3 py-2">
                                        <i class="fas fa-{{ $stock->QuantityAdded > 10 ? 'check-circle' : ($stock->QuantityAdded > 0 ? 'exclamation-triangle' : 'times-circle') }} me-1"></i>
                                        {{ $stock->QuantityAdded > 10 ? 'In Stock' : ($stock->QuantityAdded > 0 ? 'Low Stock' : 'Out of Stock') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted mb-2">No stock information available</h5>
                            <p class="text-muted mb-4">This gadget hasn't been added to stock yet.</p>
                            <a href="{{ route('stocks.create') }}?gadget={{ $gadget->GadgetID }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Stock
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
</body>
</html>
