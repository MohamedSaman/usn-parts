<div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-arrow-return-left text-primary me-2"></i> Product Returns
            </h3>
            <p class="text-muted mb-0">Manage product returns and refunds efficiently</p>
        </div>
        <div>
            <button class="btn btn-primary">
                <i class="bi bi-download me-2"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Customer Search and Invoice Selection -->
    <div class="row g-4 mb-4">
        <!-- Customer Search -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-person-search text-primary me-2"></i> Customer Search
                        </h5>
                        <p class="text-muted small mb-0">Find customer to process returns</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Search Customer or Invoice #</label>
                        <input type="text" class="form-control" wire:model.live="searchCustomer" placeholder="Search by customer name or invoice number...">
                    </div>

                    @if($searchCustomer && (count($customers) > 0 || count($customerInvoices) > 0))
                    <div class="border rounded p-3 bg-light">
                        <h6 class="fw-semibold mb-2">Search Results</h6>
                        <div class="list-group mb-2">
                            @foreach($customers as $customer)
                            <button class="list-group-item list-group-item-action p-2"
                                wire:click="selectCustomer({{ $customer->id }})"
                                type="button">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bi bi-person-circle fs-4 text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $customer->name }}</div>
                                        <small class="text-muted">{{ $customer->phone }} | {{ $customer->email }}</small>
                                    </div>
                                </div>
                            </button>
                            @endforeach
                        </div>
                        <div class="list-group">
                            @foreach($customerInvoices as $invoice)
                            @if(str_contains($invoice->invoice_number, $searchCustomer))
                            <button class="list-group-item list-group-item-action p-2"
                                wire:click="selectInvoiceForReturn({{ $invoice->id }})"
                                type="button">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bi bi-receipt fs-4 text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Invoice #{{ $invoice->invoice_number }}</div>
                                        <small class="text-muted">{{ $invoice->created_at->format('Y-m-d') }} | ${{ number_format($invoice->total_amount, 2) }}</small>
                                    </div>
                                </div>
                            </button>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($selectedCustomer)
                    <div class="mt-3 p-3 bg-info bg-opacity-10 rounded border border-info">
                        <h6 class="fw-semibold text-info mb-2">Selected Customer</h6>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-person-check fs-4 text-info"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $selectedCustomer->name }}</div>
                                <small class="text-muted">{{ $selectedCustomer->phone }} | {{ $selectedCustomer->email }}</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Invoices -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-receipt text-info me-2"></i> Recent Invoices
                        </h5>
                        <p class="text-muted small mb-0">Latest 5 invoices for selected customer</p>
                    </div>
                    <button class="btn btn-info btn-sm" wire:click="loadCustomerInvoices">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    @if($selectedCustomer && count($customerInvoices) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Invoice #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerInvoices as $invoice)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-medium text-dark">{{ $invoice->invoice_number }}</span>
                                    </td>
                                    <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">${{ number_format($invoice->total_amount, 2) }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-sm btn-success"
                                                wire:click="selectInvoiceForReturn({{ $invoice->id }})">
                                                <i class="bi bi-check-circle me-1"></i> Select
                                            </button>
                                            <button class="btn btn-sm btn-outline-info"
                                                wire:click="viewInvoice({{ $invoice->id }})">
                                                <i class="bi bi-eye me-1"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt-cutoff text-muted fs-1 mb-3"></i>
                        <p class="text-muted mb-0">No invoices found for this customer</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($showReturnSection && $selectedInvoice)
    <!-- Invoice Items for Return -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-receipt text-info me-2"></i> Invoice #{{ $selectedInvoice->invoice_number }} Items
                    </h5>
                    <p class="text-muted small mb-0">Select return quantity for each item below</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Code</th>
                                    <th>Sold Qty</th>
                                    <th>Return Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedInvoice->items as $index => $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->code }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" style="width: 80px;" min="0" max="{{ $item->quantity }}"
                                            wire:model.lazy="returnItems.{{ $index }}.return_qty">
                                        <input type="hidden" wire:model="returnItems.{{ $index }}.product_id" value="{{ $item->product->id }}">
                                        <input type="hidden" wire:model="returnItems.{{ $index }}.name" value="{{ $item->product->name }}">
                                        <input type="hidden" wire:model="returnItems.{{ $index }}.unit_price" value="{{ $item->unit_price }}">
                                        <input type="hidden" wire:model="returnItems.{{ $index }}.max_qty" value="{{ $item->quantity }}">
                                    </td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>
                                        ${{ isset($returnItems[$index]['return_qty']) ? number_format($returnItems[$index]['return_qty'] * $item->unit_price, 2) : '0.00' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <span class="fw-bold fs-5 text-warning">Total Return Value: ${{ number_format($totalReturnValue, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button class="btn btn-success px-4" wire:click="processReturn">
                            <i class="bi bi-check2-circle me-1"></i> Process Return
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif



    <!-- Return Processing Modal -->
    <div wire:ignore.self class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-return-left text-warning me-2"></i> Process Product Return
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> {{ $selectedCustomer?->name }}</p>
                            <p><strong>Invoice:</strong> #{{ $selectedInvoice?->invoice_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Return Value:</strong> ${{ number_format($totalReturnValue, 2) }}</p>
                            <p><strong>Items:</strong> {{ count($returnItems) }}</p>
                        </div>
                    </div>

                    <h6>Return Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Return Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($returnItems as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['return_qty'] }}</td>
                                    <td>${{ number_format($item['unit_price'], 2) }}</td>
                                    <td>${{ number_format($item['return_qty'] * $item['unit_price'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" wire:click="confirmReturn">Confirm Return</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Details Modal -->
    <div wire:ignore.self class="modal fade" id="invoiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt text-info me-2"></i> Invoice Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($invoiceModalData)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Invoice Number:</strong> {{ $invoiceModalData['invoice_number'] }}</p>
                            <p><strong>Customer:</strong> {{ $invoiceModalData['customer_name'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> {{ $invoiceModalData['date'] }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($invoiceModalData['total_amount'], 2) }}</p>
                        </div>
                    </div>

                    <h6>Invoice Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Code</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoiceModalData['items'] as $item)
                                <tr>
                                    <td>{{ $item['product_name'] }}</td>
                                    <td>{{ $item['product_code'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>${{ number_format($item['unit_price'], 2) }}</td>
                                    <td>${{ number_format($item['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .summary-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .summary-card.pending {
        border-left-color: #ffc107;
    }

    .summary-card.processed {
        border-left-color: #28a745;
    }

    .summary-card.total {
        border-left-color: #4361ee;
    }

    .icon-container {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px 12px 0 0 !important;
        padding: 1.25rem 1.5rem;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }

    .btn-link {
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-link:hover {
        transform: scale(1.1);
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        border-color: #4361ee;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .btn-primary:hover {
        background-color: #3f37c9;
        border-color: #3f37c9;
        transform: translateY(-2px);
    }

    .btn-info {
        background-color: #4895ef;
        border-color: #4895ef;
    }

    .btn-warning {
        background-color: #f72585;
        border-color: #f72585;
    }

    .btn-success {
        background-color: #4cc9f0;
        border-color: #4cc9f0;
    }

    .product-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border-color: #28a745;
    }

    .form-control-lg {
        font-size: 1.1rem;
        padding: 1rem 1.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Handle success alerts
    window.addEventListener('alert', event => {
        Swal.fire('Success', event.detail.message, 'success');
    });

    // Show return confirmation modal
    Livewire.on('show-return-modal', () => {
        var modalEl = document.getElementById('returnModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // Show invoice details modal
    Livewire.on('show-invoice-modal', () => {
        var modalEl = document.getElementById('invoiceModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // Close return modal after confirm
    Livewire.on('close-return-modal', () => {
        var modalEl = document.getElementById('returnModal');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    });
    // Reload page after confirm return
    Livewire.on('reload-page', () => {
        window.location.reload();
    });
</script>
@endpush