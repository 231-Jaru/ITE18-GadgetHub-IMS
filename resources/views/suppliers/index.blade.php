<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Suppliers - Gadgethub</title>
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
        
        /* Statistics Cards - Consistent styling */
        .stats-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            z-index: 5;
        }
        
        .stats-card:hover i {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        /* Table Widgets - Consistent styling */
        .table-widget {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table-widget:hover {
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
        
        /* Table styling within widgets */
        .dashboard-widget .table {
            margin-bottom: 0;
        }
        
        .dashboard-widget .table th {
            border-top: none;
            font-weight: 600;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .dashboard-widget .table td {
            border-top: 1px solid #f8f9fa;
            vertical-align: middle;
        }
        
        /* Suppliers-specific enhancements */
        .suppliers-hero {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 12px;
        }
        
        .suppliers-hero h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .suppliers-hero p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Enhanced table styling for suppliers */
        .table-widget .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .table-widget .table td {
            vertical-align: middle;
            border-top: 1px solid #f1f3f4;
        }
        
        .table-widget .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard-widget:hover {
                transform: translateY(-3px) scale(1.01);
            }
            
            .stats-card:hover {
                transform: translateY(-2px) scale(1.02);
            }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <!-- Hero Section -->
        <div class="suppliers-hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1>
                            <i class="fas fa-truck"></i> Supplier Management
                        </h1>
                        <p>Manage your supplier network and vendor relationships</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <a href="/suppliers/create" class="btn btn-light btn-lg" id="addSupplierBtn" style="text-decoration: none;">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-success text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $suppliers->count() }}</h4>
                                <p class="card-text mb-0 small">Total Suppliers</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-truck fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-info text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $suppliers->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                                <p class="card-text mb-0 small">New This Month</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-plus-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-warning text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $suppliers->whereNotNull('Email')->count() }}</h4>
                                <p class="card-text mb-0 small">With Email</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-envelope fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-primary text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $suppliers->whereNotNull('Phone')->count() }}</h4>
                                <p class="card-text mb-0 small">With Phone</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-phone fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Search Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-search"></i> Search Suppliers
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name, contact person, email, phone, ID...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="sortBy">
                                    <option value="name">Sort by Name</option>
                                    <option value="stock">Sort by Stock Quantity</option>
                                    <option value="recent">Sort by Recent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-secondary w-100" id="clearSearch">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-widget table-widget">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table"></i> Supplier Directory
                        </h5>
                        <span class="badge bg-success" id="resultsCount">{{ $suppliers->count() }} suppliers</span>
                    </div>
                    <div class="card-body">
                        @if($suppliers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="suppliersTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Contact Person</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Total Stock</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suppliers as $supplier)
                                        @php
                                            $totalStock = $supplier->stocks->sum('QuantityAdded');
                                        @endphp
                                        <tr class="supplier-row" 
                                            data-id="{{ $supplier->SupplierID }}"
                                            data-name="{{ strtolower($supplier->SupplierName) }}"
                                            data-contact="{{ strtolower($supplier->ContactPerson ?? '') }}"
                                            data-email="{{ strtolower($supplier->Email ?? '') }}"
                                            data-phone="{{ $supplier->Phone ?? '' }}"
                                            data-stock="{{ $totalStock }}"
                                            data-created="{{ $supplier->created_at ? $supplier->created_at->timestamp : 0 }}"
                                            style="cursor: pointer;"
                                            data-href="/suppliers/{{ $supplier->SupplierID }}">
                                            <td>{{ $supplier->SupplierID }}</td>
                                            <td>
                                                <strong>{{ $supplier->SupplierName }}</strong>
                                            </td>
                                            <td>{{ $supplier->ContactPerson ?? 'N/A' }}</td>
                                            <td>{{ $supplier->Email ?? 'N/A' }}</td>
                                            <td>{{ $supplier->Phone ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $totalStock > 0 ? 'info' : 'secondary' }}">{{ $totalStock }}</span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown{{ $supplier->SupplierID }}" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $supplier->SupplierID }}">
                                                        <li>
                                                            <a class="dropdown-item py-2" href="/suppliers/{{ $supplier->SupplierID }}/edit" data-action="edit">
                                                                <i class="fas fa-edit text-warning me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider my-1"></li>
                                                        <li>
                                                            <form action="/suppliers/{{ $supplier->SupplierID }}" method="POST" class="d-inline delete-form" data-item-name="supplier">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger py-2" data-action="delete">
                                                                    <i class="fas fa-trash me-2"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No suppliers found</h5>
                                <p class="text-muted">Start by adding your first supplier.</p>
                                <a href="/suppliers/create" class="btn btn-primary" id="emptyStateAddBtn">
                                    <i class="fas fa-plus"></i> Add Supplier
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        // Search and sort functionality
        let searchTimeout;
        let currentSort = 'name';
        
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFiltersAndSort();
            }, 300);
        });

        // Sort functionality
        document.getElementById('sortBy').addEventListener('change', function() {
            currentSort = this.value;
            applyFiltersAndSort();
        });

        // Clear search
        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('sortBy').value = 'name';
            currentSort = 'name';
            applyFiltersAndSort();
        });

        function applyFiltersAndSort() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const tbody = document.querySelector('#suppliersTable tbody');
            const allRows = Array.from(tbody.querySelectorAll('.supplier-row'));
            let visibleCount = 0;
            
            // First, filter rows
            const filteredRows = allRows.filter(row => {
                const id = row.dataset.id;
                const name = row.dataset.name || '';
                const contact = row.dataset.contact || '';
                const email = row.dataset.email || '';
                const phone = row.dataset.phone || '';
                
                const matches = !searchTerm || 
                    id.includes(searchTerm) ||
                    name.includes(searchTerm) ||
                    contact.includes(searchTerm) ||
                    email.includes(searchTerm) ||
                    phone.includes(searchTerm);
                
                return matches;
            });
            
            // Then, sort filtered rows
            filteredRows.sort((a, b) => {
                let aVal, bVal;
                
                switch(currentSort) {
                    case 'name':
                        aVal = a.dataset.name || '';
                        bVal = b.dataset.name || '';
                        return aVal.localeCompare(bVal);
                    case 'stock':
                        aVal = parseInt(a.dataset.stock) || 0;
                        bVal = parseInt(b.dataset.stock) || 0;
                        return bVal - aVal; // Descending order (highest first)
                    case 'recent':
                        aVal = parseInt(a.dataset.created) || 0;
                        bVal = parseInt(b.dataset.created) || 0;
                        return bVal - aVal; // Descending order (newest first)
                    default:
                        return 0;
                }
            });
            
            // Hide all rows first
            allRows.forEach(row => {
                row.style.display = 'none';
            });
            
            // Show and reorder filtered/sorted rows
            filteredRows.forEach(row => {
                row.style.display = '';
                tbody.appendChild(row);
                visibleCount++;
            });
            
            document.getElementById('resultsCount').textContent = `${visibleCount} supplier${visibleCount !== 1 ? 's' : ''}`;
        }

        // Legacy functions for backward compatibility
        function filterSuppliers() {
            applyFiltersAndSort();
        }

        function sortSuppliers() {
            applyFiltersAndSort();
        }

        // Initialize and fix all buttons
        document.addEventListener('DOMContentLoaded', function() {
            applyFiltersAndSort();
            fixAllSupplierButtons();
        });
        
        // Fix all supplier buttons
        function fixAllSupplierButtons() {
            // Fix Add New Supplier button - ensure correct href and allow natural navigation
            const addSupplierBtn = document.getElementById('addSupplierBtn');
            if (addSupplierBtn) {
                addSupplierBtn.href = '/suppliers/create';
                // Ensure button works as a normal link
                addSupplierBtn.addEventListener('click', function(e) {
                    // Only prevent if there's an issue, otherwise let it navigate naturally
                    if (!addSupplierBtn.href || addSupplierBtn.href === '#' || addSupplierBtn.href === 'javascript:void(0)') {
                        e.preventDefault();
                        window.location.href = '/suppliers/create';
                    }
                });
            }
            
            // Fix empty state Add Supplier button
            const emptyStateBtn = document.getElementById('emptyStateAddBtn');
            if (emptyStateBtn) {
                emptyStateBtn.href = '/suppliers/create';
                // Ensure button works as a normal link
                emptyStateBtn.addEventListener('click', function(e) {
                    // Only prevent if there's an issue, otherwise let it navigate naturally
                    if (!emptyStateBtn.href || emptyStateBtn.href === '#' || emptyStateBtn.href === 'javascript:void(0)') {
                        e.preventDefault();
                        window.location.href = '/suppliers/create';
                    }
                });
            }
            
            // Fix row click handlers
            document.querySelectorAll('.supplier-row').forEach(function(row) {
                if (!row.dataset.clickFixed) {
                    row.dataset.clickFixed = 'true';
                    const href = row.dataset.href;
                    if (href) {
                        row.addEventListener('click', function(e) {
                            // Don't navigate if clicking on dropdown or action buttons
                            if (!e.target.closest('.dropdown') && !e.target.closest('button') && !e.target.closest('a')) {
                                window.location.href = href;
                            }
                        });
                    }
                }
            });
            
            // Fix dropdown action links
            document.querySelectorAll('.dropdown-item[data-action]').forEach(function(link) {
                if (!link.dataset.fixed) {
                    link.dataset.fixed = 'true';
                    link.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const href = this.getAttribute('href');
                        if (href) {
                            window.location.href = href;
                        }
                    });
                }
            });
            
            // Fix delete forms
            document.querySelectorAll('form.delete-form').forEach(function(form) {
                if (!form.dataset.fixed) {
                    form.dataset.fixed = 'true';
                    form.addEventListener('submit', function(e) {
                        const itemName = form.dataset.itemName || 'supplier';
                        if (!confirm('Are you sure you want to delete this ' + itemName + '? This action cannot be undone.')) {
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
