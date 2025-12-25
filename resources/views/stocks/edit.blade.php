<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock - Gadgethub</title>
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
        
        /* Form Widgets - Consistent styling */
        .form-widget {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .form-widget:hover {
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
        
        /* Form styling */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard-widget:hover {
                transform: translateY(-3px) scale(1.01);
            }
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
                        <i class="fas fa-edit"></i> Edit Stock
                    </h1>
                    <a href="/stocks" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Stocks
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card dashboard-widget form-widget">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Stock Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="/stocks/{{ $stock->StockID }}" method="POST" id="editStockForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="GadgetID" class="form-label">Gadget <span class="text-danger">*</span></label>
                                    <select class="form-select @error('GadgetID') is-invalid @enderror" id="GadgetID" name="GadgetID" required>
                                        <option value="">Select a gadget</option>
                                        @foreach($gadgets as $gadget)
                                            <option value="{{ $gadget->GadgetID }}" {{ (old('GadgetID', $stock->GadgetID) == $gadget->GadgetID) ? 'selected' : '' }}>
                                                {{ $gadget->GadgetName }} 
                                                @if($gadget->category)
                                                    - {{ $gadget->category->CategoryName }}
                                                @endif
                                                @if($gadget->brand)
                                                    ({{ $gadget->brand->BrandName }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('GadgetID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="SupplierID" class="form-label">Supplier <span class="text-danger">*</span></label>
                                    <select class="form-select @error('SupplierID') is-invalid @enderror" id="SupplierID" name="SupplierID" required>
                                        <option value="">Select a supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->SupplierID }}" {{ (old('SupplierID', $stock->SupplierID) == $supplier->SupplierID) ? 'selected' : '' }}>
                                                {{ $supplier->SupplierName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('SupplierID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="QuantityAdded" class="form-label">Current Quantity</label>
                                    <input type="number" class="form-control" 
                                           id="QuantityAdded" 
                                           value="{{ $stock->QuantityAdded }}" 
                                           min="0" 
                                           readonly
                                           style="background-color: #f8f9fa;">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        To change quantity, use <a href="/stock-adjustments/create?gadget={{ $stock->GadgetID }}" target="_blank">Stock Adjustments</a> for proper audit trail.
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="PurchaseDate" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('PurchaseDate') is-invalid @enderror" 
                                           id="PurchaseDate" name="PurchaseDate" value="{{ old('PurchaseDate', $stock->PurchaseDate ? $stock->PurchaseDate->format('Y-m-d') : '') }}" required>
                                    @error('PurchaseDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="CostPrice" class="form-label">Cost Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('CostPrice') is-invalid @enderror" 
                                               id="CostPrice" name="CostPrice" value="{{ old('CostPrice', $stock->CostPrice) }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('CostPrice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" id="updateStockBtn">
                                        <i class="fas fa-save"></i> Update Stock
                                    </button>
                                    <a href="/stocks" class="btn btn-secondary" id="cancelBtn">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        // Optimized button handlers - no lag, immediate navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Cancel button - immediate navigation, no preventDefault needed
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    // Allow natural navigation - no preventDefault
                    // This ensures immediate redirect without lag
                    return true;
                });
            }

            // Back button - immediate navigation
            const backBtn = document.querySelector('a.btn-secondary[href="/stocks"]');
            if (backBtn && backBtn !== cancelBtn) {
                backBtn.addEventListener('click', function(e) {
                    // Allow natural navigation
                    return true;
                });
            }

            // Form submission with loading state
            const form = document.getElementById('editStockForm');
            const submitBtn = document.getElementById('updateStockBtn');
            
            if (form && submitBtn) {
                let isSubmitting = false;
                
                form.addEventListener('submit', function(e) {
                    // Prevent double submission
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    
                    isSubmitting = true;
                    
                    // Show loading state immediately
                    const originalHTML = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
                    
                    // Re-enable after 10 seconds as fallback (in case of error)
                    setTimeout(function() {
                        if (isSubmitting) {
                            isSubmitting = false;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHTML;
                        }
                    }, 10000);
                });
            }
        });
    </script>
</body>
</html>
