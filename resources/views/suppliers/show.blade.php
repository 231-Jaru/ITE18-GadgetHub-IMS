<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $supplier->SupplierName }} - Gadgethub</title>
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

        .supplier-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 16px;
            color: white;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .supplier-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .supplier-header h1 {
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
            background: white;
            overflow: visible;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .info-card .card-body {
            padding: 1.5rem;
            min-height: auto;
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
            border-left: 4px solid var(--success-color);
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
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
        }

        .detail-row {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <!-- Header Section -->
        <div class="supplier-header">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1>
                        <i class="fas fa-truck me-3"></i>
                        {{ $supplier->SupplierName }}
                    </h1>
                    <p class="mb-0 opacity-90">Supplier Details & Information</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex gap-2">
                        <a href="/suppliers/{{ $supplier->SupplierID }}/edit" class="btn btn-light btn-lg px-4 py-3" id="editSupplierBtn">
                            <i class="fas fa-edit me-2"></i> Edit
                        </a>
                        <a href="/suppliers" class="btn btn-light btn-lg px-4 py-3" id="backToSuppliersBtn">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Supplier Information -->
            <div class="col-lg-8">
                <div class="info-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-success me-2"></i>
                            Supplier Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <div class="detail-label">Supplier ID</div>
                                    <div class="detail-value">#{{ $supplier->SupplierID }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Supplier Name</div>
                                    <div class="detail-value fs-5">{{ $supplier->SupplierName }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="fas fa-user text-primary"></i> Contact Person
                                    </div>
                                    <div class="detail-value">{{ $supplier->ContactPerson ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="fas fa-phone text-success"></i> Phone Number
                                    </div>
                                    <div class="detail-value">
                                        @if($supplier->Phone)
                                            <a href="tel:{{ $supplier->Phone }}" class="text-decoration-none">
                                                {{ $supplier->Phone }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="fas fa-envelope text-info"></i> Email Address
                                    </div>
                                    <div class="detail-value">
                                        @if($supplier->Email)
                                            <a href="mailto:{{ $supplier->Email }}" class="text-decoration-none">
                                                {{ $supplier->Email }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Created At</div>
                                    <div class="detail-value">{{ $supplier->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Last Updated</div>
                                    <div class="detail-value">{{ $supplier->updated_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="col-lg-4">
                <!-- Stock Statistics -->
                <div class="info-card mb-4" style="height: auto;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar text-success me-2"></i>
                            Stock Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $stockCount = $supplier->stocks->count();
                            $totalQuantity = $supplier->stocks->sum('QuantityAdded');
                        @endphp
                        
                        <div class="row text-center g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="mb-0 text-primary">{{ $stockCount }}</h3>
                                    <small class="text-muted">Stock Records</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="mb-0 text-success">{{ $totalQuantity }}</h3>
                                    <small class="text-muted">Total Quantity</small>
                                </div>
                            </div>
                        </div>

                        @if($stockCount > 0)
                            <div class="pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Average Quantity</span>
                                    <strong>{{ number_format($totalQuantity / $stockCount, 1) }}</strong>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-2x text-muted mb-2 opacity-50"></i>
                                <p class="text-muted mb-0 small">No stock records</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="info-card" style="height: auto; min-height: 150px;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        @if($supplier->Email || $supplier->Phone)
                            <div class="d-grid gap-3">
                            @if($supplier->Email)
                                    <a href="mailto:{{ $supplier->Email }}" class="btn btn-outline-primary w-100" id="emailBtn" style="padding: 0.75rem 1rem;">
                                        <i class="fas fa-envelope me-2"></i> Send Email
                                </a>
                            @endif
                            @if($supplier->Phone)
                                    <a href="tel:{{ $supplier->Phone }}" class="btn btn-outline-success w-100" id="phoneBtn" style="padding: 0.75rem 1rem;">
                                        <i class="fas fa-phone me-2"></i> Call Supplier
                                </a>
                            @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-2x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0 small">No contact information available</p>
                                <p class="text-muted small mt-2">Add email or phone to enable quick actions</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Stock Records -->
        @if($supplier->stocks->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="info-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes text-info me-2"></i>
                            Recent Stock Records
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Gadget</th>
                                        <th>Quantity</th>
                                        <th>Cost Price</th>
                                        <th>Purchase Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->stocks->sortByDesc('PurchaseDate')->take(10) as $stock)
                                    <tr>
                                        <td>
                                            <strong>{{ $stock->gadget->GadgetName ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info rounded-pill">{{ $stock->QuantityAdded }}</span>
                                        </td>
                                        <td>₱{{ number_format($stock->CostPrice, 2) }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $stock->PurchaseDate ? $stock->PurchaseDate->format('M d, Y') : 'N/A' }}
                                            </small>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix edit button in header
            const editBtn = document.getElementById('editSupplierBtn');
            if (editBtn) {
                editBtn.setAttribute('href', '/suppliers/{{ $supplier->SupplierID }}/edit');
                editBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.location.href = '/suppliers/{{ $supplier->SupplierID }}/edit';
                    return false;
                }, true);
            }
            
            // Fix back button
            const backBtn = document.getElementById('backToSuppliersBtn');
            if (backBtn) {
                backBtn.setAttribute('href', '/suppliers');
                backBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.location.href = '/suppliers';
                    return false;
                }, true);
            }
            
        });
    </script>
</body>
</html>
