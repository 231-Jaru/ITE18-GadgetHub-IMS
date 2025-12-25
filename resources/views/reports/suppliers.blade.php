<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Report - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('partials.navbar')

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0"><i class="fas fa-truck"></i> Supplier Report</h1>
            <div class="btn-group" role="group">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>
        </div>

        <!-- Supplier Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Suppliers</h6>
                                <h3 class="mb-0">{{ number_format($totalSuppliers) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-truck fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Active Suppliers</h6>
                                <h3 class="mb-0">{{ number_format($activeSuppliers) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Stock Value</h6>
                                <h3 class="mb-0">{{ \App\Helpers\CurrencyHelper::formatPhp($totalStockValue, 0) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Low Performance</h6>
                                <h3 class="mb-0">{{ number_format($lowPerformanceSuppliers) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Analytics -->
        <div class="row">
            <!-- Stock Value by Supplier Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar"></i> Stock Value by Supplier
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="stockValueChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Suppliers -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy"></i> Top Suppliers
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($supplierPerformance->take(5) as $supplier)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1">{{ $supplier->SupplierName }}</h6>
                                    <small class="text-muted">{{ $supplier->total_stocks }} items</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ \App\Helpers\CurrencyHelper::formatPhp($supplier->total_value, 0) }}</span>
                            </div>
                            @empty
                            <div class="list-group-item text-center text-muted">
                                <i class="fas fa-info-circle"></i> No supplier data available
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Performance Table -->
            <div class="col-lg-12 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table"></i> Supplier Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Supplier Name</th>
                                        <th>Total Items</th>
                                        <th>Total Quantity</th>
                                        <th>Total Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supplierPerformance as $supplier)
                                    <tr>
                                        <td>{{ $supplier->SupplierName }}</td>
                                        <td>{{ $supplier->total_stocks }}</td>
                                        <td>{{ number_format($supplier->total_quantity) }}</td>
                                        <td class="text-success">{{ \App\Helpers\CurrencyHelper::formatPhp($supplier->total_value) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No supplier data available
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Stock Additions -->
            <div class="col-lg-12 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history"></i> Recent Stock Additions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Cost Price</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentStockAdditions as $stock)
                                    <tr>
                                        <td>{{ $stock->supplier->SupplierName ?? 'Unknown' }}</td>
                                        <td>{{ $stock->gadget->GadgetName ?? 'Unknown Product' }}</td>
                                        <td>{{ $stock->QuantityAdded }}</td>
                                        <td class="text-success">{{ \App\Helpers\CurrencyHelper::formatPhp($stock->CostPrice) }}</td>
                                        <td>{{ $stock->updated_at ? $stock->updated_at->format('Y-m-d') : 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No recent stock additions
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Stock Value by Supplier Chart
        const stockValueCtx = document.getElementById('stockValueChart').getContext('2d');
        new Chart(stockValueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stockValueBySupplierData['labels']) !!},
                datasets: [{
                    label: 'Stock Value ($)',
                    data: {!! json_encode($stockValueBySupplierData['data']) !!},
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)', // Primary
                        'rgba(25, 135, 84, 0.7)', // Success
                        'rgba(253, 126, 20, 0.7)', // Warning
                        'rgba(108, 117, 125, 0.7)', // Secondary
                        'rgba(111, 66, 193, 0.7)'  // Purple
                    ],
                    borderColor: [
                        'rgba(13, 110, 253, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(253, 126, 20, 1)',
                        'rgba(108, 117, 125, 1)',
                        'rgba(111, 66, 193, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Stock Value ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Supplier'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>