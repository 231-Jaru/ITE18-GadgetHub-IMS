<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Stock Adjustment - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .page-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            padding: 10px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1"><i class="fas fa-adjust me-2"></i>Create Stock Adjustment</h1>
                    <p class="mb-0 opacity-75">Add, remove, or set stock quantity</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/stock-adjustments" class="btn btn-light">
                        <i class="fas fa-history me-2"></i>View Adjustments
                    </a>
                    <a href="/stocks" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Stocks
                    </a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="form-container">
                    <div class="info-box">
                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>What is a Stock Adjustment?</h6>
                        <p class="mb-0 small">
                            <strong>Increase:</strong> Add stock (found items, returns)<br>
                            <strong>Decrease:</strong> Remove stock (damage, theft, write-offs)<br>
                            <strong>Set:</strong> Set to exact number (corrections after physical count)
                        </p>
                    </div>

                    <form method="POST" action="/stock-adjustments">
                        @csrf

                        <div class="mb-3">
                            <label for="GadgetID" class="form-label">
                                <i class="fas fa-mobile-alt me-2"></i>Gadget <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('GadgetID') is-invalid @enderror" 
                                    id="GadgetID" name="GadgetID" required>
                                <option value="">Select a gadget</option>
                                @foreach($gadgets as $gadget)
                                    <option value="{{ $gadget->GadgetID }}" 
                                            {{ old('GadgetID', $selectedGadget ?? '') == $gadget->GadgetID ? 'selected' : '' }}>
                                        {{ $gadget->GadgetName }}
                                        @if($gadget->category)
                                            - {{ $gadget->category->CategoryName }}
                                        @endif
                                        (Current Stock: {{ $gadget->stocks ? $gadget->stocks->sum('QuantityAdded') : 0 }})
                                    </option>
                                @endforeach
                            </select>
                            @error('GadgetID')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="AdjustmentType" class="form-label">
                                <i class="fas fa-exchange-alt me-2"></i>Adjustment Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('AdjustmentType') is-invalid @enderror" 
                                    id="AdjustmentType" name="AdjustmentType" required>
                                <option value="">Select type</option>
                                <option value="INCREASE" {{ old('AdjustmentType') == 'INCREASE' ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-up"></i> Increase (Add Stock)
                                </option>
                                <option value="DECREASE" {{ old('AdjustmentType') == 'DECREASE' ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-down"></i> Decrease (Remove Stock)
                                </option>
                                <option value="SET" {{ old('AdjustmentType') == 'SET' ? 'selected' : '' }}>
                                    <i class="fas fa-equals"></i> Set (Set to Exact Number)
                                </option>
                            </select>
                            @error('AdjustmentType')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="QuantityChanged" class="form-label">
                                <i class="fas fa-hashtag me-2"></i>Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('QuantityChanged') is-invalid @enderror" 
                                   id="QuantityChanged" 
                                   name="QuantityChanged" 
                                   value="{{ old('QuantityChanged') }}" 
                                   min="0" 
                                   required
                                   placeholder="Enter quantity">
                            <small class="form-text text-muted" id="quantityHelp">
                                <span id="quantityHelpText">Enter the amount to add or remove</span>
                            </small>
                            @error('QuantityChanged')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="Reason" class="form-label">
                                <i class="fas fa-comment me-2"></i>Reason (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('Reason') is-invalid @enderror" 
                                   id="Reason" 
                                   name="Reason" 
                                   value="{{ old('Reason') }}" 
                                   placeholder="e.g., Damaged items, Found items, Physical count correction">
                            @error('Reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="Notes" class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>Notes (Optional)
                            </label>
                            <textarea class="form-control @error('Notes') is-invalid @enderror" 
                                      id="Notes" 
                                      name="Notes" 
                                      rows="3" 
                                      placeholder="Additional details...">{{ old('Notes') }}</textarea>
                            @error('Notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/stocks" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Adjustment
                            </button>
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
            // Prevent cancel button from submitting form
            const cancelBtn = document.querySelector('a.btn-secondary');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (href) {
                        window.location.href = href;
                    }
                });
            }

            // Add loading state to submit button
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                    }
                });
            }

            // Update help text based on adjustment type
            const adjustmentTypeSelect = document.getElementById('AdjustmentType');
            const quantityInput = document.getElementById('QuantityChanged');
            const quantityHelpText = document.getElementById('quantityHelpText');

            if (adjustmentTypeSelect && quantityInput && quantityHelpText) {
                adjustmentTypeSelect.addEventListener('change', function() {
                    const type = this.value;
                    if (type === 'INCREASE') {
                        quantityHelpText.textContent = 'Enter the amount to add to current stock';
                        quantityInput.min = 1;
                    } else if (type === 'DECREASE') {
                        quantityHelpText.textContent = 'Enter the amount to remove from current stock';
                        quantityInput.min = 1;
                    } else if (type === 'SET') {
                        quantityHelpText.textContent = 'Enter the final quantity you want (can be 0)';
                        quantityInput.min = 0;
                    } else {
                        quantityHelpText.textContent = 'Enter the amount to add or remove';
                        quantityInput.min = 0;
                    }
                });
            }

            // Handle gadget parameter from URL (e.g., ?gadget=123)
            const urlParams = new URLSearchParams(window.location.search);
            const gadgetId = urlParams.get('gadget');
            if (gadgetId) {
                const gadgetSelect = document.getElementById('GadgetID');
                if (gadgetSelect) {
                    gadgetSelect.value = gadgetId;
                    // Trigger change event to update any dependent fields
                    gadgetSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
</body>
</html>

