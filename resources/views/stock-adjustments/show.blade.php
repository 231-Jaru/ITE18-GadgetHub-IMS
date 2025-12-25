<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Adjustment Details - Inventory System</title>
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
        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .info-row {
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
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
                    <h1 class="mb-1"><i class="fas fa-adjust me-2"></i>Stock Adjustment Details</h1>
                    <p class="mb-0 opacity-75">View adjustment information</p>
                </div>
                <a href="/stock-adjustments" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="detail-card">
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Gadget:</strong></div>
                            <div class="col-md-8">
                                {{ $adjustment->gadget->GadgetName }}
                                @if($adjustment->gadget->category)
                                    <span class="text-muted">({{ $adjustment->gadget->category->CategoryName }})</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Adjustment Type:</strong></div>
                            <div class="col-md-8">
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
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Quantity Before:</strong></div>
                            <div class="col-md-8"><strong class="text-muted">{{ $adjustment->QuantityBefore }}</strong></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Quantity Changed:</strong></div>
                            <div class="col-md-8">
                                @if($adjustment->QuantityChanged > 0)
                                    <span class="text-success"><strong>+{{ $adjustment->QuantityChanged }}</strong></span>
                                @else
                                    <span class="text-danger"><strong>{{ $adjustment->QuantityChanged }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Quantity After:</strong></div>
                            <div class="col-md-8"><strong class="text-primary">{{ $adjustment->QuantityAfter }}</strong></div>
                        </div>
                    </div>

                    @if($adjustment->Reason)
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Reason:</strong></div>
                            <div class="col-md-8">{{ $adjustment->Reason }}</div>
                        </div>
                    </div>
                    @endif

                    @if($adjustment->Notes)
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Notes:</strong></div>
                            <div class="col-md-8">{{ $adjustment->Notes }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Date:</strong></div>
                            <div class="col-md-8">{{ $adjustment->AdjustmentDate->format('F d, Y h:i A') }}</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4"><strong>Admin:</strong></div>
                            <div class="col-md-8">{{ $adjustment->admin->Username ?? 'System' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/stock-adjustments" class="btn btn-secondary">
                                <i class="fas fa-list me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

