<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Gadgethub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-card {
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .stats-card .card-body {
            padding: 1.25rem;
        }
        .dashboard-section {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .dashboard-section .card-header {
            padding: 0.75rem 1.25rem;
        }
        .dashboard-section .card-body {
            padding: 1.25rem;
        }
        .dashboard-section .table {
            margin-bottom: 0;
        }
        .dashboard-section .table th {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }
        .dashboard-section .table td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }
        .container-fluid {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4 mb-4">
        <div class="mb-4">
            <h2 class="mb-2">
                <i class="fas fa-tachometer-alt text-primary"></i> Dashboard
            </h2>
            <p class="text-muted mb-0">Welcome back, {{ session('user_name', 'User') }}!</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card bg-primary text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_gadgets'] }}</h3>
                                <small>Total Gadgets</small>
                            </div>
                            <i class="fas fa-mobile-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-success text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_stocks'] }}</h3>
                                <small>Total Stock</small>
                            </div>
                            <i class="fas fa-boxes fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-info text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_transactions'] }}</h3>
                                <small>Transactions</small>
                            </div>
                            <i class="fas fa-receipt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-warning text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $lowStockItems->count() }}</h3>
                                <small>Low Stock Items</small>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Low Stock Items -->
            <div class="col-lg-6">
                <div class="card dashboard-section h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Items</h5>
                    </div>
                    <div class="card-body">
                        @if($lowStockItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Gadget</th>
                                            <th>Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowStockItems as $item)
                                        @if($item->gadget)
                                        <tr>
                                            <td>{{ $item->gadget->GadgetName ?? 'Unknown' }}</td>
                                            <td><span class="badge bg-warning">{{ $item->QuantityAdded }}</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); openAddStockModal({{ $item->gadget->GadgetID }}, '{{ $item->gadget->GadgetName ?? 'Unknown' }}')">
                                                    <i class="fas fa-plus"></i> Restock
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">All stock levels are good!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin: Recent Transactions -->
            <div class="col-lg-6">
                <div class="card dashboard-section h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        @if($recentTransactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Details</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                <strong class="text-info">{{ $transaction->admin->Username ?? 'Admin' }}</strong><br>
                                                <small class="text-muted">{{ $transaction->gadget->GadgetName ?? '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    Restock
                                                </span>
                                            </td>
                                            <td>{{ \App\Helpers\CurrencyHelper::formatPhp($transaction->TotalAmount ?? 0) }}</td>
                                            <td>
                                                <small>{{ $transaction->TransactionDate ? $transaction->TransactionDate->format('M d, Y') : 'N/A' }}</small>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">No recent transactions.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Restocked Gadgets -->
            <div class="col-lg-6">
                <div class="card dashboard-section h-100">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy me-2"></i> 
                            Top Restocked Gadgets
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($topPurchasedGadgets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Gadget</th>
                                            <th>Quantity Restocked</th>
                                            <th>Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topPurchasedGadgets as $gadget)
                                        <tr>
                                            <td>{{ $gadget->GadgetName }}</td>
                                            <td><span class="badge bg-success">{{ $gadget->total_purchased }}</span></td>
                                            <td>{{ \App\Helpers\CurrencyHelper::formatPhp($gadget->total_cost) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">No restocking data available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Inventory Purchases Chart -->
            <div class="col-lg-6">
                <div class="card dashboard-section h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Monthly Inventory Purchases (Last 6 Months)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyPurchasesChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
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
                            <select class="form-select" id="supplierId" name="SupplierID" required>
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

        // Notification function
        function showNotification(message, type = 'success') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Dashboard initialization
        document.addEventListener('DOMContentLoaded', function() {
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
                    const requiredFields = ['QuantityAdded', 'CostPrice', 'PurchaseDate', 'SupplierID'];
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
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                     document.querySelector('input[name="_token"]')?.value;
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        isSubmitting = false;
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                        showNotification('CSRF token not found. Please refresh the page.', 'error');
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
                                
                                // Reload page after delay to show notification and display session flash message
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000); // 2 second delay so user can see the notification
                            } else {
                                throw new Error(data.message || 'Failed to add stock');
                            }
                        })
                        .catch(error => {
                            console.error('Error adding stock:', error);
                            showNotification(error.message || 'Failed to add stock. Please try again.', 'error');
                            
                            // Reset button state
                            isSubmitting = false;
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        });
            });
            }
        });

        // Monthly Purchases Chart
        const monthlyPurchasesData = @json($monthlyPurchases);
        const ctx = document.getElementById('monthlyPurchasesChart');
        
        if (ctx && monthlyPurchasesData.length > 0) {
            const labels = monthlyPurchasesData.map(item => item.month_name);
            const data = monthlyPurchasesData.map(item => parseFloat(item.total));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Inventory Purchases (₱)',
                        data: data,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
