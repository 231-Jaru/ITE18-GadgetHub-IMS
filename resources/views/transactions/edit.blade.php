<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction - Gadgethub</title>
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
                        <i class="fas fa-edit"></i> Edit Transaction #{{ $transaction->TransactionID }}
                    </h1>
                    <div>
                        <a href="{{ route('transactions.show', $transaction->TransactionID) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Transactions
                        </a>
                    </div>
                </div>

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('transactions.update', $transaction->TransactionID) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="TransactionType" class="form-label">Transaction Type *</label>
                                        <select class="form-select" id="TransactionType" name="TransactionType" required>
                                            <option value="">Select Type</option>
                                            <option value="IN" {{ old('TransactionType', $transaction->TransactionType) == 'IN' ? 'selected' : '' }}>IN (Stock In)</option>
                                            <option value="OUT" {{ old('TransactionType', $transaction->TransactionType) == 'OUT' ? 'selected' : '' }}>OUT (Sale)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="TransactionDate" class="form-label">Transaction Date *</label>
                                        <input type="datetime-local" class="form-control" id="TransactionDate" name="TransactionDate" value="{{ old('TransactionDate', $transaction->TransactionDate ? $transaction->TransactionDate->format('Y-m-d\TH:i') : '') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="GadgetID" class="form-label">Gadget *</label>
                                        <select class="form-select" id="GadgetID" name="GadgetID" required>
                                            <option value="">Select Gadget</option>
                                            @foreach($gadgets as $gadget)
                                                <option value="{{ $gadget->GadgetID }}" {{ old('GadgetID', $transaction->GadgetID) == $gadget->GadgetID ? 'selected' : '' }}>
                                                    {{ $gadget->GadgetName }} - ₱{{ number_format($gadget->Price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="Quantity" class="form-label">Quantity *</label>
                                        <input type="number" class="form-control" id="Quantity" name="Quantity" value="{{ old('Quantity', $transaction->Quantity) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="TotalAmount" class="form-label">Total Amount *</label>
                                        <input type="number" step="0.01" class="form-control" id="TotalAmount" name="TotalAmount" value="{{ old('TotalAmount', $transaction->TotalAmount) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="Status" class="form-label">Status</label>
                                        <select class="form-select" id="Status" name="Status">
                                            <option value="Pending" {{ old('Status', $transaction->Status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Completed" {{ old('Status', $transaction->Status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Cancelled" {{ old('Status', $transaction->Status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="AdminID" class="form-label">Admin</label>
                                        <select class="form-select" id="AdminID" name="AdminID">
                                            <option value="">Select Admin</option>
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->AdminID }}" {{ old('AdminID', $transaction->AdminID) == $admin->AdminID ? 'selected' : '' }}>
                                                    {{ $admin->AdminName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="Notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="Notes" name="Notes" rows="3">{{ old('Notes', $transaction->Notes) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Transaction
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-calculate total amount when quantity or gadget changes
        document.getElementById('GadgetID').addEventListener('change', calculateTotal);
        document.getElementById('Quantity').addEventListener('input', calculateTotal);

        function calculateTotal() {
            const gadgetSelect = document.getElementById('GadgetID');
            const quantityInput = document.getElementById('Quantity');
            const totalAmountInput = document.getElementById('TotalAmount');

            if (gadgetSelect.value && quantityInput.value) {
                const selectedOption = gadgetSelect.options[gadgetSelect.selectedIndex];
                const priceText = selectedOption.text.split('$')[1];
                if (priceText) {
                    const price = parseFloat(priceText);
                    const quantity = parseInt(quantityInput.value);
                    const total = price * quantity;
                    totalAmountInput.value = total.toFixed(2);
                }
            }
        }
    </script>
</body>
</html>
