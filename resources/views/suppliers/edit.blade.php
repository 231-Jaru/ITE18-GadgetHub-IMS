<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Supplier - Gadgethub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Professional Design System */
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Hero Section */
        .suppliers-hero {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .suppliers-hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .suppliers-hero h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .suppliers-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .form-section {
            background: #f8fafc;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-section h5 {
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }

        .form-section h5 i {
            margin-right: 0.5rem;
            color: var(--warning-color);
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--warning-color);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: var(--transition);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #f97316 100%);
            border: none;
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
            color: white;
        }

        .btn-light {
            background: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-light:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .suppliers-hero {
                padding: 2rem 1rem;
                text-align: center;
            }

            .suppliers-hero h1 {
                font-size: 2rem;
            }

            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <!-- Hero Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="suppliers-hero">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1>
                                <i class="fas fa-edit me-3"></i>
                                Edit Supplier: {{ $supplier->SupplierName }}
                            </h1>
                            <p>Update supplier information and contact details</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-flex gap-2">
                                <a href="/suppliers/{{ $supplier->SupplierID }}" class="btn btn-light btn-lg px-4 py-3" id="viewSupplierBtn">
                                    <i class="fas fa-eye me-2"></i> View
                                </a>
                                <a href="/suppliers" class="btn btn-light btn-lg px-4 py-3" id="backToSuppliersBtn">
                                    <i class="fas fa-arrow-left me-2"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="/suppliers/{{ $supplier->SupplierID }}" method="POST" id="updateForm" class="update-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-info-circle"></i>Basic Information</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="SupplierName" class="form-label">
                                        <i class="fas fa-truck"></i> Supplier Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('SupplierName') is-invalid @enderror" 
                                           id="SupplierName" 
                                           name="SupplierName" 
                                           value="{{ old('SupplierName', $supplier->SupplierName) }}" 
                                           placeholder="Enter supplier name"
                                           required>
                                    @error('SupplierName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-address-book"></i>Contact Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ContactPerson" class="form-label">
                                        <i class="fas fa-user"></i> Contact Person
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('ContactPerson') is-invalid @enderror" 
                                           id="ContactPerson" 
                                           name="ContactPerson" 
                                           value="{{ old('ContactPerson', $supplier->ContactPerson) }}" 
                                           placeholder="Contact person name">
                                    @error('ContactPerson')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="Phone" class="form-label">
                                        <i class="fas fa-phone"></i> Phone Number
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('Phone') is-invalid @enderror" 
                                           id="Phone" 
                                           name="Phone" 
                                           value="{{ old('Phone', $supplier->Phone) }}" 
                                           placeholder="+1234567890">
                                    @error('Phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="Email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('Email') is-invalid @enderror" 
                                           id="Email" 
                                           name="Email" 
                                           value="{{ old('Email', $supplier->Email) }}" 
                                           placeholder="supplier@example.com">
                                    @error('Email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional - must be unique if provided</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                            <div>
                                <form action="/suppliers/{{ $supplier->SupplierID }}" 
                                      method="POST" 
                                      class="d-inline delete-form"
                                      data-item-name="supplier"
                                      id="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" id="deleteBtn">
                                        <i class="fas fa-trash me-2"></i> Delete Supplier
                                    </button>
                                </form>
                            </div>
                            <div class="d-flex gap-3">
                                <a href="/suppliers" class="btn btn-outline-secondary" id="cancelBtn">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-warning" id="submitBtn">
                                    <i class="fas fa-save me-2"></i> Update Supplier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure navigation buttons work correctly
            const viewBtn = document.getElementById('viewSupplierBtn');
            if (viewBtn) {
                viewBtn.href = '/suppliers/{{ $supplier->SupplierID }}';
            }
            
            const backBtn = document.getElementById('backToSuppliersBtn');
            if (backBtn) {
                backBtn.href = '/suppliers';
            }
            
            // Ensure cancel button doesn't submit form
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.href = '/suppliers';
                cancelBtn.addEventListener('click', function(e) {
                    // Just ensure it's a link, not a button that might submit form
                    if (this.tagName === 'BUTTON') {
                        e.preventDefault();
                        window.location.href = '/suppliers';
                    }
                });
            }

            // Handle update form submission - ensure it only handles update form
            const updateForm = document.getElementById('updateForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (updateForm && submitBtn) {
                // Use once: true to prevent duplicate handlers, or check if already handled
                if (!updateForm.dataset.updateHandlerAttached) {
                    updateForm.dataset.updateHandlerAttached = 'true';
                    
                    updateForm.addEventListener('submit', function(e) {
                        // Only process if this is the update form (has PUT method)
                        const methodInput = this.querySelector('input[name="_method"][value="PUT"]');
                        if (!methodInput) {
                            return; // Not the update form
                        }
                        
                        // Check if form is valid
                        if (!this.checkValidity()) {
                            e.preventDefault();
                            e.stopPropagation();
                            this.classList.add('was-validated');
                            return false;
                        }
                        
                        // Prevent double submission
                        if (submitBtn.disabled) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                        
                        // Show loading state
                        submitBtn.disabled = true;
                        const originalHTML = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
                        
                        // Re-enable after 10 seconds as fallback (in case of error)
                        setTimeout(function() {
                            if (submitBtn && submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalHTML;
                            }
                        }, 10000);
                        
                        // Allow form to submit normally
                        return true;
                    }, { once: false, capture: false });
                }
            }

            // Handle delete form - ensure it only handles delete form
            const deleteForm = document.getElementById('deleteForm');
            const deleteBtn = document.getElementById('deleteBtn');
            
            if (deleteForm && deleteBtn) {
                // Use dataset to prevent duplicate handlers
                if (!deleteForm.dataset.deleteHandlerAttached) {
                    deleteForm.dataset.deleteHandlerAttached = 'true';
                    
                    deleteForm.addEventListener('submit', function(e) {
                        // Only process if this is the delete form (has DELETE method)
                        const methodInput = this.querySelector('input[name="_method"][value="DELETE"]');
                        if (!methodInput) {
                            return; // Not the delete form
                        }
                        
                        // Stop propagation to prevent interference with update form
                        e.stopPropagation();
                        
                        // Show confirmation
                        const confirmed = confirm('Are you sure you want to delete this supplier? This action cannot be undone.');
                        
                        if (!confirmed) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return false;
                        }
                        
                        // If confirmed, show loading state
                        deleteBtn.disabled = true;
                        const originalHTML = deleteBtn.innerHTML;
                        deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
                        
                        // Allow form to submit
                        return true;
                    }, { once: false, capture: false });
                    
                    // Also handle button click to ensure it doesn't trigger update form
                    deleteBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                    }, { once: false });
                }
            }
        });
    </script>
</body>
</html>
