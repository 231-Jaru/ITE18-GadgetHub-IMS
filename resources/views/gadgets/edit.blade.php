<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $gadget->GadgetName }} - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-submit {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .gadget-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <div class="text-center mb-4">
                        <h1 class="mb-0">
                            <i class="fas fa-edit"></i> Edit Gadget
                        </h1>
                        <p class="mb-0">Update gadget information</p>
                    </div>

                    <!-- Current Gadget Info -->
                    <div class="gadget-info">
                        <h5 class="mb-3">
                            <i class="fas fa-mobile-alt"></i> {{ $gadget->GadgetName }}
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Category:</strong> {{ $gadget->category->CategoryName ?? 'No Category' }}</p>
                                <p class="mb-0"><strong>Brand:</strong> {{ $gadget->brand->BrandName ?? 'No Brand' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total Stock:</strong> {{ $gadget->stocks->sum('QuantityAdded') }}</p>
                                <p class="mb-0"><strong>Stock Entries:</strong> {{ $gadget->stocks->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="/gadgets/{{ $gadget->GadgetID }}" id="editGadgetForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="GadgetName" class="form-label">
                                    <i class="fas fa-mobile-alt me-2"></i>Gadget Name
                                </label>
                                <input type="text" class="form-control @error('GadgetName') is-invalid @enderror" 
                                       id="GadgetName" name="GadgetName" value="{{ old('GadgetName', $gadget->GadgetName) }}" 
                                       placeholder="Enter gadget name" required>
                                @error('GadgetName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="CategoryID" class="form-label">
                                    <i class="fas fa-tags me-2"></i>Category
                                </label>
                                <select class="form-select @error('CategoryID') is-invalid @enderror" 
                                        id="CategoryID" name="CategoryID" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->CategoryID }}" 
                                                {{ old('CategoryID', $gadget->CategoryID) == $category->CategoryID ? 'selected' : '' }}>
                                            {{ $category->CategoryName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('CategoryID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="BrandID" class="form-label">
                                    <i class="fas fa-star me-2"></i>Brand
                                </label>
                                <select class="form-select @error('BrandID') is-invalid @enderror" 
                                        id="BrandID" name="BrandID" required>
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->BrandID }}" 
                                                {{ old('BrandID', $gadget->BrandID) == $brand->BrandID ? 'selected' : '' }}>
                                            {{ $brand->BrandName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('BrandID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Low Stock Alert -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="ReorderPoint" class="form-label">
                                    <i class="fas fa-bell me-2"></i>Low Stock Alert Level
                                </label>
                                <input type="number" class="form-control @error('ReorderPoint') is-invalid @enderror" 
                                       id="ReorderPoint" name="ReorderPoint" 
                                       value="{{ old('ReorderPoint', $gadget->ReorderPoint ?? 10) }}" 
                                       min="0" placeholder="Alert me when stock goes below this number">
                                <small class="form-text text-white-50">
                                    <i class="fas fa-info-circle"></i> You'll get an alert when stock falls below this number (default: 10)
                                </small>
                                @error('ReorderPoint')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-end">
                                    <a href="/gadgets/{{ $gadget->GadgetID }}" 
                                       class="btn btn-light"
                                       type="button">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <a href="/gadgets" 
                                       class="btn btn-outline-light"
                                       type="button">
                                        <i class="fas fa-arrow-left me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-submit" id="updateGadgetBtn">
                                        <i class="fas fa-save me-1"></i> Update Gadget
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form - Separate from update form -->
                    <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                        <form method="POST" action="/gadgets/{{ $gadget->GadgetID }}" class="d-inline delete-form" 
                              data-item-name="{{ $gadget->GadgetName }}" id="deleteGadgetForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Delete Gadget
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get forms and buttons
            const editForm = document.getElementById('editGadgetForm');
            const deleteForm = document.getElementById('deleteGadgetForm');
            const updateBtn = document.getElementById('updateGadgetBtn');
            const viewBtn = document.querySelector('a[href*="/gadgets/"]:not([href*="/edit"])');
            const cancelBtn = document.querySelector('a[href="/gadgets"]');
            
            let isSubmitting = false;

            // Handle delete form with confirmation
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    // Stop propagation to prevent affecting other forms
                    e.stopPropagation();
                    
                    // Get gadget name from data attribute
                    const gadgetName = this.getAttribute('data-item-name') || 'gadget';
                    
                    // Show confirmation dialog with clear message
                    const confirmed = confirm(
                        'Are you sure you want to delete the gadget "' + gadgetName + '"?\n\n' +
                        'This action cannot be undone. The gadget will be moved to the deleted gadgets page where you can restore it later.\n\n' +
                        'Click OK to delete, or Cancel to keep the gadget.'
                    );
                    
                    // If user cancels, prevent form submission
                    if (!confirmed) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                    
                    // User confirmed, allow form to submit
                    return true;
                });
            }

            // Update form submission - Optimized to prevent lag
            if (editForm && updateBtn) {
                editForm.addEventListener('submit', function(e) {
                    // Prevent double submission
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }

                    // Ensure we're submitting the UPDATE form, not DELETE
                    const formMethod = this.querySelector('input[name="_method"]');
                    if (formMethod && formMethod.value !== 'PUT') {
                        e.preventDefault();
                        console.error('Form method mismatch!');
                        return false;
                    }

                    isSubmitting = true;
                    
                    // Show loading state immediately
                    const originalHTML = updateBtn.innerHTML;
                    updateBtn.disabled = true;
                    updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                    // Re-enable after 10 seconds as fallback (in case of error)
                    setTimeout(function() {
                        if (isSubmitting) {
                            isSubmitting = false;
                            updateBtn.disabled = false;
                            updateBtn.innerHTML = originalHTML;
                        }
                    }, 10000);
                });
            }

            // Navigation buttons - immediate navigation, no lag
            if (viewBtn) {
                viewBtn.addEventListener('click', function(e) {
                    // Allow natural navigation
                    return true;
                });
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    // Allow natural navigation
                    return true;
                });
            }
        });
    </script>
</body>
</html>