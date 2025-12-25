<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Low Stock Alerts - Gadgethub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .alert-card {
            border-left: 4px solid #dc3545;
            transition: transform 0.2s;
        }
        .alert-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stock-progress {
            height: 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-2">
                    <i class="fas fa-exclamation-triangle text-danger"></i> Low Stock Alerts
                </h2>
                <p class="text-muted mb-0">Items with stock below 10 units</p>
            </div>
            <a href="{{ route('stocks.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Stocks
            </a>
        </div>

        @if($lowStockItems->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-check-circle"></i> Great! No items are currently low in stock.
            </div>
        @else
            <div class="row g-3">
                @foreach($lowStockItems as $stock)
                    <div class="col-md-6 col-lg-4">
                        <div class="card alert-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">{{ $stock->gadget->GadgetName ?? 'Unknown' }}</h5>
                                        <small class="text-muted">
                                            <i class="fas fa-tag"></i> {{ $stock->gadget->category->CategoryName ?? 'N/A' }}
                                        </small>
                                    </div>
                                    <span class="badge bg-danger">{{ $stock->QuantityAdded }} units</span>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Stock Level</small>
                                        <small class="text-muted">{{ $stock->QuantityAdded }} / 10</small>
                                    </div>
                                    <div class="progress stock-progress">
                                        @php
                                            $percentage = min(100, ($stock->QuantityAdded / 10) * 100);
                                        @endphp
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                             style="width: {{ $percentage }}%" 
                                             aria-valuenow="{{ $stock->QuantityAdded }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="10">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-truck"></i> Supplier: {{ $stock->supplier->SupplierName ?? 'N/A' }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-dollar-sign"></i> Cost: ₱{{ number_format($stock->CostPrice ?? 0, 2) }}
                                    </small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-sm btn-success" onclick="openAddStockModal({{ $stock->gadget->GadgetID }}, '{{ $stock->gadget->GadgetName ?? 'Unknown' }}')">
                                        <i class="fas fa-plus me-1"></i> Restock Now
                                    </button>
                                    <a href="{{ route('stocks.show', $stock->StockID) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-info-circle me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Total Low Stock Items:</strong> {{ $lowStockItems->count() }}
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
                        <i class="fas fa-plus-circle me-2"></i>Restock Item
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
        let isSubmitting = false;

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

        // Add Stock Functionality - Optimized with loading protection
        const saveStockBtn = document.getElementById('saveStockBtn');
        
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
                            
                            // Reload page after short delay (reduced from default to 800ms)
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
                // Reset flags
                isSubmitting = false;
                isModalOpening = false;
            });
        }
    </script>
</body>
</html>

