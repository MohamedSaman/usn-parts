<div class="container-fluid py-3">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('message'))
    <div class="alert alert-info alert-dismissible fade show mb-4">
        <i class="bi bi-info-circle me-2"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        {{-- Customer Information --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Customer Information
                    </h5>
                    <button class="btn btn-sm btn-primary" wire:click="openCustomerModal">
                        <i class="bi bi-plus-circle me-1"></i> Add New Customer
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Select Customer *</label>
                            <select class="form-select" wire:model.live="customerId">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }} - {{ $customer->phone }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">Select existing customer</div>
                        </div>
       {{-- Customer Information --}}
<div class="col-6 mb-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-person me-2"></i>Customer Information
            </h5>
            <button class="btn btn-sm btn-primary" wire:click="openCustomerModal">
                <i class="bi bi-plus-circle me-1"></i> Add New Customer
            </button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Select Customer *</label>
                    <select class="form-select" wire:model.live="customerId">
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $customer->name === 'Walking Customer' ? 'selected' : '' }}>
                            {{ $customer->name }}
                            @if($customer->phone)
                             - {{ $customer->phone }}
                            @endif
                            @if($customer->name === 'Walking Customer') (Default) @endif
                        </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        @if($selectedCustomer && $selectedCustomer->name === 'Walking Customer')
                        <span class="text-info">
                            <i class="bi bi-info-circle"></i> Using default walking customer
                        </span>
                        @else
                        Select existing customer or add new
                        @endif
                    </div>
                </div>
              
            </div>
        </div>
    </div>
</div>

        {{-- Add Products Card --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-search me-2"></i>Add Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" wire:model.live="search"
                            placeholder="Search by product name, code or model...">
                    </div>

                    {{-- Search Results --}}
                    @if($search && count($searchResults) > 0)
                    <div class="search-results border rounded bg-white">
                        @foreach($searchResults as $product)
                        <div class="p-3 border-bottom" wire:key="product-{{ $product['id'] }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $product['name'] }}</h6>
                                    <p class="text-muted small mb-0">
                                        Code: {{ $product['code'] }} |
                                        Model: {{ $product['model'] }}
                                    </p>
                                    <p class="text-success small mb-0">
                                        Rs.{{ number_format($product['price'], 2) }} |
                                        Stock: {{ $product['stock'] }}
                                    </p>
                                </div>
                                <button class="btn btn-sm btn-primary"
                                    wire:click="addToCart({{ json_encode($product) }})"
                                    {{ $product['stock'] <= 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @elseif($search)
                    <div class="text-center text-muted p-3">
                        No products found
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sale Items Table --}}
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cart me-2"></i>Sale Items
                    </h5>
                    <span class="badge bg-primary">{{ count($cart) }} items</span>
                </div>
                <div class="card-body p-0">
                    @if(count($cart) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="30">#</th>
                                    <th>Product</th>
                                    <th width="120">Unit Price</th>
                                    <th width="150">Quantity</th>
                                    <th width="120">Discount/Unit</th>
                                    <th width="120">Total</th>
                                    <th width="100" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $item['name'] }}</strong>
                                            <div class="text-muted small">
                                                {{ $item['code'] }} | {{ $item['model'] }}
                                            </div>
                                            <div class="text-info small">
                                                Stock: {{ $item['stock'] }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">
                                        Rs.{{ number_format($item['price'], 2) }}
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary" type="button"
                                                wire:click="decrementQuantity({{ $index }})">-</button>
                                            <input type="number" class="form-control text-center"
                                                wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}">
                                            <button class="btn btn-outline-secondary" type="button"
                                                wire:click="incrementQuantity({{ $index }})">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-danger"
                                            wire:change="updateDiscount({{ $index }}, $event.target.value)"
                                            value="{{ $item['discount'] }}" min="0" step="0.01"
                                            placeholder="0.00">
                                    </td>
                                    <td class="fw-bold">
                                        Rs.{{ number_format($item['total'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger"
                                            wire:click="removeFromCart({{ $index }})"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                    <td class="fw-bold">Rs.{{ number_format($subtotal, 2) }}</td>
                                    <td></td>
                                </tr>

                                {{-- Additional Discount Section --}}
                                <tr>
                                    <td colspan="3" class="text-end fw-bold align-middle">
                                        Additional Discount:
                                        @if($additionalDiscount > 0)
                                        <button type="button" class=" text-danger p-0 ms-1 border-0 bg-opacity-0"
                                            wire:click="removeAdditionalDiscount" title="Remove discount">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        @endif
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group input-group-sm">
                                            <input type="number"
                                                class="form-control form-control-sm text-danger"
                                                wire:model.live="additionalDiscount"
                                                min="0"
                                                step="{{ $additionalDiscountType === 'percentage' ? '1' : '0.01' }}"
                                                @if($additionalDiscountType==='percentage' ) max="100" @endif
                                                placeholder="0{{ $additionalDiscountType === 'percentage' ? '' : '.00' }}">

                                            <span class="input-group-text">
                                                {{ $additionalDiscountType === 'percentage' ? '%' : 'Rs.' }}
                                            </span>

                                            <button type="button"
                                                class="btn btn-outline-secondary"
                                                wire:click="toggleDiscountType"
                                                title="Switch Discount Type">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        @if($additionalDiscount > 0)
                                        - Rs.{{ number_format($additionalDiscountAmount, 2) }}
                                        @if($additionalDiscountType === 'percentage')
                                        <div class="text-muted small">({{ $additionalDiscount }}%)</div>
                                        @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-cart display-4 d-block mb-2"></i>
                        No items added yet
                    </div>
                    @endif
                </div>
                @if(count($cart) > 0)
                <div class="card-footer">
                    <button class="btn btn-danger" wire:click="clearCart">
                        <i class="bi bi-trash me-2"></i>Clear All Items
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Notes --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-text me-2"></i>Notes
                    </h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" wire:model="notes" rows="3"
                        placeholder="Add any notes for this sale..."></textarea>
                </div>
            </div>
        </div>

        {{-- Create Sale Button --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="fw-bold fs-5">Grand Total</div>
                        <div class="fw-bold fs-5 text-primary">Rs.{{ number_format($grandTotal, 2) }}</div>
                    </div>
                    <button class="btn btn-success btn-lg px-5" wire:click="createSale"
                        {{ count($cart) == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cart-check me-2"></i>Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Customer Modal --}}
    @if($showCustomerModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>Add New Customer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCustomerModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" wire:model="customerName" placeholder="Enter customer name">
                            @error('customerName') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone *</label>
                            <input type="text" class="form-control" wire:model="customerPhone" placeholder="Enter phone number">
                            @error('customerPhone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="customerEmail" placeholder="Enter email address">
                            @error('customerEmail') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Type *</label>
                            <select class="form-select" wire:model="customerType">
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                                <option value="business">Business</option>
                            </select>
                            @error('customerType') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Name</label>
                            <input type="text" class="form-control" wire:model="businessName" placeholder="Enter business name">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address *</label>
                            <textarea class="form-control" wire:model="customerAddress" rows="3" placeholder="Enter address"></textarea>
                            @error('customerAddress') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCustomerModal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="createCustomer">
                        <i class="bi bi-check-circle me-2"></i>Create Customer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Sale Preview Modal --}}
    @if($showSaleModal && $createdSale)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-cart-check me-2"></i>
                        Sale Completed Successfully! - {{ $createdSale->invoice_number }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="createNewSale"></button>
                </div>

                <div class="modal-body p-0">
                    {{-- Sale Preview --}}
                    <div class="sale-preview p-4">
                        {{-- Header --}}
                        <div class="header text-center mb-4">
                            <h2 class="text-success mb-1">USN AUTO PARTS</h2>
                            <p class="mb-1">103 H, Yatiyanthota Road, Seethawaka, Avissawella</p>
                            <p class="mb-1">Phone: (076) 9085252 | Email: autopartsusn@gmail.com</p>
                        </div>

                        {{-- Customer & Sale Details --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Customer Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>{{ $createdSale->customer->name }}</strong></p>
                                        <p class="mb-1">{{ $createdSale->customer->address }}</p>
                                        <p class="mb-1">Tel: {{ $createdSale->customer->phone }}</p>
                                        @if($createdSale->customer->email)
                                        <p class="mb-0">Email: {{ $createdSale->customer->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Sale Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Sale ID:</strong> {{ $createdSale->sale_id }}</p>
                                        <p class="mb-1"><strong>Invoice No:</strong> {{ $createdSale->invoice_number }}</p>
                                        <p class="mb-1"><strong>Date:</strong> {{ $createdSale->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Item Code</th>
                                        <th>Description</th>
                                        <th width="80">Qty</th>
                                        <th width="120">Unit Price </th>
                                        <th width="120">Discount </th>
                                        <th width="120">Subtotal </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($createdSale->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->discount_per_unit ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                {{-- Totals Section --}}
                                <tfoot class="table-light">
                                    @php
                                    $itemDiscountTotal = $createdSale->items->sum(function($item) {
                                        return ($item->discount_per_unit ?? 0) * $item->quantity;
                                    });
                                    @endphp
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold">{{ number_format($createdSale->subtotal + $itemDiscountTotal, 2) }}</td>
                                    </tr>

                                    @php
                                    $itemDiscountTotal = $createdSale->items->sum(function($item) {
                                        return ($item->discount_per_unit ?? 0) * $item->quantity;
                                    });
                                    $totalDiscount = ($createdSale->discount_amount + $itemDiscountTotal);
                                    @endphp
                                    @if($totalDiscount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold text-danger">Total Discount:</td>
                                        <td class="text-end fw-bold text-danger">- {{ number_format($totalDiscount, 2) }}</td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td colspan="6" class="text-end fw-bold fs-5">Grand Total:</td>
                                        <td class="text-end fw-bold fs-5 text-primary">
                                            {{ number_format($createdSale->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Notes --}}
                        @if($createdSale->notes)
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Notes</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{!! nl2br(e($createdSale->notes)) !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary me-2" wire:click="createNewSale">
                        <i class="bi bi-plus-circle me-2"></i>Create New Sale
                    </button>
                    <button type="button" class="btn btn-success" wire:click="downloadInvoice">
                        <i class="bi bi-download me-2"></i>Download Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    
    .container-fluid,
    .card,
    .modal-content {
        font-size: 13px !important;
    }

    .table th,
    .table td {
        font-size: 12px !important;
        padding: 0.35rem 0.5rem !important;
    }

    .modal-header{
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
        margin-bottom: 0.25rem !important;
    }
    .modal-footer,
    .card-header,
    .card-body,
    .row,
    .col-md-6,
    .col-md-4,
    .col-md-2,
    .col-md-12 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
        margin-top: 0.25rem !important;
        margin-bottom: 0.25rem !important;
    }

    .form-control,
    .form-select {
        font-size: 12px !important;
        padding: 0.35rem 0.5rem !important;
    }

    .btn,
    .btn-sm,
    .btn-primary,
    .btn-secondary,
    .btn-outline-danger,
    .btn-outline-secondary {
        font-size: 12px !important;
        padding: 0.25rem 0.5rem !important;
    }

    .badge {
        font-size: 11px !important;
        padding: 0.25em 0.5em !important;
    }

    .list-group-item,
    .dropdown-item {
        font-size: 12px !important;
        padding: 0.35rem 0.5rem !important;
    }

    .summary-card,
    .card {
        border-radius: 8px !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06) !important;
    }

    .icon-container {
        width: 36px !important;
        height: 36px !important;
        font-size: 1.1rem !important;
    }
    .search-results {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    .search-results .p-3 {
        transition: background-color 0.2s ease;
    }

    .search-results .p-3:hover {
        background-color: #f8f9fa;
    }

    .table th {
        font-size: 0.875rem;
        font-weight: 600;
        background-color: #f8f9fa;
    }

    .form-control-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }

    .input-group-sm>.btn {
        padding: 0.25rem 0.5rem;
    }

    /* Modal Styles */
    .modal.show {
        backdrop-filter: blur(2px);
    }

    /* Sale Preview Styles */
    .sale-preview {
        background: white;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .sale-preview .header {
        border-bottom: 2px solid #057642ff;
        padding-bottom: 1rem;
    }

    .sale-preview table th {
        background-color: #038d4fff;
        color: white;
        border: none;
    }

    .sale-preview table td {
        border: 1px solid #dee2e6;
    }

    /* Discount input styling */
    .text-danger.form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .input-group-sm {
            flex-wrap: nowrap;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }
    }

    /* Stock warning */
    .text-info small {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-close alerts after 5 seconds
    document.addEventListener('livewire:initialized', () => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });

    // Prevent form submission on enter key in search
    document.addEventListener('keydown', function(e) {
        if (e.target.type === 'text' && e.target.getAttribute('wire:model') === 'search') {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        }
    });
</script>
@endpush