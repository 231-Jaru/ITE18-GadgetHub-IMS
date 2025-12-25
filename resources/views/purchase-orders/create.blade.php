<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Purchase Order - Inventory System</title>
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
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .item-row {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        .btn-remove-item {
            background: #ef4444;
            color: white;
            border: none;
        }

        .btn-remove-item:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid mt-4">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1"><i class="fas fa-shopping-cart me-2"></i>Create Purchase Order</h1>
                    <p class="mb-0 opacity-75">Add items to order from supplier</p>
                </div>
                <a href="/purchase-orders" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <form method="POST" action="/purchase-orders" id="poForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="SupplierID" class="form-label">
                                    <i class="fas fa-truck me-2"></i>Supplier <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('SupplierID') is-invalid @enderror" 
                                        id="SupplierID" name="SupplierID" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->SupplierID }}" 
                                                {{ old('SupplierID') == $supplier->SupplierID ? 'selected' : '' }}>
                                            {{ $supplier->SupplierName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('SupplierID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="OrderDate" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Order Date
                                </label>
                                <input type="date" 
                                       class="form-control @error('OrderDate') is-invalid @enderror" 
                                       id="OrderDate" 
                                       name="OrderDate" 
                                       value="{{ old('OrderDate', date('Y-m-d')) }}">
                                @error('OrderDate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="ExpectedDeliveryDate" class="form-label">
                                    <i class="fas fa-truck-fast me-2"></i>Expected Delivery Date
                                </label>
                                <input type="date" 
                                       class="form-control @error('ExpectedDeliveryDate') is-invalid @enderror" 
                                       id="ExpectedDeliveryDate" 
                                       name="ExpectedDeliveryDate" 
                                       value="{{ old('ExpectedDeliveryDate') }}">
                                @error('ExpectedDeliveryDate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="Notes" class="form-label">
                                    <i class="fas fa-sticky-note me-2"></i>Notes (Optional)
                                </label>
                                <textarea class="form-control @error('Notes') is-invalid @enderror" 
                                          id="Notes" 
                                          name="Notes" 
                                          rows="2">{{ old('Notes') }}</textarea>
                                @error('Notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><i class="fas fa-list me-2"></i>Items</h5>
                            <button type="button" class="btn btn-primary" id="addItemBtn">
                                <i class="fas fa-plus me-2"></i>Add Item
                            </button>
                        </div>

                        <div id="itemsContainer">
                            <!-- Items will be added here dynamically -->
                        </div>

                        <div class="alert alert-info mt-3" id="noItemsAlert" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>Please add at least one item to the purchase order.
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="/purchase-orders" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Purchase Order
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
            let itemCount = 0;
            const gadgets = @json($gadgets);

            const addItemBtn = document.getElementById('addItemBtn');
            if (addItemBtn) {
                addItemBtn.addEventListener('click', function() {
                    addItemRow();
                });
            }

            function addItemRow() {
                itemCount++;
                const container = document.getElementById('itemsContainer');
                const noItemsAlert = document.getElementById('noItemsAlert');
                if (noItemsAlert) {
                    noItemsAlert.style.display = 'none';
                }
                if (!container) {
                    console.error('Items container not found');
                    return;
                }

                const itemRow = document.createElement('div');
                itemRow.className = 'item-row';
                itemRow.id = `item-${itemCount}`;
                
                itemRow.innerHTML = `
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gadget <span class="text-danger">*</span></label>
                        <select class="form-select gadget-select" name="items[${itemCount}][GadgetID]" required>
                            <option value="">Select Gadget</option>
                            ${gadgets.map(g => `<option value="${g.GadgetID}">${g.GadgetName}${g.category ? ' - ' + g.category.CategoryName : ''}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control quantity-input" name="items[${itemCount}][Quantity]" 
                               min="1" value="1" required onchange="calculateTotal(this)">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Unit Cost (₱) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control cost-input" name="items[${itemCount}][UnitCost]" 
                               step="0.01" min="0" value="0" required onchange="calculateTotal(this)">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control total-display" readonly value="₱0.00">
                    </div>
                    <div class="col-md-1 mb-3">
                        <button type="button" class="btn btn-remove-item w-100" data-item-id="${itemCount}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

                container.appendChild(itemRow);
            }

            // Event delegation for remove buttons - works for dynamically added items
            const itemsContainer = document.getElementById('itemsContainer');
            if (itemsContainer) {
                itemsContainer.addEventListener('click', function(e) {
                    // Check if clicked element is a remove button or inside one
                    const removeBtn = e.target.closest('.btn-remove-item');
                    if (removeBtn) {
                        e.preventDefault();
                        e.stopPropagation();
                        const itemId = removeBtn.getAttribute('data-item-id');
                        if (itemId) {
                            const item = document.getElementById(`item-${itemId}`);
                            if (item) {
                                item.remove();
                                checkItemsCount();
                            }
                        }
                    }
                });
            }

            // Make calculateTotal globally accessible for inline onchange handlers
            window.calculateTotal = function(input) {
                const row = input.closest('.item-row');
                if (!row) return;
                const quantityInput = row.querySelector('.quantity-input');
                const costInput = row.querySelector('.cost-input');
                const totalDisplay = row.querySelector('.total-display');
                if (!quantityInput || !costInput || !totalDisplay) return;
                
                const quantity = parseFloat(quantityInput.value) || 0;
                const cost = parseFloat(costInput.value) || 0;
                const total = quantity * cost;
                totalDisplay.value = '₱' + total.toFixed(2);
            };

            function checkItemsCount() {
                const items = document.querySelectorAll('.item-row');
                const noItemsAlert = document.getElementById('noItemsAlert');
                if (noItemsAlert) {
                    if (items.length === 0) {
                        noItemsAlert.style.display = 'block';
                    } else {
                        noItemsAlert.style.display = 'none';
                    }
                }
            }

            const poForm = document.getElementById('poForm');
            if (poForm) {
                poForm.addEventListener('submit', function(e) {
                    const items = document.querySelectorAll('.item-row');
                    if (items.length === 0) {
                        e.preventDefault();
                        alert('Please add at least one item to the purchase order.');
                        return false;
                    }

                    // Add loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                    }
                });
            }

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

            // Add first item on page load
            addItemRow();
        });
    </script>
</body>
</html>

