<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Adjustments - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .page-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .badge-increase { background-color: #10b981; }
        .badge-decrease { background-color: #ef4444; }
        .badge-set { background-color: #3b82f6; }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1"><i class="fas fa-adjust me-2"></i>Stock Adjustments</h1>
                    <p class="mb-0 opacity-75">View all stock adjustments and corrections</p>
                </div>
                <a href="/stock-adjustments/create" class="btn btn-light">
                    <i class="fas fa-plus me-2"></i>New Adjustment
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div class="flex-grow-1">
                    <strong>{{ session('success') }}</strong>
                    @if(session('adjustment_id'))
                        <div class="mt-2">
                            <small class="d-block mb-2">What would you like to do next?</small>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="/stock-adjustments/{{ session('adjustment_id') }}" class="btn btn-outline-success">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                                <a href="/stock-adjustments/create" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-1"></i> Create Another
                                </a>
                                <a href="/stocks" class="btn btn-outline-info">
                                    <i class="fas fa-boxes me-1"></i> View Stocks
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($adjustments->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Stock Adjustments Yet</h5>
                    <p class="text-muted">Create your first stock adjustment to get started.</p>
                    <a href="/stock-adjustments/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Adjustment
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Gadget</th>
                                    <th>Type</th>
                                    <th>Before</th>
                                    <th>Change</th>
                                    <th>After</th>
                                    <th>Reason</th>
                                    <th>Admin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adjustments as $adjustment)
                                    <tr class="{{ session('adjustment_id') == $adjustment->AdjustmentID ? 'table-success' : '' }}">
                                        <td>
                                            {{ $adjustment->AdjustmentDate ? $adjustment->AdjustmentDate->format('M d, Y') : 'N/A' }}
                                            @if(session('adjustment_id') == $adjustment->AdjustmentID)
                                                <span class="badge bg-success ms-2">New</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($adjustment->gadget)
                                                <strong>{{ $adjustment->gadget->GadgetName }}</strong>
                                                @if($adjustment->gadget->category)
                                                    <br><small class="text-muted">{{ $adjustment->gadget->category->CategoryName }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Gadget not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($adjustment->AdjustmentType === 'INCREASE')
                                                <span class="badge badge-increase text-white">
                                                    <i class="fas fa-arrow-up"></i> Increase
                                                </span>
                                            @elseif($adjustment->AdjustmentType === 'DECREASE')
                                                <span class="badge badge-decrease text-white">
                                                    <i class="fas fa-arrow-down"></i> Decrease
                                                </span>
                                            @else
                                                <span class="badge badge-set text-white">
                                                    <i class="fas fa-equals"></i> Set
                                                </span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $adjustment->QuantityBefore }}</strong></td>
                                        <td>
                                            @if($adjustment->QuantityChanged > 0)
                                                <span class="text-success">+{{ $adjustment->QuantityChanged }}</span>
                                            @else
                                                <span class="text-danger">{{ $adjustment->QuantityChanged }}</span>
                                            @endif
                                        </td>
                                        <td><strong class="text-primary">{{ $adjustment->QuantityAfter }}</strong></td>
                                        <td>
                                            @if($adjustment->Reason)
                                                {{ $adjustment->Reason }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $adjustment->admin ? $adjustment->admin->Username : 'System' }}</td>
                                        <td>
                                            <a href="/stock-adjustments/{{ $adjustment->AdjustmentID }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($adjustments->hasPages())
                        <div class="card-footer bg-light py-3">
                            @include('partials.simple-pagination', ['paginator' => $adjustments])
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

