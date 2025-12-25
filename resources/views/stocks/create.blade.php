<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add New Stock - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ========================================
           CONSISTENT DASHBOARD WIDGET DESIGN SYSTEM
           ======================================== */
        
        /* Base Widget Styles - Applied to ALL widgets */
        .dashboard-widget {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        
        .dashboard-widget:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 5;
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
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        /* Hero Section */
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
        <!-- Hero Section -->
        <div class="stocks-hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1>
                            <i class="fas fa-plus-circle"></i> Add New Stock
                        </h1>
                        <p>Add stock entries for your inventory items</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <a href="/stocks" class="btn btn-light btn-lg">
                            <i class="fas fa-arrow-left me-2"></i> Back to Stocks
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card dashboard-widget form-widget">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-boxes"></i> Stock Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('stocks.store') }}" method="POST" id="stockForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="GadgetID" class="form-label">
                                        <i class="fas fa-mobile-alt me-2"></i>Gadget <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('GadgetID') is-invalid @enderror" id="GadgetID" name="GadgetID" required>
                                        <option value="">Select a gadget</option>
                                        @foreach($gadgets as $gadget)
                                            <option value="{{ $gadget->GadgetID }}" 
                                                    {{ (old('GadgetID', isset($selectedGadget) && $selectedGadget ? $selectedGadget->GadgetID : '') == $gadget->GadgetID) ? 'selected' : '' }}>
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
                                    <label for="SupplierID" class="form-label">
                                        <i class="fas fa-truck me-2"></i>Supplier
                                    </label>
                                    <select class="form-select @error('SupplierID') is-invalid @enderror" id="SupplierID" name="SupplierID">
                                        <option value="">Select a supplier (optional)</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->SupplierID }}" {{ old('SupplierID') == $supplier->SupplierID ? 'selected' : '' }}>
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
                                    <label for="QuantityAdded" class="form-label">
                                        <i class="fas fa-boxes me-2"></i>Quantity Added <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('QuantityAdded') is-invalid @enderror" 
                                           id="QuantityAdded" name="QuantityAdded" value="{{ old('QuantityAdded') }}" 
                                           min="1" required>
                                    @error('QuantityAdded')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="PurchaseDate" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Purchase Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('PurchaseDate') is-invalid @enderror" 
                                           id="PurchaseDate" name="PurchaseDate" value="{{ old('PurchaseDate', date('Y-m-d')) }}" required>
                                    @error('PurchaseDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="CostPrice" class="form-label">
                                        <i class="fas fa-dollar-sign me-2"></i>Cost Price (₱) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control @error('CostPrice') is-invalid @enderror" 
                                               id="CostPrice" name="CostPrice" value="{{ old('CostPrice') }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('CostPrice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-2"></i> Add Stock
                                    </button>
                                    <a href="/stocks" class="btn btn-secondary" id="cancelBtn">
                                        <i class="fas fa-times me-2"></i> Cancel
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
            // Cancel button - immediate navigation
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    // Allow natural navigation - no preventDefault
                    return true;
                });
            }

            // Back button - immediate navigation
            const backBtn = document.querySelector('a.btn-light[href="/stocks"]');
            if (backBtn && backBtn !== cancelBtn) {
                backBtn.addEventListener('click', function(e) {
                    // Allow natural navigation
                    return true;
                });
            }

            // Form submission with loading state
            const form = document.getElementById('stockForm');
            const submitBtn = document.getElementById('submitBtn');
            
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
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
                    
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

