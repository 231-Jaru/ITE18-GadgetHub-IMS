<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Gadgethub</title>
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
            cursor: pointer;
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
        
        /* Transactions-specific enhancements */
        .transactions-hero {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 12px;
        }
        
        .transactions-hero h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .transactions-hero p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Enhanced table styling for transactions */
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
        <div class="transactions-hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1>
                            <i class="fas fa-receipt"></i> Transaction Management
                        </h1>
                        <p>Track and manage all sales transactions and customer orders</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <i class="fas fa-cash-register fa-5x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-primary text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $transactions->count() }}</h4>
                                <p class="card-text mb-0 small">Total Transactions</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-receipt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card bg-success text-white stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">₱{{ number_format($transactions->sum(function($t) { return $t->Quantity * ($t->stock->CostPrice ?? 0); }), 2) }}</h4>
                                <p class="card-text mb-0 small">Total Cost Value</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
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
                                <h4 class="card-title mb-1">{{ $transactions->where('TransactionDate', '>=', now()->startOfMonth())->count() }}</h4>
                                <p class="card-text mb-0 small">This Month</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
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
                                <h4 class="card-title mb-1">{{ $transactions->where('TransactionDate', '>=', now()->startOfDay())->count() }}</h4>
                                <p class="card-text mb-0 small">Today</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-4">
                    <h2>
                        <i class="fas fa-list"></i> Transaction History
                    </h2>
                    <p class="text-muted mb-0">Transactions are automatically created when stock is added, purchase orders are received, or adjustments are made.</p>
                </div>

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

                <!-- Search and Filter Section -->
                <div class="card mb-4 dashboard-widget">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-search"></i> Search & Filter
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="searchInput" class="form-label">Search Transactions</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search by buyer, gadget, ID...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="type" class="form-label">Transaction Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>IN (Stock In)</option>
                                    <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>OUT (Sale)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-primary me-2" id="applyFilters">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-widget table-widget">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table"></i> Transaction History
                        </h5>
                        <span class="badge bg-primary" id="resultsCount">{{ $transactions->count() }} transactions</span>
                    </div>
                    <div class="card-body">
                        @if($transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="transactionsTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Buyer</th>
                                            <th>Gadget</th>
                                            <th>Quantity</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                        <tr class="transaction-row" 
                                            data-id="{{ $transaction->TransactionID }}"
                                            data-buyer="{{ strtolower($transaction->buyer->BuyerName ?? '') }}"
                                            data-gadget="{{ strtolower($transaction->gadget->GadgetName ?? '') }}"
                                            data-type="{{ $transaction->TransactionType ?? '' }}"
                                            data-date="{{ $transaction->TransactionDate ? $transaction->TransactionDate->format('Y-m-d') : '' }}">
                                            <td>{{ $transaction->TransactionID }}</td>
                                            <td>{{ $transaction->TransactionDate ? $transaction->TransactionDate->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->TransactionType == 'OUT' ? 'success' : 'info' }}">
                                                    {{ $transaction->TransactionType }}
                                                </span>
                                            </td>
                                            <td>{{ $transaction->buyer->BuyerName ?? 'N/A' }}</td>
                                            <td>{{ $transaction->gadget->GadgetName ?? 'N/A' }}</td>
                                            <td>{{ $transaction->Quantity }}</td>
                                            <td><strong>{{ \App\Helpers\CurrencyHelper::formatPhp($transaction->Quantity * ($transaction->stock ? \App\Helpers\CurrencyHelper::getPhpPrice($transaction->stock->CostPrice) : 0)) }}</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->TransactionType == 'OUT' ? 'success' : 'info' }}">
                                                    {{ $transaction->TransactionType == 'OUT' ? 'Sale' : 'Restock' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('transactions.show', $transaction->TransactionID) }}" class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('transactions.edit', $transaction->TransactionID) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('transactions.destroy', $transaction->TransactionID) }}" method="POST" class="d-inline delete-form" data-item-name="transaction" onsubmit="return confirm('Are you sure you want to delete this transaction? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($transactions->hasPages())
                                <div class="card-footer bg-light py-3">
                                    @include('partials.simple-pagination', ['paginator' => $transactions])
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No transactions found</h5>
                                <p class="text-muted">Transactions are automatically created when you add stock, receive purchase orders, or make adjustments.</p>
                                <div class="mt-3">
                                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary me-2">
                                        <i class="fas fa-shopping-cart me-1"></i> Create Purchase Order
                                    </a>
                                    <a href="{{ route('stocks.index') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-boxes me-1"></i> View Stocks
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterTransactions();
            }, 300);
        });

        // Filter functionality
        document.getElementById('applyFilters').addEventListener('click', function() {
            filterTransactions();
        });

        // Auto-filter on select change
        document.getElementById('type').addEventListener('change', filterTransactions);
        document.getElementById('buyer').addEventListener('change', filterTransactions);
        document.getElementById('date_from').addEventListener('change', filterTransactions);
        document.getElementById('date_to').addEventListener('change', filterTransactions);

        function filterTransactions() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('type').value;
            const buyerFilter = document.getElementById('buyer').value;
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            
            const rows = document.querySelectorAll('.transaction-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const id = row.dataset.id;
                const buyer = row.dataset.buyer || '';
                const gadget = row.dataset.gadget || '';
                const type = row.dataset.type || '';
                const date = row.dataset.date || '';
                
                // Search filter
                const matchesSearch = !searchTerm || 
                    id.includes(searchTerm) ||
                    buyer.includes(searchTerm) ||
                    gadget.includes(searchTerm);
                
                // Type filter
                const matchesType = !typeFilter || type === typeFilter;
                
                // Buyer filter
                const matchesBuyer = !buyerFilter || row.querySelector('td:nth-child(4)').textContent.includes(
                    document.getElementById('buyer').options[document.getElementById('buyer').selectedIndex].text
                );
                
                // Date filter
                let matchesDate = true;
                if (dateFrom && date < dateFrom) matchesDate = false;
                if (dateTo && date > dateTo) matchesDate = false;
                
                if (matchesSearch && matchesType && matchesBuyer && matchesDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update results count
            document.getElementById('resultsCount').textContent = `${visibleCount} transaction${visibleCount !== 1 ? 's' : ''}`;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            filterTransactions();
        });
    </script>
</body>
</html>
