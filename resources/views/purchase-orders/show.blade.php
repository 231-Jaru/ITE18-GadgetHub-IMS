<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $purchaseOrder->PONumber }} - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .badge-draft { background-color: #6b7280; }
        .badge-ordered { background-color: #3b82f6; }
        .badge-received { background-color: #10b981; }
        .badge-cancelled { background-color: #ef4444; }

        .item-row {
            border-left: 4px solid var(--primary-color);
            background: #f8fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-shopping-cart me-2"></i>Purchase Order: {{ $purchaseOrder->PONumber }}
                    </h1>
                    <div class="d-flex gap-3 align-items-center">
                        <span class="badge badge-{{ strtolower($purchaseOrder->Status) }} text-white px-3 py-2">
                            {{ $purchaseOrder->Status }}
                        </span>
                        <span class="opacity-75">Total: <strong>{{ \App\Helpers\CurrencyHelper::formatPhp($purchaseOrder->TotalAmount) }}</strong></span>
                    </div>
                </div>
                <a href="/purchase-orders" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                    <div class="mb-2">
                        <strong>Supplier:</strong><br>
                        {{ $purchaseOrder->supplier->SupplierName }}
                    </div>
                    <div class="mb-2">
                        <strong>Order Date:</strong><br>
                        {{ $purchaseOrder->OrderDate ? $purchaseOrder->OrderDate->format('M d, Y') : '-' }}
                    </div>
                    @if($purchaseOrder->ExpectedDeliveryDate)
                    <div class="mb-2">
                        <strong>Expected Delivery:</strong><br>
                        {{ $purchaseOrder->ExpectedDeliveryDate->format('M d, Y') }}
                    </div>
                    @endif
                    @if($purchaseOrder->ReceivedDate)
                    <div class="mb-2">
                        <strong>Received Date:</strong><br>
                        {{ $purchaseOrder->ReceivedDate->format('M d, Y') }}
                    </div>
                    @endif
                    @if($purchaseOrder->Notes)
                    <div class="mb-2">
                        <strong>Notes:</strong><br>
                        {{ $purchaseOrder->Notes }}
                    </div>
                    @endif
                </div>

                @if($purchaseOrder->Status !== 'RECEIVED' && $purchaseOrder->Status !== 'CANCELLED')
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Update Status</h5>
                    <form method="POST" action="/purchase-orders/{{ $purchaseOrder->PurchaseOrderID }}/update-status">
                        @csrf
                        <div class="mb-3">
                            <select class="form-select" name="Status" required>
                                <option value="DRAFT" {{ $purchaseOrder->Status === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                <option value="ORDERED" {{ $purchaseOrder->Status === 'ORDERED' ? 'selected' : '' }}>Ordered</option>
                                <option value="RECEIVED" {{ $purchaseOrder->Status === 'RECEIVED' ? 'selected' : '' }}>Received</option>
                                <option value="CANCELLED" {{ $purchaseOrder->Status === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                    @if($purchaseOrder->Status === 'ORDERED')
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Marking as "Received" will automatically add stock to inventory.</small>
                    </div>
                    @endif
                </div>
                @endif

                @if($purchaseOrder->Status === 'DRAFT')
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-trash me-2"></i>Delete Purchase Order</h5>
                    <p class="text-muted small mb-3">Only draft purchase orders can be deleted. Once ordered or received, use "Cancel" status instead.</p>
                    <form method="POST" action="/purchase-orders/{{ $purchaseOrder->PurchaseOrderID }}" 
                          class="delete-form" 
                          data-item-name="purchase order"
                          onsubmit="return confirm('Are you sure you want to delete purchase order {{ $purchaseOrder->PONumber }}? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete Purchase Order
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="col-md-8">
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-list me-2"></i>Order Items</h5>
                    @foreach($purchaseOrder->items as $item)
                        <div class="item-row">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <h6 class="mb-1">{{ $item->gadget->GadgetName }}</h6>
                                    @if($item->gadget->category)
                                        <small class="text-muted">{{ $item->gadget->category->CategoryName }}</small>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    <strong>Qty:</strong> {{ $item->Quantity }}
                                </div>
                                <div class="col-md-2 text-center">
                                    <strong>Unit Cost:</strong><br>
                                    {{ \App\Helpers\CurrencyHelper::formatPhp($item->UnitCost) }}
                                </div>
                                <div class="col-md-2 text-center">
                                    <strong>Total:</strong><br>
                                    <span class="text-primary">{{ \App\Helpers\CurrencyHelper::formatPhp($item->TotalCost) }}</span>
                                </div>
                                <div class="col-md-1 text-center">
                                    @if($item->QuantityReceived > 0)
                                        <span class="badge bg-success">
                                            {{ $item->QuantityReceived }}/{{ $item->Quantity }} Received
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Total Amount:</h5>
                            <h4 class="mb-0 text-primary">{{ \App\Helpers\CurrencyHelper::formatPhp($purchaseOrder->TotalAmount) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/button-fixes.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading state to status update form
            const statusForm = document.querySelector('form[action*="update-status"]');
            if (statusForm) {
                statusForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
                    }
                });
            }
        });
    </script>
</body>
</html>

