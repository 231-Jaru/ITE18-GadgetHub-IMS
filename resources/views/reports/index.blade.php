<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    @include('partials.navbar')

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-chart-bar"></i> Reports & Analytics
                </h1>
            </div>
        </div>

        <!-- Professional Report Cards -->
        <div class="row">
            <!-- Sales Report -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Inventory Purchases</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text text-muted mb-3">Monthly inventory purchases and procurement trends</p>
                        <ul class="list-unstyled small text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Monthly purchase analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i>Top purchased products</li>
                            <li><i class="fas fa-check text-success me-2"></i>Inventory value tracking</li>
                        </ul>
                        <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye me-1"></i>View Purchase Report
                        </a>
                    </div>
                </div>
            </div>


            <!-- Supplier Report -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-warning text-white text-center py-3">
                        <i class="fas fa-truck fa-2x mb-2"></i>
                        <h5 class="card-title mb-0">Supplier Reports</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text text-muted mb-3">Supplier performance and procurement analytics</p>
                        <ul class="list-unstyled small text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Performance metrics</li>
                            <li><i class="fas fa-check text-success me-2"></i>Procurement analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i>Cost effectiveness</li>
                        </ul>
                        <a href="{{ route('reports.suppliers') }}" class="btn btn-warning btn-sm w-100">
                            <i class="fas fa-eye me-1"></i>View Supplier Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
