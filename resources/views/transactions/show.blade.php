<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction #{{ $transaction->TransactionID }} - Gadgethub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    @include('partials.navbar')
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-store"></i> Gadgethub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gadgets.index') }}">
                            <i class="fas fa-mobile-alt"></i> Gadgets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stocks.inventory') }}">
                            <i class="fas fa-warehouse"></i> Stocks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('transactions.index') }}">
                            <i class="fas fa-receipt"></i> Transactions
                        </a>
                    </li>
                    <li class="nav-item">
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('suppliers.index') }}">
                            <i class="fas fa-truck"></i> Suppliers
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>
                        <i class="fas fa-receipt"></i> Transaction #{{ $transaction->TransactionID }}
                    </h1>
                    <div>
                        <a href="{{ route('transactions.edit', $transaction->TransactionID) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Transactions
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Transaction Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="30%">Transaction ID:</th>
                                                <td>{{ $transaction->TransactionID }}</td>
                                            </tr>
                                            <tr>
                                                <th>Type:</th>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->TransactionType == 'OUT' ? 'success' : 'info' }}">
                                                        {{ $transaction->TransactionType }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date:</th>
                                                <td>{{ $transaction->TransactionDate ? $transaction->TransactionDate->format('M d, Y H:i') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status:</th>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->Status == 'Completed' ? 'success' : ($transaction->Status == 'Pending' ? 'warning' : 'secondary') }}">
                                                        {{ $transaction->Status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="30%">Buyer:</th>
                                                <td>{{ $transaction->buyer->BuyerName ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Gadget:</th>
                                                <td>{{ $transaction->gadget->GadgetName ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Quantity:</th>
                                                <td><strong>{{ $transaction->Quantity }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Total Amount:</th>
                                                <td><strong class="text-success">{{ \App\Helpers\CurrencyHelper::formatPhp($transaction->TotalAmount) }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($transaction->Notes)
                                <div class="mt-4">
                                    <h6>Notes</h6>
                                    <div class="bg-light p-3 rounded">
                                        <p class="mb-0">{{ $transaction->Notes }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($transaction->admin)
                                <div class="mt-4">
                                    <h6>Admin Information</h6>
                                    <p class="text-muted">Processed by: <strong>{{ $transaction->admin->AdminName }}</strong></p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center text-muted">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <p>Inventory Purchase Transaction</p>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Transaction Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Gadget:</strong> {{ $transaction->gadget->GadgetName ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Quantity:</strong> {{ $transaction->Quantity }}
                                    </div>
                                </div>
                                @if(false)
                                    @foreach([] as $item)
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <div>
                                            <strong>{{ $item->gadget->GadgetName ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">Qty: {{ $item->Quantity }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong>{{ \App\Helpers\CurrencyHelper::formatPhp($item->PriceAtSale * $item->Quantity) }}</strong>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                        <p>No items found</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
