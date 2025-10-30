<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-receipt text-success me-2"></i> Customer Payment Receipt
            </h3>
            <p class="text-muted mb-0">Record customer payments and allocate to due invoices</p>
        </div>
    </div>

    {{-- Customer Search --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-search me-2"></i> Find Customer with Due Payments
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Search Customer</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control"
                            placeholder="Search by customer name, phone or email..."
                            wire:model.live="search">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Selected Customer</label>
                    @if($selectedCustomer)
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $selectedCustomer->name }}</h6>
                                <p class="mb-1 text-muted">
                                    <i class="bi bi-telephone me-1"></i>{{ $selectedCustomer->phone }}
                                    @if($selectedCustomer->email)
                                    | <i class="bi bi-envelope me-1"></i>{{ $selectedCustomer->email }}
                                    @endif
                                </p>
                                <span class="badge bg-warning">
                                    {{ count($customerSales) }} Due Invoice(s)
                                </span>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="clearSelectedCustomer">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="border rounded p-3 text-center text-muted">
                        <i class="bi bi-person-x display-6 d-block mb-2"></i>
                        No customer selected
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Customer List --}}
    @if(!$selectedCustomer)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-people me-2"></i> Customers with Due Payments
            </h5>
            <span class="badge bg-primary">{{ $customers->total() }} customers</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Customer</th>
                            <th>Contact</th>
                            <th class="text-center">Due Invoices</th>
                            <th class="text-end">Total Due Amount</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        @php
                        $dueInvoices = $customer->sales->where('due_amount', '>', 0)->count();
                        $totalDue = $customer->sales->where('due_amount', '>', 0)->sum('due_amount');
                        @endphp
                        <tr wire:key="customer-{{ $customer->id }}">
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $customer->name }}</div>
                                <small class="text-muted">Type: {{ ucfirst($customer->type) }}</small>
                            </td>
                            <td>
                                <div class="text-dark">{{ $customer->phone }}</div>
                                @if($customer->email)
                                <small class="text-muted">{{ $customer->email }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning">{{ $dueInvoices }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-danger">Rs.{{ number_format($totalDue, 2) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-primary btn-sm" wire:click="selectCustomer({{ $customer->id }})">
                                    <i class="bi bi-credit-card me-1"></i> Receive Payment
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-person-check display-4 d-block mb-2"></i>
                                No customers found with due payments.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($customers->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Due Invoices and Payment Allocation --}}
    @if($selectedCustomer && count($customerSales) > 0)
    <div class="row">
        {{-- Due Invoices List --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-receipt me-2"></i> Due Invoices - {{ $selectedCustomer->name }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Invoice</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Due Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerSales as $sale)
                                <tr class="{{ $sale['due_amount'] > 0 ? 'table-warning' : '' }}">
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary">{{ $sale['invoice_number'] }}</div>
                                        <small class="text-muted">#{{ $sale['sale_id'] }}</small>
                                    </td>
                                    <td class="text-center">{{ $sale['sale_date'] }}</td>
                                    <td class="text-end">Rs.{{ number_format($sale['total_amount'], 2) }}</td>
                                    <td class="text-end text-success">Rs.{{ number_format($sale['paid_amount'], 2) }}</td>
                                    <td class="text-end fw-bold text-danger">Rs.{{ number_format($sale['due_amount'], 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $sale['payment_status'] == 'partial' ? 'warning' : 'danger' }}">
                                            {{ ucfirst($sale['payment_status']) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-info btn-sm"
                                            wire:click="viewSale({{ $sale['id'] }})"
                                            title="View Invoice Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total Due:</td>
                                    <td class="text-end fw-bold text-danger">Rs.{{ number_format($totalDueAmount, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Allocation --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-cash-coin me-2"></i> Payment Allocation
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Show total due at the top --}}
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between">
                            <span>Total Due Amount</span>
                            <span class="fw-bold">Rs.{{ number_format($totalDueAmount, 2) }}</span>
                        </div>
                    </div>

                    {{-- Add Amount Field --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Enter Payment Amount</label>
                        <input type="number" min="1" max="{{ $totalDueAmount }}" step="0.01" class="form-control form-control-lg" wire:model.lazy="totalPaymentAmount" placeholder="Enter amount to pay">
                    </div>

                    {{-- Process Payment Button --}}
                    <div class="d-grid mt-3">
                        <button class="btn btn-success btn-lg" wire:click="openPaymentModal" @if($totalPaymentAmount <=0) disabled @endif>
                            <i class="bi bi-cash-coin me-1"></i> Process Payment - Rs.{{ number_format($totalPaymentAmount, 2) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

{{-- View Sale Modal --}}
@if($showViewModal && $selectedSale)
<div class="modal fade show d-block" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-eye me-2"></i> Invoice Details - {{ $selectedSale->invoice_number }}
                </h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">INVOICE INFORMATION</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Invoice Number:</strong></td>
                                <td>{{ $selectedSale->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Sale ID:</strong></td>
                                <td>{{ $selectedSale->sale_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date & Time:</strong></td>
                                <td>{{ $selectedSale->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td class="fw-bold">Rs.{{ number_format($selectedSale->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Due Amount:</strong></td>
                                <td class="fw-bold text-danger">Rs.{{ number_format($selectedSale->due_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $selectedSale->payment_status == 'paid' ? 'success' : ($selectedSale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($selectedSale->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">CUSTOMER INFORMATION</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Name:</strong></td>
                                <td>{{ $selectedSale->customer->name ?? 'Walk-in Customer' }}</td>
                            </tr>
                            @if($selectedSale->customer)
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $selectedSale->customer->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>{{ ucfirst($selectedSale->customer_type) }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <h6 class="text-muted mb-3">ITEMS SOLD</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-center">Code</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedSale->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-center">{{ $item->product_code }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">Rs.{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">Rs.{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Payment Modal --}}
@if($showPaymentModal)
<div class="modal fade show d-block" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-credit-card me-2"></i> Confirm Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closePaymentModal"></button>
            </div>
            <div class="modal-body">
                {{-- Customer Info --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">CUSTOMER INFORMATION</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Name:</strong></td>
                                <td>{{ $selectedCustomer?->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $selectedCustomer?->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>{{ ucfirst($selectedCustomer?->type) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">PAYMENT SUMMARY</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="50%"><strong>Total Due:</strong></td>
                                <td class="text-end">Rs.{{ number_format($totalDueAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount Paid:</strong></td>
                                <td class="text-end text-success fw-bold">Rs.{{ number_format($totalPaymentAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Remaining Due:</strong></td>
                                <td class="text-end text-danger">Rs.{{ number_format($remainingAmount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Payment Details Form --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" class="form-control" wire:model="paymentData.payment_date">
                        @error('paymentData.payment_date') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <select class="form-select" wire:model="paymentData.payment_method">
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Reference Number</label>
                        <input type="text" class="form-control" wire:model="paymentData.reference_number"
                            placeholder="Enter reference number if any...">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" rows="3" wire:model="paymentData.notes"
                            placeholder="Additional payment notes..."></textarea>
                    </div>
                </div>

                {{-- Allocation Breakdown --}}
                <h6 class="text-muted mt-4 mb-3">PAYMENT ALLOCATION BREAKDOWN</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th class="text-end">Due Amount</th>
                                <th class="text-end">Payment Amount</th>
                                <th class="text-end">Remaining</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allocations as $allocation)
                            @if($allocation['payment_amount'] > 0)
                            <tr>
                                <td class="fw-bold">{{ $allocation['invoice_number'] }}</td>
                                <td class="text-end">Rs.{{ number_format($allocation['due_amount'], 2) }}</td>
                                <td class="text-end text-success fw-bold">Rs.{{ number_format($allocation['payment_amount'], 2) }}</td>
                                <td class="text-end text-danger">Rs.{{ number_format($allocation['due_amount'] - $allocation['payment_amount'], 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $allocation['is_fully_paid'] ? 'success' : 'warning' }}">
                                        {{ $allocation['is_fully_paid'] ? 'Fully Paid' : 'Partial Payment' }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closePaymentModal">Cancel</button>
                <button type="button" class="btn btn-success" wire:click="processPayment">
                    <i class="bi bi-check-circle me-1"></i> Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Receipt Modal --}}
@if($showReceiptModal && $latestPayment)
<div class="modal fade show d-block" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-check-circle me-2"></i> Payment Successful!
                </h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closeReceiptModal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-check-circle-fill text-success display-1"></i>
                    <h3 class="text-success mt-3">Payment Processed Successfully!</h3>
                    <p class="text-muted">Payment receipt has been generated and saved.</p>
                </div>

                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-receipt me-2"></i> Payment Receipt
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Receipt ID:</strong></td>
                                        <td>#{{ $latestPayment->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($latestPayment->payment_date)->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td>{{ $selectedCustomer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $selectedCustomer->phone }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td class="text-capitalize">{{ $latestPayment->payment_method }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount Paid:</strong></td>
                                        <td class="fw-bold text-success">Rs.{{ number_format($latestPayment->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reference No:</strong></td>
                                        <td>{{ $latestPayment->reference_number ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Received By:</strong></td>
                                        <td>{{ Auth::user()->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($latestPayment->notes)
                        <div class="mt-3">
                            <strong>Notes:</strong>
                            <p class="mb-0">{{ $latestPayment->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeReceiptModal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
                <button type="button" class="btn btn-success" wire:click="downloadReceipt">
                    <i class="bi bi-download me-1"></i> Download Receipt
                </button>
                <button type="button" class="btn btn-primary" wire:click="closeReceiptModal">
                    <i class="bi bi-check-circle me-1"></i> Done
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    .sticky-top {
        position: sticky;
        z-index: 10;
    }

    .allocation-items {
        max-height: 400px;
        overflow-y: auto;
    }

    .table th {
        font-weight: 600;
    }

    .badge {
        font-size: 0.75em;
    }

    .input-group-sm input {
        font-size: 0.875rem;
    }

    .modal.show {
        display: block !important;
    }

    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
    }
</style>
</div>