<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gadgets - Inventory System</title>
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

        /* Statistics Cards */
        .stats-card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
        }

        .stats-card .card-body {
            padding: 1.5rem;
            text-align: center;
        }

        .stats-card .icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .stats-card.primary .icon-wrapper {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .stats-card.success .icon-wrapper {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .stats-card.warning .icon-wrapper {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            color: white;
        }

        .stats-card.info .icon-wrapper {
            background: linear-gradient(135deg, var(--secondary-color), #0891b2);
            color: white;
        }

        .stats-card h3 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .stats-card p {
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0;
        }

        /* Search and Filter Section */
        .search-filter-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .search-box {
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            transition: var(--transition);
            font-size: 1rem;
        }

        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .filter-btn {
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            padding: 0.5rem 1.25rem;
            background: white;
            color: #6b7280;
            font-weight: 500;
            transition: var(--transition);
            margin: 0.25rem;
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        /* Gadget Cards */
        .gadget-card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
            overflow: hidden;
        }

        .gadget-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }

        .gadget-image {
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            position: relative;
        }

        .stock-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 10;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .stock-badge.in-stock {
            background: var(--success-color);
            color: white;
        }

        .stock-badge.low-stock {
            background: var(--warning-color);
            color: white;
        }

        .stock-badge.out-of-stock {
            background: var(--danger-color);
            color: white;
        }

        .gadget-card .card-body {
            padding: 1.5rem;
        }

        .gadget-card h5 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .category-badge, .brand-badge {
            border-radius: 50px;
            padding: 0.375rem 0.875rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0.25rem 0.25rem 0.25rem 0;
            display: inline-block;
        }

        .category-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .brand-badge {
            background: linear-gradient(135deg, var(--secondary-color), #0891b2);
            color: white;
        }

        .stats-row {
            background: #f8fafc;
            border-radius: var(--border-radius);
            padding: 1rem;
            margin: 1rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 0.5rem;
        }

        .stat-item h6 {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .stat-item h5 {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 0;
        }

        .stat-item.primary h5 {
            color: var(--primary-color);
        }

        .stat-item.success h5 {
            color: var(--success-color);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .action-buttons .btn-action {
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-action {
            flex: 1;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .gadget-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .gadget-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-view::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-view:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            transform: translateY(-2px);
            color: white;
        }

        .btn-view:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-view i {
            position: relative;
            z-index: 1;
        }

        .btn-view span {
            position: relative;
            z-index: 1;
        }

        .btn-edit {
            background: var(--warning-color);
            color: white;
            border-color: var(--warning-color);
        }

        .btn-edit:hover {
            background: #d97706;
            border-color: #d97706;
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
        }


        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
        }

        .empty-state i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1.5rem;
        }

        .empty-state h4 {
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #9ca3af;
            margin-bottom: 2rem;
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

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                margin-bottom: 0.5rem;
            }
        }

        /* Loading Animation */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <!-- Success/Error Messages -->
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

        <!-- Hero Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="gadgets-hero">
                    @php
                        $deletedCount = \App\Models\Gadgets::onlyTrashed()->count();
                    @endphp
                    @if($deletedCount > 0)
                    <div class="mb-3">
                        <a href="{{ route('gadgets.deleted') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-trash-restore me-1"></i> View Deleted Gadgets ({{ $deletedCount }})
                        </a>
                    </div>
                    @endif
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1>
                                <i class="fas fa-mobile-alt me-3"></i>
                                Gadgets Management
                            </h1>
                            <p>
                                View and manage all your products and inventory items
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <button onclick="openAddGadgetModal()" class="btn btn-light btn-lg px-4 py-3">
                                <i class="fas fa-plus me-2"></i> Add New Gadget
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card stats-card primary">
                    <div class="card-body">
                        <div class="icon-wrapper">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h3>{{ $gadgets->count() }}</h3>
                        <p>Total Gadgets</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card stats-card success">
                    <div class="card-body">
                        <div class="icon-wrapper">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h3>{{ $gadgets->pluck('CategoryID')->unique()->count() }}</h3>
                        <p>Categories</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card stats-card warning">
                    <div class="card-body">
                        <div class="icon-wrapper">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3>{{ $gadgets->pluck('BrandID')->unique()->count() }}</h3>
                        <p>Brands</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card stats-card info">
                    <div class="card-body">
                        <div class="icon-wrapper">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <h3>{{ $gadgets->sum(function($gadget) { return $gadget->stocks ? $gadget->stocks->sum('QuantityAdded') : 0; }) }}</h3>
                        <p>Total Stock</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control search-box" id="searchInput" placeholder="Search gadgets by name, category, or brand...">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex flex-wrap justify-content-lg-end">
                        <button class="btn filter-btn active" data-filter="all">
                            <i class="fas fa-th-large me-1"></i> All
                        </button>
                        @foreach($gadgets->groupBy(function($gadget) { return $gadget->category ? $gadget->category->CategoryName : 'No Category'; }) as $categoryName => $categoryGadgets)
                            <button class="btn filter-btn" data-filter="{{ $categoryName }}">
                                <i class="fas fa-tag me-1"></i> {{ $categoryName }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Gadgets Grid -->
        <div class="row" id="gadgetsGrid">
            @forelse($gadgets as $gadget)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4 gadget-item" 
                 data-category="{{ $gadget->category->CategoryName ?? 'No Category' }}"
                 data-name="{{ strtolower($gadget->GadgetName) }}">
                <div class="card gadget-card h-100" 
                     onclick="viewGadgetDetails({{ $gadget->GadgetID }})" 
                     style="cursor: pointer;"
                     title="Click to view details">
                    <div class="position-relative">
                        <div class="gadget-image">
                            @php
                                $categoryName = strtolower($gadget->category->CategoryName ?? '');
                            @endphp
                            @if(str_contains($categoryName, 'phone') || str_contains($categoryName, 'smartphone'))
                                <i class="fas fa-mobile-alt"></i>
                            @elseif(str_contains($categoryName, 'laptop') || str_contains($categoryName, 'computer'))
                                <i class="fas fa-laptop"></i>
                            @elseif(str_contains($categoryName, 'tablet'))
                                <i class="fas fa-tablet-alt"></i>
                            @elseif(str_contains($categoryName, 'headphone') || str_contains($categoryName, 'audio'))
                                <i class="fas fa-headphones"></i>
                            @elseif(str_contains($categoryName, 'camera'))
                                <i class="fas fa-camera"></i>
                            @elseif(str_contains($categoryName, 'watch') || str_contains($categoryName, 'smartwatch'))
                                <i class="fas fa-clock"></i>
                            @elseif(str_contains($categoryName, 'gaming') || str_contains($categoryName, 'console'))
                                <i class="fas fa-gamepad"></i>
                            @elseif(str_contains($categoryName, 'tv') || str_contains($categoryName, 'television'))
                                <i class="fas fa-tv"></i>
                            @elseif(str_contains($categoryName, 'speaker') || str_contains($categoryName, 'sound'))
                                <i class="fas fa-volume-up"></i>
                            @elseif(str_contains($categoryName, 'keyboard') || str_contains($categoryName, 'mouse'))
                                <i class="fas fa-keyboard"></i>
                            @else
                                <i class="fas fa-mobile-alt"></i>
                            @endif
                        </div>
                        @php
                            $totalStock = $gadget->stocks ? $gadget->stocks->sum('QuantityAdded') : 0;
                        @endphp
                        <div class="stock-badge {{ $totalStock > 10 ? 'in-stock' : ($totalStock > 0 ? 'low-stock' : 'out-of-stock') }}">
                            @if($totalStock > 10)
                                <i class="fas fa-check-circle me-1"></i> In Stock
                            @elseif($totalStock > 0)
                                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
                            @else
                                <i class="fas fa-times-circle me-1"></i> Out of Stock
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <h5>{{ $gadget->GadgetName }}</h5>
                        
                        <div class="mb-3">
                            <span class="category-badge">
                                <i class="fas fa-tag me-1"></i> {{ $gadget->category->CategoryName ?? 'No Category' }}
                            </span>
                            <span class="brand-badge">
                                <i class="fas fa-star me-1"></i> {{ $gadget->brand->BrandName ?? 'No Brand' }}
                            </span>
                        </div>

                        <div class="stats-row">
                            <div class="row">
                                <div class="col-6">
                                    <div class="stat-item primary">
                                        <h6>Stock</h6>
                                        <h5>{{ $totalStock }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item success">
                                        <h6>Cost Price</h6>
                                        @php
                                            // Get the cost price per unit (from first available stock with quantity > 0, or first stock)
                                            $costPrice = 0;
                                            if ($gadget->stocks && $gadget->stocks->count() > 0) {
                                                $availableStock = $gadget->stocks->where('QuantityAdded', '>', 0)->first();
                                                if ($availableStock) {
                                                    $costPrice = $availableStock->CostPricePhp;
                                                } else {
                                                    // If no stock available, use first stock's cost price
                                                    $costPrice = $gadget->stocks->first()->CostPricePhp ?? 0;
                                                }
                                            }
                                        @endphp
                                        <h5>{{ \App\Helpers\CurrencyHelper::formatPhp($costPrice) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="/gadgets/{{ $gadget->GadgetID }}" 
                                    class="btn btn-action btn-view" 
                               onclick="event.stopPropagation(); return true;"
                               title="View {{ $gadget->GadgetName }}">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            <a href="/gadgets/{{ $gadget->GadgetID }}/edit" 
                               class="btn btn-action btn-edit"
                               onclick="event.stopPropagation(); return true;"
                               title="Edit {{ $gadget->GadgetName }}">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4>No gadgets found</h4>
                    <p>Start building your inventory by adding your first gadget.</p>
                    <button onclick="openAddGadgetModal()" class="btn btn-primary btn-lg px-4 py-3">
                        <i class="fas fa-plus me-2"></i> Add First Gadget
                    </button>
                </div>
            </div>
            @endforelse
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // DEFINE ALL GLOBAL FUNCTIONS FIRST (IMMEDIATELY)
        // ============================================
        
        // Track active filter globally
        let activeFilter = 'all';
        
        // Update results count display - Make it globally accessible
        window.updateResultsCount = function(count) {
            let countElement = document.getElementById('resultsCount');
            if (!countElement) {
                countElement = document.createElement('div');
                countElement.id = 'resultsCount';
                countElement.className = 'text-muted small mt-2';
                const searchSection = document.querySelector('.search-filter-section');
                if (searchSection) {
                    searchSection.appendChild(countElement);
                }
            }
            countElement.textContent = `${count} gadget${count !== 1 ? 's' : ''} found`;
        }
        
        // Make applyFilter globally accessible for testing
        window.applyFilter = function(filterValue) {
            activeFilter = filterValue;
            console.log('Applying filter:', activeFilter);
            
            // Get current search term
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            
                const gadgets = document.querySelectorAll('.gadget-item');
                let visibleCount = 0;
                
                gadgets.forEach(gadget => {
                // Get the exact category name from data attribute
                const gadgetCategory = gadget.getAttribute('data-category') || '';
                const gadgetName = gadget.getAttribute('data-name') || '';
                
                // Check if it matches the filter (exact match)
                const matchesFilter = activeFilter === 'all' || gadgetCategory === activeFilter;
                
                // Check if it matches the search term (if any)
                const matchesSearch = !searchTerm || 
                    gadgetName.includes(searchTerm) || 
                    gadgetCategory.toLowerCase().includes(searchTerm);
                
                // Show only if both filter and search match
                if (matchesFilter && matchesSearch) {
                        gadget.style.display = 'block';
                        gadget.style.animation = 'fadeIn 0.3s ease-in-out';
                        visibleCount++;
                    } else {
                        gadget.style.display = 'none';
                    }
                });
            
            console.log('Visible gadgets after filter:', visibleCount);
                
                // Update results count
            window.updateResultsCount(visibleCount);
        };
        
        // ============================================
        // DEFINE ALL GLOBAL FUNCTIONS IMMEDIATELY
        // ============================================
        
        // View gadget details in modal - Optimized to prevent lag
        window.viewGadgetDetails = function(gadgetId) {
            // Prevent multiple simultaneous calls
            if (window.viewGadgetDetails.loading) {
                return;
            }
            
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded!');
                return;
            }
            
            const modalElement = document.getElementById('gadgetDetailsModal');
            if (!modalElement) {
                console.error('Gadget details modal not found!');
                return;
        }

            // Set loading flag
            window.viewGadgetDetails.loading = true;
            
            try {
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
            } catch (error) {
                console.error('Error creating modal:', error);
                window.viewGadgetDetails.loading = false;
                return;
            }
            
            // Show loading state immediately
            const contentEl = document.getElementById('gadgetDetailsContent');
            if (contentEl) {
                contentEl.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading gadget details...</p>
                </div>
            `;
            }
            
            const userType = '{{ session("user_type") ?? "" }}';
            
            // Fetch with timeout
            const fetchPromise = fetch(`/gadgets/${gadgetId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                cache: 'no-cache'
            });
            
            const timeoutPromise = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Request timeout')), 10000)
            );
            
            Promise.race([fetchPromise, timeoutPromise])
            .then(response => {
                    if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    if (data.message && data.message.includes('not found')) {
                    if (contentEl) {
                        contentEl.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> ${data.message}
                            </div>
                        `;
                    }
                        return;
                    }
                    
                    if (data.error) {
                    if (contentEl) {
                        contentEl.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> ${data.error}
                            </div>
                        `;
                    }
                        return;
                    }
                    
                if (window.renderGadgetDetails) {
                    window.renderGadgetDetails(data, userType);
                }
                })
                .catch(error => {
                    console.error('Error fetching gadget details:', error);
                if (contentEl) {
                    contentEl.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> Failed to load gadget details. Please try again.
                            <br><small>Error: ${error.message}</small>
                        </div>
                    `;
                }
            })
            .finally(() => {
                // Clear loading flag after a short delay
                setTimeout(() => {
                    window.viewGadgetDetails.loading = false;
                }, 500);
            });
        };
        
        // Open Add Gadget Modal - Optimized to prevent lag
        window.openAddGadgetModal = function() {
            // Prevent multiple simultaneous calls
            if (window.openAddGadgetModal.loading) {
                return;
            }
            
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded!');
                return;
            }
            
            const modalElement = document.getElementById('addGadgetModal');
            if (!modalElement) {
                console.error('Add gadget modal not found!');
                return;
            }
            
            // Set loading flag
            window.openAddGadgetModal.loading = true;
            
            // Show modal immediately (no waiting for API)
            try {
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Error opening modal:', error);
                window.openAddGadgetModal.loading = false;
                return;
            }
            
            // Show loading state in dropdowns
            const categorySelect = document.getElementById('modalCategoryID');
            const brandSelect = document.getElementById('modalBrandID');
            
            if (categorySelect) {
                categorySelect.innerHTML = '<option value="">Loading categories...</option>';
                categorySelect.disabled = true;
            }
            if (brandSelect) {
                brandSelect.innerHTML = '<option value="">Loading brands...</option>';
                brandSelect.disabled = true;
            }
            
            // Load categories and brands in background
            const fetchPromise = fetch('/gadgets/create', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                cache: 'no-cache'
            });
            
            const timeoutPromise = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Request timeout')), 5000)
            );
            
            Promise.race([fetchPromise, timeoutPromise])
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                } else {
                    return { categories: [], brands: [] };
                }
            })
            .then(data => {
                // Populate category dropdown
                if (categorySelect) {
                    categorySelect.innerHTML = '<option value="">Select Category</option>';
                    categorySelect.disabled = false;
                    if (data.categories && data.categories.length > 0) {
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.CategoryID;
                            option.textContent = category.CategoryName;
                            categorySelect.appendChild(option);
                        });
                    }
                }
                
                // Populate brand dropdown
                if (brandSelect) {
                    brandSelect.innerHTML = '<option value="">Select Brand</option>';
                    brandSelect.disabled = false;
                    if (data.brands && data.brands.length > 0) {
                        data.brands.forEach(brand => {
                            const option = document.createElement('option');
                            option.value = brand.BrandID;
                            option.textContent = brand.BrandName;
                            brandSelect.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading categories/brands:', error);
                // Still allow form to work, just with empty dropdowns
                if (categorySelect) {
                    categorySelect.innerHTML = '<option value="">Select Category</option>';
                    categorySelect.disabled = false;
                }
                if (brandSelect) {
                    brandSelect.innerHTML = '<option value="">Select Brand</option>';
                    brandSelect.disabled = false;
                }
            })
            .finally(() => {
                // Clear loading flag
                setTimeout(() => {
                    window.openAddGadgetModal.loading = false;
                }, 300);
            });
        };
        
        // Global modal instances for reuse
        let categoryModalInstance = null;
        let brandModalInstance = null;
        let isCategoryModalOpening = false;
        let isBrandModalOpening = false;

        // Open Add Category Modal - Optimized for immediate opening
        window.openAddCategoryModal = function() {
            // Prevent multiple simultaneous calls
            if (isCategoryModalOpening) {
                return;
            }
            
            isCategoryModalOpening = true;
            
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded!');
                isCategoryModalOpening = false;
                return;
            }
            
            const modalElement = document.getElementById('addCategoryModalFromGadget');
            if (!modalElement) {
                console.error('Category modal not found!');
                isCategoryModalOpening = false;
                return;
            }
            
            try {
                // Reuse or create modal instance
                if (!categoryModalInstance) {
                    categoryModalInstance = new bootstrap.Modal(modalElement, {
                        backdrop: true,
                        keyboard: true
                    });
                }
                
                // Open modal immediately
                categoryModalInstance.show();
                
                // Reset flag after a short delay
                setTimeout(() => {
                    isCategoryModalOpening = false;
                }, 300);
            } catch (error) {
                console.error('Error opening category modal:', error);
                isCategoryModalOpening = false;
            }
        };
        
        // Open Add Brand Modal - Optimized for immediate opening
        window.openAddBrandModal = function() {
            // Prevent multiple simultaneous calls
            if (isBrandModalOpening) {
                return;
            }
            
            isBrandModalOpening = true;
            
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded!');
                isBrandModalOpening = false;
                return;
            }
            
            const modalElement = document.getElementById('addBrandModalFromGadget');
            if (!modalElement) {
                console.error('Brand modal not found!');
                isBrandModalOpening = false;
                return;
            }
            
            try {
                // Reuse or create modal instance
                if (!brandModalInstance) {
                    brandModalInstance = new bootstrap.Modal(modalElement, {
                        backdrop: true,
                        keyboard: true
                    });
                }
                
                // Open modal immediately
                brandModalInstance.show();
                
                // Reset flag after a short delay
                setTimeout(() => {
                    isBrandModalOpening = false;
                }, 300);
            } catch (error) {
                console.error('Error opening brand modal:', error);
                isBrandModalOpening = false;
            }
        };
        
        // Notification system
        window.showNotification = function(message, type = 'info') {
            const alertClass = type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info';
            const notification = document.createElement('div');
            notification.className = `alert alert-${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        };
        
        // Full implementation of renderGadgetDetails
        window.renderGadgetDetails = function(gadget, userType) {
            const totalStock = gadget.stocks ? gadget.stocks.reduce((sum, stock) => sum + (parseInt(stock.QuantityAdded) || 0), 0) : 0;
            const totalValue = gadget.stocks ? gadget.stocks.reduce((sum, stock) => sum + ((parseInt(stock.QuantityAdded) || 0) * (parseFloat(stock.CostPrice) || 0)), 0) : 0;
            const stockEntries = gadget.stocks ? gadget.stocks.length : 0;
            const suppliers = gadget.stocks ? new Set(gadget.stocks.map(s => s.supplier?.SupplierName).filter(Boolean)).size : 0;
            
            // Get available stock with cost price - ensure it's a number
            const availableStock = gadget.stocks ? gadget.stocks.find(s => (parseInt(s.QuantityAdded) || 0) > 0) : null;
            let costPrice = 0;
            if (availableStock && availableStock.CostPrice) {
                costPrice = parseFloat(availableStock.CostPrice) || 0;
            } else if (gadget.stocks && gadget.stocks.length > 0) {
                // Fallback: get first stock's cost price
                const firstStock = gadget.stocks[0];
                costPrice = parseFloat(firstStock.CostPrice) || 0;
            }
            
            // Ensure costPrice is a number
            costPrice = isNaN(costPrice) ? 0 : costPrice;
            
            // Update modal title
            const gadgetDetailsNameEl = document.getElementById('gadgetDetailsName');
            const modalLabelEl = document.getElementById('gadgetDetailsModalLabel');
            
            if (gadgetDetailsNameEl) {
                gadgetDetailsNameEl.textContent = gadget.GadgetName || 'Gadget Details';
            }
            if (modalLabelEl) {
                modalLabelEl.innerHTML = '<i class="fas fa-mobile-alt me-2"></i> ' + (gadget.GadgetName || 'Gadget Details');
            }
            
            // Update view full page link
            const viewFullPageLink = document.getElementById('viewFullPageLink');
            if (viewFullPageLink) {
                viewFullPageLink.href = `/gadgets/${gadget.GadgetID}`;
                viewFullPageLink.style.display = 'inline-block';
            }
            
            // Render admin view (full information)
            const adminHTML = `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex gap-3 mb-3">
                                <span class="badge" style="background: linear-gradient(45deg, #667eea, #764ba2); padding: 8px 16px; border-radius: 20px;">
                                    <i class="fas fa-tag"></i> ${gadget.category?.CategoryName || 'No Category'}
                                </span>
                                <span class="badge" style="background: linear-gradient(45deg, #f093fb, #f5576c); padding: 8px 16px; border-radius: 20px;">
                                    <i class="fas fa-star"></i> ${gadget.brand?.BrandName || 'No Brand'}
                                </span>
                            </div>
                            <p class="text-muted mb-0"><strong>Gadget ID:</strong> ${gadget.GadgetID}</p>
                        </div>
                    </div>
                    
                    <!-- Admin Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div class="card-body">
                                    <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                                    <h4 class="mb-1">${totalStock}</h4>
                                    <p class="text-muted mb-0">Total Stock</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div class="card-body">
                                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                    <h4 class="mb-1">₱${(parseFloat(totalValue) || 0).toFixed(2)}</h4>
                                    <p class="text-muted mb-0">Total Value</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div class="card-body">
                                    <i class="fas fa-warehouse fa-2x text-info mb-2"></i>
                                    <h4 class="mb-1">${stockEntries}</h4>
                                    <p class="text-muted mb-0">Stock Entries</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div class="card-body">
                                    <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                                    <h4 class="mb-1">${suppliers}</h4>
                                    <p class="text-muted mb-0">Suppliers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Admin Stock Information (Full Details) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-warehouse"></i> Stock Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    ${gadget.stocks && gadget.stocks.length > 0 ? gadget.stocks.map(stock => `
                                        <div class="mb-3 p-3" style="border-left: 4px solid #667eea; background: #f8f9fa; border-radius: 10px;">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <h6 class="mb-1">Quantity: <span class="text-primary">${stock.QuantityAdded || 0}</span></h6>
                                                    <small class="text-muted">Stock ID: ${stock.StockID}</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="mb-1">Cost Price: <span class="text-success">₱${(parseFloat(stock.CostPrice) || 0).toFixed(2)}</span></h6>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="mb-1">Total Value: <span class="text-info">₱${((parseInt(stock.QuantityAdded) || 0) * (parseFloat(stock.CostPrice) || 0)).toFixed(2)}</span></h6>
                                                    <small class="text-muted">Supplier: ${stock.supplier?.SupplierName || 'Unknown'}</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="badge bg-${(parseInt(stock.QuantityAdded) || 0) > 10 ? 'success' : ((parseInt(stock.QuantityAdded) || 0) > 0 ? 'warning' : 'danger')}">
                                                        ${(parseInt(stock.QuantityAdded) || 0) > 10 ? 'In Stock' : ((parseInt(stock.QuantityAdded) || 0) > 0 ? 'Low Stock' : 'Out of Stock')}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('') : `
                                        <div class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No stock information available</h5>
                                            <p class="text-muted">This gadget hasn't been added to stock yet.</p>
                                        </div>
                                    `}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('gadgetDetailsContent').innerHTML = adminHTML;
        };

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure Bootstrap is loaded before executing scripts
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded!');
                return;
            }

            // Enhanced Search functionality with debouncing
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.toLowerCase();
                        const gadgets = document.querySelectorAll('.gadget-item');
                        let visibleCount = 0;
                        
                        gadgets.forEach(gadget => {
                            const gadgetName = gadget.dataset.name;
                            const category = gadget.dataset.category;
                            
                            // First check if it matches the active filter
                            const matchesFilter = activeFilter === 'all' || category === activeFilter;
                            
                            // Then check if it matches the search term
                            const matchesSearch = !searchTerm || 
                                gadgetName.includes(searchTerm) || 
                                category.toLowerCase().includes(searchTerm);
                            
                            if (matchesFilter && matchesSearch) {
                                gadget.style.display = 'block';
                                gadget.style.animation = 'fadeIn 0.3s ease-in-out';
                                visibleCount++;
                            } else {
                                gadget.style.display = 'none';
                            }
                        });
                        
                        // Update results count
                        window.updateResultsCount(visibleCount);
                    }, 300);
                });
            }

            // Enhanced Filter functionality - Simple and direct
            const filterButtons = document.querySelectorAll('.filter-btn');
            console.log('Filter buttons found:', filterButtons.length);
            
            filterButtons.forEach((btn) => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const filterValue = this.getAttribute('data-filter');
                    console.log('Filter clicked:', filterValue);
                    
                    if (!filterValue) {
                        console.error('No filter value!');
                        return false;
        }

                    // Update active filter
                    activeFilter = filterValue;
                    
                    // Update button active states
                    filterButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Get search term
                    const searchInput = document.getElementById('searchInput');
                    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                    
                    // Filter gadgets
                    const gadgets = document.querySelectorAll('.gadget-item');
                    let visibleCount = 0;
                    
                    gadgets.forEach(gadget => {
                        const category = gadget.getAttribute('data-category') || '';
                        const name = gadget.getAttribute('data-name') || '';
                        
                        const matchesFilter = activeFilter === 'all' || category === activeFilter;
                        const matchesSearch = !searchTerm || 
                            name.includes(searchTerm) || 
                            category.toLowerCase().includes(searchTerm);
                        
                        if (matchesFilter && matchesSearch) {
                            gadget.style.display = 'block';
                            visibleCount++;
                        } else {
                            gadget.style.display = 'none';
                        }
                    });
                    
                    console.log('Filter:', activeFilter, 'Visible:', visibleCount);
                    window.updateResultsCount(visibleCount);
                    
                    return false;
                });
            });

            // Initialize page animations and tooltips
            // Add loading animation to cards
            const cards = document.querySelectorAll('.gadget-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.style.animation = 'fadeIn 0.6s ease-out forwards';
            });
            
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Add fade-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);

        // Save Gadget Button - Set up after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const saveGadgetBtn = document.getElementById('saveGadgetBtn');
            if (saveGadgetBtn) {
                saveGadgetBtn.addEventListener('click', function() {
                const form = document.getElementById('addGadgetForm');
                const formData = new FormData(form);
                
                // Validate required fields
                if (!formData.get('GadgetName') || !formData.get('CategoryID') || !formData.get('BrandID')) {
                    showNotification('Please fill in all required fields', 'error');
                    return;
                }

                // Show loading state
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                this.disabled = true;

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Send AJAX request
                fetch('/gadgets', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: formData
                })
                .then(async response => {
                    // Handle authentication errors (401)
                    if (response.status === 401) {
                        const data = await response.json().catch(() => ({}));
                        showNotification(data.message || data.error || 'Your session has expired. Please login again.', 'error');
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 2000);
                        return;
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (!response.ok) {
                            if (data.errors) {
                                const errorList = Object.values(data.errors).flat().join(', ');
                                throw new Error(errorList);
                            }
                            throw new Error(data.message || data.error || `HTTP ${response.status}`);
                        }
                        return data;
                    } else {
                        if (response.ok) {
                            return { success: true, message: 'Gadget created successfully!' };
                        }
                        const text = await response.text();
                        throw new Error(`Server error: ${text.substring(0, 100)}`);
                    }
                })
                .then(data => {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('addGadgetModal')).hide();
                    
                    // Show success message
                    showNotification('Gadget created successfully!', 'success');
                    
                    // Reload page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to create gadget: ' + error.message, 'error');
                })
                .finally(() => {
                    // Reset button state
                    this.innerHTML = '<i class="fas fa-save me-2"></i> Create Gadget';
                    this.disabled = false;
                });
            });
        }
        });


        // Save Category from Add Gadget Modal - Optimized with loading protection
        document.addEventListener('DOMContentLoaded', function() {
            const saveCategoryBtn = document.getElementById('saveCategoryBtnFromGadget');
            let isSubmittingCategory = false;
            
            if (saveCategoryBtn) {
                saveCategoryBtn.addEventListener('click', function() {
                    // Prevent double submission
                    if (isSubmittingCategory) {
                        return;
                    }
                    
                const categoryName = document.getElementById('newCategoryNameFromGadget').value.trim();
                
                if (!categoryName) {
                        showNotification('Please enter a category name', 'error');
                    return;
                }

                    isSubmittingCategory = true;
                    const originalHTML = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
                this.disabled = true;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                    if (!csrfToken) {
                        showNotification('CSRF token not found. Please refresh the page.', 'error');
                        isSubmittingCategory = false;
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                        return;
                    }
                    
                    // Send request with timeout
                    const fetchPromise = fetch('/categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ CategoryName: categoryName })
                    });
                    
                    const timeoutPromise = new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Request timeout')), 10000)
                    );
                    
                    Promise.race([fetchPromise, timeoutPromise])
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || data.error || 'Failed to add category');
                        }
                        return data;
                    } else {
                        throw new Error('Invalid response from server');
                    }
                })
                .then(data => {
                    if (data.CategoryID) {
                        const categorySelect = document.getElementById('modalCategoryID');
                        if (categorySelect) {
                            const newOption = document.createElement('option');
                            newOption.value = data.CategoryID;
                            newOption.textContent = data.CategoryName;
                            newOption.selected = true;
                            categorySelect.appendChild(newOption);
                        }
                        
                                // Close modal using instance
                                if (categoryModalInstance) {
                                    categoryModalInstance.hide();
                                }
                        document.getElementById('addCategoryFormFromGadget').reset();
                        showNotification('Category added successfully!', 'success');
                            } else {
                                throw new Error('Invalid response: Missing CategoryID');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to add category: ' + error.message, 'error');
                })
                .finally(() => {
                            isSubmittingCategory = false;
                            this.innerHTML = originalHTML;
                    this.disabled = false;
                });
            });
        }
        });

        // Save Brand from Add Gadget Modal - Optimized with loading protection
        document.addEventListener('DOMContentLoaded', function() {
            const saveBrandBtn = document.getElementById('saveBrandBtnFromGadget');
            let isSubmittingBrand = false;
            
            if (saveBrandBtn) {
                saveBrandBtn.addEventListener('click', function() {
                    // Prevent double submission
                    if (isSubmittingBrand) {
                        return;
                    }
                    
                const brandName = document.getElementById('newBrandNameFromGadget').value.trim();
                
                if (!brandName) {
                        showNotification('Please enter a brand name', 'error');
                    return;
                }

                    isSubmittingBrand = true;
                    const originalHTML = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
                this.disabled = true;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                    if (!csrfToken) {
                        showNotification('CSRF token not found. Please refresh the page.', 'error');
                        isSubmittingBrand = false;
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                        return;
                    }
                    
                    // Send request with timeout
                    const fetchPromise = fetch('/brands', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ BrandName: brandName })
                    });
                    
                    const timeoutPromise = new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Request timeout')), 10000)
                    );
                    
                    Promise.race([fetchPromise, timeoutPromise])
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || data.error || 'Failed to add brand');
                        }
                        return data;
                    } else {
                        throw new Error('Invalid response from server');
                    }
                })
                .then(data => {
                    if (data.BrandID) {
                        const brandSelect = document.getElementById('modalBrandID');
                        if (brandSelect) {
                            const newOption = document.createElement('option');
                            newOption.value = data.BrandID;
                            newOption.textContent = data.BrandName;
                            newOption.selected = true;
                            brandSelect.appendChild(newOption);
                        }
                        
                                // Close modal using instance
                                if (brandModalInstance) {
                                    brandModalInstance.hide();
                                }
                        document.getElementById('addBrandFormFromGadget').reset();
                        showNotification('Brand added successfully!', 'success');
                            } else {
                                throw new Error('Invalid response: Missing BrandID');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to add brand: ' + error.message, 'error');
                })
                .finally(() => {
                            isSubmittingBrand = false;
                            this.innerHTML = originalHTML;
                    this.disabled = false;
                });
            });
        }
        });

        // Reset Add Gadget Modal when closed - Set up after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const addGadgetModal = document.getElementById('addGadgetModal');
            if (addGadgetModal) {
                addGadgetModal.addEventListener('hidden.bs.modal', function(e) {
                    // Stop any propagation
                    e.stopPropagation();
                    
                    // Reset form
                const form = document.getElementById('addGadgetForm');
                    if (form) {
                        form.reset();
                    }
                    
                    // Reset button state
                const btn = document.getElementById('saveGadgetBtn');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-save me-2"></i> Create Gadget';
                    btn.disabled = false;
                }
                    
                    // Clear any validation errors
                    const errorElements = form?.querySelectorAll('.is-invalid');
                    if (errorElements) {
                        errorElements.forEach(el => el.classList.remove('is-invalid'));
                    }
            });
        }
        });
    </script>

    <!-- Gadget Details Modal -->
    <div class="modal fade" id="gadgetDetailsModal" tabindex="-1" aria-labelledby="gadgetDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title fw-bold" id="gadgetDetailsModalLabel">
                        <i class="fas fa-info-circle me-2"></i> <span id="gadgetDetailsName">Product Details</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="gadgetDetailsContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading product details...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <a href="#" id="viewFullPageLink" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> View Full Page
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Gadget Modal -->
    <div class="modal fade" id="addGadgetModal" tabindex="-1" aria-labelledby="addGadgetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white;">
                    <h5 class="modal-title fw-bold" id="addGadgetModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> Add New Gadget
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addGadgetForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="modalGadgetName" class="form-label">
                                <i class="fas fa-mobile-alt me-2"></i>Gadget Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="modalGadgetName" name="GadgetName" 
                                   placeholder="Enter gadget name (e.g., iPhone 15 Pro, MacBook Air M2)" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modalCategoryID" class="form-label">
                                    <i class="fas fa-tags me-2"></i>Category <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <select class="form-select" id="modalCategoryID" name="CategoryID" required>
                                        <option value="">Select Category</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-success" onclick="openAddCategoryModal()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modalBrandID" class="form-label">
                                    <i class="fas fa-award me-2"></i>Brand <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <select class="form-select" id="modalBrandID" name="BrandID" required>
                                        <option value="">Select Brand</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-success" onclick="openAddBrandModal()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="modalReorderPoint" class="form-label">
                                <i class="fas fa-bell me-2"></i>Low Stock Alert Threshold
                            </label>
                            <input type="number" class="form-control" id="modalReorderPoint" name="ReorderPoint" 
                                   value="10" min="0" placeholder="Enter number (e.g., 10)">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> You'll get an alert when stock falls below this number (default: 10)
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="event.stopPropagation();">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveGadgetBtn">
                        <i class="fas fa-save me-2"></i> Create Gadget
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal (for use in Add Gadget Modal) -->
    <div class="modal fade" id="addCategoryModalFromGadget" tabindex="-1" aria-labelledby="addCategoryModalFromGadgetLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addCategoryModalFromGadgetLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryFormFromGadget">
                        <div class="mb-3">
                            <label for="newCategoryNameFromGadget" class="form-label">
                                <i class="fas fa-tag me-2"></i>Category Name
                            </label>
                            <input type="text" class="form-control" id="newCategoryNameFromGadget" name="CategoryName" 
                                   placeholder="Enter category name (e.g., Smartphone, Laptop, Tablet)" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveCategoryBtnFromGadget">
                        <i class="fas fa-save me-2"></i>Add Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Brand Modal (for use in Add Gadget Modal) -->
    <div class="modal fade" id="addBrandModalFromGadget" tabindex="-1" aria-labelledby="addBrandModalFromGadgetLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addBrandModalFromGadgetLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Brand
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBrandFormFromGadget">
                        <div class="mb-3">
                            <label for="newBrandNameFromGadget" class="form-label">
                                <i class="fas fa-award me-2"></i>Brand Name
                            </label>
                            <input type="text" class="form-control" id="newBrandNameFromGadget" name="BrandName" 
                                   placeholder="Enter brand name (e.g., Apple, Samsung, Sony)" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveBrandBtnFromGadget">
                        <i class="fas fa-save me-2"></i>Add Brand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        // Verify all functions are loaded and add fallback event listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded. Checking functions...');
            console.log('viewGadgetDetails:', typeof window.viewGadgetDetails);
            console.log('openAddGadgetModal:', typeof window.openAddGadgetModal);
            console.log('openAddCategoryModal:', typeof window.openAddCategoryModal);
            console.log('openAddBrandModal:', typeof window.openAddBrandModal);
            console.log('Bootstrap:', typeof bootstrap);
            
            // Add fallback event listeners for buttons with onclick handlers
            // This ensures buttons work even if onclick doesn't fire
            // Functions are now defined at the top, so they should always be available
            setTimeout(function() {
                document.querySelectorAll('[onclick*="viewGadgetDetails"]').forEach(btn => {
                    const onclick = btn.getAttribute('onclick');
                    const match = onclick.match(/viewGadgetDetails\((\d+)\)/);
                    if (match && match[1]) {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            if (window.viewGadgetDetails && typeof window.viewGadgetDetails === 'function') {
                                window.viewGadgetDetails(parseInt(match[1]));
                            } else {
                                console.error('viewGadgetDetails function not found!');
                            }
                        });
                    }
                });
                
                document.querySelectorAll('[onclick*="openAddGadgetModal"]').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (window.openAddGadgetModal && typeof window.openAddGadgetModal === 'function') {
                            window.openAddGadgetModal();
                        } else {
                            console.error('openAddGadgetModal function not found!');
                        }
                    });
                });
                
                document.querySelectorAll('[onclick*="openAddCategoryModal"]').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (window.openAddCategoryModal && typeof window.openAddCategoryModal === 'function') {
                            window.openAddCategoryModal();
                        } else {
                            console.error('openAddCategoryModal function not found!');
                        }
                    });
                });
                
                document.querySelectorAll('[onclick*="openAddBrandModal"]').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (window.openAddBrandModal && typeof window.openAddBrandModal === 'function') {
                            window.openAddBrandModal();
                        } else {
                            console.error('openAddBrandModal function not found!');
                        }
                    });
                });
            }, 100); // Small delay to ensure all functions are defined
        });
    </script>
</body>
</html>