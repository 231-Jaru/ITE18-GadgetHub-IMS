<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Supplier - Gadgethub</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            color: var(--success-color);
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
            border-color: var(--success-color);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: var(--transition);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
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
                                <i class="fas fa-plus-circle me-3"></i>
                                Add New Supplier
                            </h1>
                            <p>Create a new supplier and connect it to your inventory system</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="/suppliers" class="btn btn-light btn-lg px-4 py-3" id="backToSuppliersBtn">
                                <i class="fas fa-arrow-left me-2"></i> Back to Suppliers
                            </a>
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

                    <form action="/suppliers" method="POST">
                        @csrf
                        
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
                                           value="{{ old('SupplierName') }}" 
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
                                           value="{{ old('ContactPerson') }}" 
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
                                           value="{{ old('Phone') }}" 
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
                                           value="{{ old('Email') }}" 
                                           placeholder="supplier@example.com">
                                    @error('Email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional - must be unique if provided</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="/suppliers" class="btn btn-outline-secondary" id="cancelBtn">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Create Supplier
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
            
            // Prevent cancel button from submitting form
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.setAttribute('href', '/suppliers');
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.location.href = '/suppliers';
                    return false;
                }, true);
            }

            // Add loading state to submit button
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                    }
                });
            }
        });
    </script>
</body>
</html>
