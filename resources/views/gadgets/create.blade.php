<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add New Gadget - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Professional Design System */
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --secondary-color: #06b6d4;
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
        .gadgets-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .gadgets-hero::before {
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

        .gadgets-hero h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .gadgets-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
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
            border-left: 4px solid var(--primary-color);
        }

        .form-section h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            transition: var(--transition);
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        /* Add New Buttons */
        .btn-add-new {
            background: var(--success-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: var(--transition);
            margin-left: 0.5rem;
        }

        .btn-add-new:hover {
            background: #059669;
            transform: translateY(-1px);
            color: white;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: var(--border-radius-lg);
            border: none;
            box-shadow: var(--card-shadow-hover);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1);
        }

        /* Action Buttons */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-cancel {
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #4b5563;
            transform: translateY(-1px);
            color: white;
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .gadgets-hero {
                padding: 2rem 1rem;
                text-align: center;
            }

            .gadgets-hero h1 {
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
                <div class="gadgets-hero">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1>
                                <i class="fas fa-plus-circle me-3"></i>
                                Add New Gadget
                            </h1>
                            <p>Create a new gadget and connect it to categories, brands, and inventory</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="{{ route('gadgets.index') }}" class="btn btn-light btn-lg px-4 py-3">
                                <i class="fas fa-arrow-left me-2"></i> Back to Gadgets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <form method="POST" action="{{ route('gadgets.store') }}" id="gadgetForm">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="GadgetName" class="form-label">
                                        <i class="fas fa-mobile-alt"></i>Gadget Name
                                    </label>
                                    <input type="text" class="form-control @error('GadgetName') is-invalid @enderror" 
                                           id="GadgetName" name="GadgetName" value="{{ old('GadgetName') }}" 
                                           placeholder="Enter gadget name (e.g., iPhone 15 Pro, MacBook Air M2)" required>
                                    @error('GadgetName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Category and Brand Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-layer-group me-2"></i>Category & Brand</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="CategoryID" class="form-label">
                                        <i class="fas fa-tags"></i>Category
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select @error('CategoryID') is-invalid @enderror" 
                                                id="CategoryID" name="CategoryID" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->CategoryID }}" 
                                                        {{ old('CategoryID') == $category->CategoryID ? 'selected' : '' }}>
                                                    {{ $category->CategoryName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-add-new" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                            <i class="fas fa-plus"></i> Add New
                                        </button>
                                    </div>
                                    @error('CategoryID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="BrandID" class="form-label">
                                        <i class="fas fa-award"></i>Brand
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select @error('BrandID') is-invalid @enderror" 
                                                id="BrandID" name="BrandID" required>
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->BrandID }}" 
                                                        {{ old('BrandID') == $brand->BrandID ? 'selected' : '' }}>
                                                    {{ $brand->BrandName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-add-new" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                                            <i class="fas fa-plus"></i> Add New
                                        </button>
                                    </div>
                                    @error('BrandID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Alert Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-bell me-2"></i>Low Stock Alert</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="ReorderPoint" class="form-label">
                                        <i class="fas fa-bell"></i>Alert Me When Stock Goes Below
                                    </label>
                                    <input type="number" class="form-control @error('ReorderPoint') is-invalid @enderror" 
                                           id="ReorderPoint" name="ReorderPoint" 
                                           value="{{ old('ReorderPoint', 10) }}" 
                                           min="0" placeholder="Enter number (e.g., 10)">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> You'll get an alert when stock falls below this number (default: 10)
                                    </small>
                                    @error('ReorderPoint')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-end">
                                    <a href="{{ route('gadgets.index') }}" class="btn btn-cancel">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-submit" id="submitBtn">
                                        <i class="fas fa-save me-2"></i> Create Gadget
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="mb-3">
                            <label for="newCategoryName" class="form-label">
                                <i class="fas fa-tag me-2"></i>Category Name
                            </label>
                            <input type="text" class="form-control" id="newCategoryName" name="CategoryName" 
                                   placeholder="Enter category name (e.g., Smartphone, Laptop, Tablet)" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveCategoryBtn">
                        <i class="fas fa-save me-2"></i>Add Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Brand
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBrandForm">
                        <div class="mb-3">
                            <label for="newBrandName" class="form-label">
                                <i class="fas fa-award me-2"></i>Brand Name
                            </label>
                            <input type="text" class="form-control" id="newBrandName" name="BrandName" 
                                   placeholder="Enter brand name (e.g., Apple, Samsung, Sony)" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveBrandBtn">
                        <i class="fas fa-save me-2"></i>Add Brand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global modal instances for reuse
        let categoryModalInstance = null;
        let brandModalInstance = null;
        let isSubmittingCategory = false;
        let isSubmittingBrand = false;

        // Initialize modal instances
        document.addEventListener('DOMContentLoaded', function() {
            const categoryModalElement = document.getElementById('addCategoryModal');
            const brandModalElement = document.getElementById('addBrandModal');
            
            if (categoryModalElement) {
                categoryModalInstance = new bootstrap.Modal(categoryModalElement, {
                    backdrop: true,
                    keyboard: true
                });
            }
            
            if (brandModalElement) {
                brandModalInstance = new bootstrap.Modal(brandModalElement, {
                    backdrop: true,
                    keyboard: true
                });
            }
        });

        // Add Category Functionality - Optimized with loading protection
        document.addEventListener('DOMContentLoaded', function() {
            const saveCategoryBtn = document.getElementById('saveCategoryBtn');
            if (saveCategoryBtn) {
                saveCategoryBtn.addEventListener('click', function() {
                    // Prevent double submission
                    if (isSubmittingCategory) {
                        return;
                    }
                    
                    const categoryName = document.getElementById('newCategoryName').value.trim();
                    
                    if (!categoryName) {
                        showNotification('Please enter a category name', 'error');
                        return;
                    }

                    isSubmittingCategory = true;
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
                    this.disabled = true;

                    // Get CSRF token from meta tag or use fallback
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    
                    // Send request with timeout
                    const fetchPromise = fetch('{{ route("categories.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            CategoryName: categoryName
                        })
                    });
                    
                    const timeoutPromise = new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Request timeout')), 10000)
                    );
                    
                    Promise.race([fetchPromise, timeoutPromise])
                        .then(async response => {
                            const contentType = response.headers.get('content-type');
                            let data;
                            
                            if (contentType && contentType.includes('application/json')) {
                                data = await response.json();
                            } else {
                                const text = await response.text();
                                throw new Error(`Server returned non-JSON response: ${text.substring(0, 100)}`);
                            }
                            
                            if (!response.ok) {
                                const errorMsg = data.message || data.error || `HTTP ${response.status}: ${response.statusText}`;
                                if (data.errors) {
                                    const errorList = Object.values(data.errors).flat().join(', ');
                                    throw new Error(errorList || errorMsg);
                                }
                                throw new Error(errorMsg);
                            }
                            
                            return data;
                        })
                        .then(data => {
                            if (data.CategoryID) {
                                // Add new option to category select
                                const categorySelect = document.getElementById('CategoryID');
                                const newOption = document.createElement('option');
                                newOption.value = data.CategoryID;
                                newOption.textContent = data.CategoryName;
                                newOption.selected = true;
                                categorySelect.appendChild(newOption);
                                
                                // Close modal using instance
                                if (categoryModalInstance) {
                                    categoryModalInstance.hide();
                                }
                                document.getElementById('addCategoryForm').reset();
                                
                                // Show success message
                                showNotification('Category added successfully!', 'success');
                            } else {
                                throw new Error('Invalid response from server: Missing CategoryID');
                            }
                        })
                        .catch(error => {
                            console.error('Error adding category:', error);
                            const errorMessage = error.message || 'Failed to add category. Please try again.';
                            showNotification(errorMessage, 'error');
                        })
                        .finally(() => {
                            isSubmittingCategory = false;
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        });
                });
            }
        });

        // Add Brand Functionality - Optimized with loading protection
        document.addEventListener('DOMContentLoaded', function() {
            const saveBrandBtn = document.getElementById('saveBrandBtn');
            if (saveBrandBtn) {
                saveBrandBtn.addEventListener('click', function() {
                    // Prevent double submission
                    if (isSubmittingBrand) {
                        return;
                    }
                    
                    const brandName = document.getElementById('newBrandName').value.trim();
                    
                    if (!brandName) {
                        showNotification('Please enter a brand name', 'error');
                        return;
                    }

                    isSubmittingBrand = true;
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
                    this.disabled = true;

                    // Get CSRF token from meta tag or use fallback
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    
                    // Send request with timeout
                    const fetchPromise = fetch('{{ route("brands.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            BrandName: brandName
                        })
                    });
                    
                    const timeoutPromise = new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Request timeout')), 10000)
                    );
                    
                    Promise.race([fetchPromise, timeoutPromise])
                        .then(async response => {
                            const contentType = response.headers.get('content-type');
                            let data;
                            
                            if (contentType && contentType.includes('application/json')) {
                                data = await response.json();
                            } else {
                                const text = await response.text();
                                throw new Error(`Server returned non-JSON response: ${text.substring(0, 100)}`);
                            }
                            
                            if (!response.ok) {
                                const errorMsg = data.message || data.error || `HTTP ${response.status}: ${response.statusText}`;
                                if (data.errors) {
                                    const errorList = Object.values(data.errors).flat().join(', ');
                                    throw new Error(errorList || errorMsg);
                                }
                                throw new Error(errorMsg);
                            }
                            
                            return data;
                        })
                        .then(data => {
                            if (data.BrandID) {
                                // Add new option to brand select
                                const brandSelect = document.getElementById('BrandID');
                                const newOption = document.createElement('option');
                                newOption.value = data.BrandID;
                                newOption.textContent = data.BrandName;
                                newOption.selected = true;
                                brandSelect.appendChild(newOption);
                                
                                // Close modal using instance
                                if (brandModalInstance) {
                                    brandModalInstance.hide();
                                }
                                document.getElementById('addBrandForm').reset();
                                
                                // Show success message
                                showNotification('Brand added successfully!', 'success');
                            } else {
                                throw new Error('Invalid response from server: Missing BrandID');
                            }
                        })
                        .catch(error => {
                            console.error('Error adding brand:', error);
                            const errorMessage = error.message || 'Failed to add brand. Please try again.';
                            showNotification(errorMessage, 'error');
                        })
                        .finally(() => {
                            isSubmittingBrand = false;
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        });
                });
            }
        });

        // Form submission with loading state
        document.getElementById('gadgetForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            submitBtn.disabled = true;
            document.body.classList.add('loading');
        });

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

        // Reset modals when closed
        document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('addCategoryForm').reset();
            const btn = document.getElementById('saveCategoryBtn');
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Add Category';
            btn.disabled = false;
        });

        document.getElementById('addBrandModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('addBrandForm').reset();
            const btn = document.getElementById('saveBrandBtn');
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Add Brand';
            btn.disabled = false;
        });
    </script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
</body>
</html>