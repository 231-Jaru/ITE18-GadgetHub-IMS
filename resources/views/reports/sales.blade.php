<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Reports - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i> Purchase Reports
                    </h1>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Purchase Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Purchase Value</h6>
                                <h3 class="mb-0">₱{{ number_format($totalSales, 2) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
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
                                <h6 class="card-title">Transactions</h6>
                                <h3 class="mb-0">{{ $totalTransactions }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-receipt fa-2x opacity-75"></i>
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
                                <h6 class="card-title">Avg. Purchase</h6>
                                <h3 class="mb-0">{{ \App\Helpers\CurrencyHelper::formatPhp($averageOrder) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calculator fa-2x opacity-75"></i>
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
                                <h6 class="card-title">Growth</h6>
                                <h3 class="mb-0">{{ $growthPercentage > 0 ? '+' : '' }}{{ number_format($growthPercentage, 1) }}%</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-trending-up fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Data -->
        <div class="row">
            <!-- Monthly Purchase Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line text-primary"></i> Monthly Purchase Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlySalesChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Purchased Products -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy text-warning"></i> Top Purchased Products
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($topSellingProducts as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1">{{ $product->GadgetName }}</h6>
                                    <small class="text-muted">{{ $product->category->CategoryName ?? 'No Category' }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $product->total_sold }} units</span>
                            </div>
                            @empty
                            <div class="list-group-item text-center text-muted">
                                <i class="fas fa-info-circle"></i> No purchase data available
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchases by Category -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie text-success"></i> Purchases by Category
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock text-info"></i> Recent Transactions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Purchased By</th>
                                        <th>Product</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->admin->Username ?? 'System' }}</td>
                                        <td>{{ $transaction->gadget->GadgetName ?? 'Unknown Product' }}</td>
                                        <td class="text-success">{{ \App\Helpers\CurrencyHelper::formatPhp(($transaction->Quantity ?? 0) * ($transaction->stock->CostPrice ?? 0)) }}</td>
                                        <td>{{ $transaction->TransactionDate ? $transaction->TransactionDate->format('Y-m-d') : 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No recent transactions
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
    <script>
        // Monthly Purchase Chart
        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlySalesData['labels']) !!},
                datasets: [{
                    label: 'Purchases (₱)',
                    data: {!! json_encode($monthlySalesData['data']) !!},
                    borderColor: 'rgb(13, 110, 253)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
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
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($categoryData['data']) !!},
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#fd7e14',
                        '#6f42c1',
                        '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>