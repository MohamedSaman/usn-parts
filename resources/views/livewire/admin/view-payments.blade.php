<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-credit-card text-primary me-2"></i> Payment Records
            </h3>
            <p class="text-muted mb-0">View and manage all payment records</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-5">
        <!-- Total Payments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card total h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-container bg-primary bg-opacity-10 me-3">
                            <i class="bi bi-wallet text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Payments</p>
                            <h4 class="fw-bold mb-0">Rs.{{ number_format($totalPayments, 2) }}</h4>
                            <span class="badge bg-primary bg-opacity-10 text-primary">All Payments</span>
                        </div>
                    </div>
                </div>
            </div>
<<<<<<< Updated upstream

            <!-- Pending Payments Card -->
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Pending Payments</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        Rs.{{ number_format($pendingPayments, 2) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center rounded-circle">
                                    <i class="bi bi-hourglass-split text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <!-- Empty space for future stats -->
            </div>
=======
>>>>>>> Stashed changes
        </div>

        <!-- Pending Payments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card pending h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-container bg-warning bg-opacity-10 me-3">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
<<<<<<< Updated upstream

                        <!-- Filter dropdown -->
                        <div class="dropdown w-100 w-sm-auto">
                            <button class="btn btn-outline-primary dropdown-toggle shadow-sm rounded-pill w-100"
                                type="button" id="filterDropdown" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-funnel me-1"></i> Filters
                                @if ($filters['status'] || $filters['paymentMethod'] || $filters['dateRange'])
                                <span class="badge bg-primary ms-1">!</span>
                                @endif
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg dropdown-menu-end" style="width: 300px;"
                                aria-labelledby="filterDropdown">
                                <h6 class="dropdown-header bg-light rounded-3 py-2 mb-3 text-center">Filter Options</h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Payment Status</label>
                                    <select class="form-select form-select-sm rounded-pill"
                                        wire:model.live="filters.status">
                                        <option value="">All Statuses</option>
                                        <option value="pending">Pending Approval</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Payment Method</label>
                                    <select class="form-select form-select-sm rounded-pill"
                                        wire:model.live="filters.paymentMethod">
                                        <option value="">All Methods</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="credit">Credit</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Date Range</label>
                                    <input type="text" class="form-control form-control-sm rounded-pill"
                                        placeholder="Select date range" wire:model.live="filters.dateRange">
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-sm btn-secondary rounded-pill"
                                        wire:click="resetFilters">
                                        <i class="bi bi-arrow-repeat me-1"></i> Reset Filters
                                    </button>
                                </div>
                            </div>
=======
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Pending Payments</p>
                            <h4 class="fw-bold mb-0">Rs.{{ number_format($pendingPayments, 2) }}</h4>
                            <span class="badge bg-warning bg-opacity-10 text-warning">Awaiting</span>
>>>>>>> Stashed changes
                        </div>
                    </div>
                </div>
            </div>
        </div>

<<<<<<< Updated upstream
            <!-- Payments Table -->
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Invoice</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Customer</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Amount</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Method</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Staff</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                            <tr>
                                <td>
                                    <div class="d-flex px-3 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $payment->sale->invoice_number }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">
                                        {{ $payment->sale->customer->name ?? 'Walk-in Customer' }}
                                    </p>
                                    <p class="text-xs text-secondary mb-0">
                                        {{ $payment->sale->customer->phone ?? 'N/A' }}
                                    </p>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">Rs.{{ number_format($payment->amount, 2) }}</p>
                                    <p class="text-xs {{ $payment->is_completed ? 'text-success' : 'text-warning' }} mb-0">
                                        {{ $payment->is_completed ? 'Completed' : 'Scheduled' }}
                                    </p>
                                </td>
                                <td>
                                    <div class="text-sm font-weight-bold mb-0 badge bg-light text-dark">
                                        {{ ucfirst($payment->payment_method) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                            $payment->status === 'pending' ? 'warning' : 
                                            ($payment->status === 'approved' ? 'success' : 
                                            ($payment->status === 'rejected' ? 'danger' : 
                                            ($payment->is_completed ? 'success' : 'secondary'))) }}">
                                        {{ $payment->status ? ucfirst($payment->status) : ($payment->is_completed ? 'Paid' : 'Scheduled') }}
                                    </span>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">
                                        {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 
                                               ($payment->due_date ? 'Due: '.$payment->due_date->format('d M Y') : 'N/A') }}
                                    </p>
                                    <p class="text-xs text-secondary mb-0">
                                        {{ $payment->payment_date ? $payment->payment_date->format('h:i A') : '' }}
                                    </p>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">{{ $payment->sale->user->name ?? 'N/A' }}</p>
                                </td>
                                <td class="align-middle">
                                    <button class="btn btn-link text-secondary mb-0" wire:click="viewPaymentDetails({{ $payment->id }})">
                                        <i class="bi bi-receipt-cutoff text-xs"></i> View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No payment records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
=======
        <!-- Approved Payments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card approved h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-container bg-success bg-opacity-10 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Approved Payments</p>
                            <h4 class="fw-bold mb-0">Rs.{{ number_format($approvedPayments, 2) }}</h4>
                            <span class="badge bg-success bg-opacity-10 text-success">Confirmed</span>
                        </div>
                    </div>
>>>>>>> Stashed changes
                </div>
            </div>
        </div>

        <!-- Completed Payments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card completed h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-container bg-info bg-opacity-10 me-3">
                            <i class="bi bi-currency-dollar text-info fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Completed Payments</p>
                            <h4 class="fw-bold mb-0">Rs.{{ number_format($completedPayments, 2) }}</h4>
                            <span class="badge bg-info bg-opacity-10 text-info">Paid</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Records Table --}}
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-journal-text text-primary me-2"></i> Payment Records
                </h5>
                <p class="text-muted small mb-0">View and manage all payment transactions</p>
            </div>
        </div>
        <div class="card-body">
            {{-- Search and Filters --}}
            <div class="row g-3 mb-4">
                <!-- Search Bar -->
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" 
                               placeholder="Search invoices or customers..."
                               wire:model.live.debounce.300ms="search">
                    </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-md-6">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" 
                                id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel me-2"></i> Filters
                            @if ($filters['status'] || $filters['paymentMethod'] || $filters['dateRange'])
                                <span class="badge bg-primary ms-1">!</span>
                            @endif
                        </button>
                        <div class="dropdown-menu p-3 dropdown-menu-end" style="width: 300px;" 
                             aria-labelledby="filterDropdown">
                            <h6 class="fw-bold text-dark mb-3">Filter Options</h6>
                            
                            <!-- Status Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Payment Status</label>
                                <select class="form-select" wire:model.live="filters.status">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending Approval</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            
                            <!-- Payment Method Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Payment Method</label>
                                <select class="form-select" wire:model.live="filters.paymentMethod">
                                    <option value="">All Methods</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            
                            <!-- Date Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Date Range</label>
                                <input type="text" class="form-control" 
                                       placeholder="Select date range" 
                                       wire:model.live="filters.dateRange">
                            </div>
                            
                            <!-- Clear Filters -->
                            @if ($filters['status'] || $filters['paymentMethod'] || $filters['dateRange'])
                            <div class="d-grid">
                                <button class="btn btn-secondary" wire:click="resetFilters">
                                    <i class="bi bi-arrow-repeat me-1"></i> Reset Filters
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payments Table --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Customer</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Method</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Staff</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="ps-4">
                                    <div>
                                        <span class="fw-medium text-dark">{{ $payment->sale->invoice_number }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container bg-primary bg-opacity-10 me-3">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium text-dark">{{ $payment->sale->customer->name ?? 'Walk-in Customer' }}</span>
                                            @if($payment->sale->customer && $payment->sale->customer->phone)
                                            <div class="text-muted small">{{ $payment->sale->customer->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark">Rs.{{ number_format($payment->amount, 2) }}</span>
                                    <div class="text-muted small">
                                        {{ $payment->is_completed ? 'Completed' : 'Scheduled' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ ucfirst($payment->payment_method) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ 
                                        $payment->status === 'pending' ? 'warning' : 
                                        ($payment->status === 'approved' ? 'success' : 
                                        ($payment->status === 'rejected' ? 'danger' : 
                                        ($payment->is_completed ? 'success' : 'secondary'))) }}">
                                        {{ $payment->status ? ucfirst($payment->status) : ($payment->is_completed ? 'Paid' : 'Scheduled') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div>
                                        <span class="fw-medium text-dark">
                                            {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 
                                               ($payment->due_date ? 'Due: '.$payment->due_date->format('d M Y') : 'N/A') }}
                                        </span>
                                        @if($payment->payment_date)
                                        <div class="text-muted small">{{ $payment->payment_date->format('h:i A') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-medium text-dark">{{ $payment->sale->user->name ?? 'N/A' }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-link text-primary p-0" 
                                            wire:click="viewPaymentDetails({{ $payment->id }})"
                                            wire:loading.attr="disabled"
                                            title="View Receipt">
                                        <i class="bi bi-receipt fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-credit-card display-4 d-block mb-2"></i>
                                    No payment records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    <!-- Payment Receipt Modal -->
    <div wire:ignore.self class="modal fade" id="payment-receipt-modal" tabindex="-1" aria-labelledby="payment-receipt-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt text-primary me-2"></i> Payment Receipt
                    </h5>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="printReceiptContent()">
                            <i class="bi bi-printer me-1"></i> Print
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4" id="receiptContent">
                    @if ($selectedPayment)
<<<<<<< Updated upstream
                    <div class="receipt-container">
                        <!-- Receipt Header -->
                        <div class="text-center mb-4">
                            <h3 class="mb-0">USN Auto Parts</h3>
                            <p class="mb-0 text-muted small">103 H,Yatiyanthota Road,Seethawaka,avissawella.</p>
                            <p class="mb-0 text-muted small">Phone: ( 076) 9085252| Email: autopartsusn@gmail.com </p>
                            <h4 class="mt-3 border-bottom border-2 pb-2">PAYMENT RECEIPT</h4>
                        </div>

                        <!-- Payment Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">PAYMENT INFORMATION</h6>
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <p class="mb-1"><strong>Payment ID:</strong> #{{ $selectedPayment->id }}</p>
                                        <p class="mb-1"><strong>Amount:</strong> Rs.{{ number_format($selectedPayment->amount, 2) }}</p>
                                        <p class="mb-1"><strong>Method:</strong> {{ ucfirst($selectedPayment->payment_method) }}</p>
                                        <p class="mb-1"><strong>Date:</strong>
                                            {{ $selectedPayment->payment_date ? 
                                                   $selectedPayment->payment_date->format('d/m/Y h:i A') : 
                                                   'N/A' }}
                                        </p>
                                        <p class="mb-1"><strong>Status:</strong>
                                            <span class="badge bg-{{ 
                                                    $selectedPayment->status === 'pending' ? 'warning' : 
                                                    ($selectedPayment->status === 'approved' ? 'success' : 
                                                    ($selectedPayment->status === 'rejected' ? 'danger' : 
                                                    ($selectedPayment->is_completed ? 'success' : 'secondary'))) }}">
                                                {{ $selectedPayment->status ? ucfirst($selectedPayment->status) : 
                                                       ($selectedPayment->is_completed ? 'Paid' : 'Scheduled') }}
                                            </span>
                                        </p>

                                        @if($selectedPayment->payment_method == 'cheque' || $selectedPayment->payment_method == 'bank_transfer')
                                        <div class="mt-3 pt-3 border-top">
                                            <h6 class="fw-bold mb-2">
                                                {{ $selectedPayment->payment_method == 'cheque' ? 'CHEQUE DETAILS' : 'BANK TRANSFER DETAILS' }}
                                            </h6>

                                            <!-- Payment Reference -->
                                            @if($selectedPayment->payment_reference)
                                            <div class="mb-3">
                                                <p class="mb-1"><strong>Payment Reference:</strong></p>
                                                @if(pathinfo($selectedPayment->payment_reference, PATHINFO_EXTENSION) == 'pdf')
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-danger me-2"><i class="bi bi-file-pdf"></i> PDF</span>
                                                    <a href="{{ asset('storage/' . $selectedPayment->payment_reference) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        target="_blank">
                                                        <i class="bi bi-eye me-1"></i> View Document
                                                    </a>
                                                    <a href="{{ asset('storage/' . $selectedPayment->payment_reference) }}"
                                                        class="btn btn-sm btn-outline-secondary ms-2"
                                                        download>
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </a>
                                                </div>
                                                @else
                                                <img src="{{ asset('storage/' . $selectedPayment->payment_reference) }}"
                                                    class="img-fluid border rounded" style="max-height: 120px"
                                                    alt="Payment reference" onclick="openFullImage('{{ asset('storage/' . $selectedPayment->payment_reference) }}')">
                                                @endif
                                            </div>
                                            @endif

                                            <!-- Additional Banking Details if Available -->
                                            @if($selectedPayment->bank_name)
                                            <p class="mb-1"><strong>Bank:</strong> {{ $selectedPayment->bank_name }}</p>
                                            @endif

                                            @if($selectedPayment->card_number)
                                            <p class="mb-1"><strong>Account/Card Number:</strong> {{ $selectedPayment->card_number }}</p>
                                            @endif

                                            <!-- Due Payment Attachment (if exists) -->
                                            {{-- @if($selectedPayment->due_payment_attachment)
                                                        <div class="mt-3">
                                                            <p class="mb-1"><strong>Additional Documentation:</strong></p>
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ asset('storage/' . $selectedPayment->due_payment_attachment) }}"
                                            class="img-fluid border rounded" style="max-height: 120px"
                                            alt="Payment proof" onclick="openFullImage('{{ asset('storage/' . $selectedPayment->due_payment_attachment) }}')">
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Due Payment Method (if exists) -->
                                    @if($selectedPayment->due_payment_method)
                                    <p class="mb-1 mt-2"><strong>Due Payment Method:</strong> {{ ucfirst($selectedPayment->due_payment_method) }}</p>
                                    @endif --}}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">INVOICE INFORMATION</h6>
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-3">
                                <p class="mb-1"><strong>Invoice:</strong> {{ $selectedPayment->sale->invoice_number }}</p>
                                <p class="mb-1"><strong>Sale Date:</strong> {{ $selectedPayment->sale->created_at->format('d/m/Y h:i A') }}</p>
                                <p class="mb-1"><strong>Subtotal:</strong> Rs.{{ number_format($selectedPayment->sale->subtotal, 2) }}</p>
                                @if($selectedPayment->sale->discount_amount > 0)
                                <p class="mb-1"><strong>Discount:</strong> Rs.{{ number_format($selectedPayment->sale->discount_amount, 2) }}</p>
                                @endif
                                <p class="mb-1"><strong>Total:</strong> Rs.{{ number_format($selectedPayment->sale->total_amount, 2) }}</p>
                                <p class="mb-1"><strong>Due Amount:</strong> Rs.{{ number_format($selectedPayment->sale->due_amount, 2) }}</p>
                                <p class="mb-0"><strong>Payment Status:</strong>
                                    <span class="badge bg-{{ $selectedPayment->sale->payment_status == 'paid' ? 'success' : ($selectedPayment->sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($selectedPayment->sale->payment_status) }}
                                    </span>
                                </p>
                            </div>
=======
                        <div class="receipt-container">
                            <!-- Receipt Header -->
                            <div class="text-center mb-4">
                                <h3 class="mb-0">USN Auto Parts</h3>
                                <p class="mb-0 text-muted small">103 H,Yatiyanthota Road,Seethawaka,avissawella.</p>
                                <p class="mb-0 text-muted small">Phone: (076) 9085252 | Email: autopartsusn@gmail.com</p>
                                <h4 class="mt-3 border-bottom border-2 pb-2">PAYMENT RECEIPT</h4>
                            </div>

                            <!-- Payment Details -->
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="fw-bold text-dark mb-0">
                                                <i class="bi bi-credit-card text-primary me-2"></i> Payment Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Payment ID</label>
                                                    <p class="fw-bold text-dark mb-0">#{{ $selectedPayment->id }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Amount</label>
                                                    <p class="fw-bold text-primary h5 mb-0">Rs.{{ number_format($selectedPayment->amount, 2) }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Method</label>
                                                    <p class="fw-bold text-dark mb-0">{{ ucfirst($selectedPayment->payment_method) }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Date</label>
                                                    <p class="fw-bold text-dark mb-0">
                                                        {{ $selectedPayment->payment_date ? 
                                                           $selectedPayment->payment_date->format('d/m/Y h:i A') : 
                                                           'N/A' }}
                                                    </p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Status</label>
                                                    <div>
                                                        <span class="badge bg-{{ 
                                                            $selectedPayment->status === 'pending' ? 'warning' : 
                                                            ($selectedPayment->status === 'approved' ? 'success' : 
                                                            ($selectedPayment->status === 'rejected' ? 'danger' : 
                                                            ($selectedPayment->is_completed ? 'success' : 'secondary'))) }}">
                                                            {{ $selectedPayment->status ? ucfirst($selectedPayment->status) : 
                                                               ($selectedPayment->is_completed ? 'Paid' : 'Scheduled') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="fw-bold text-dark mb-0">
                                                <i class="bi bi-file-text text-primary me-2"></i> Invoice Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Invoice</label>
                                                    <p class="fw-bold text-dark mb-0">{{ $selectedPayment->sale->invoice_number }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Sale Date</label>
                                                    <p class="fw-bold text-dark mb-0">{{ $selectedPayment->sale->created_at->format('d/m/Y h:i A') }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Subtotal</label>
                                                    <p class="fw-bold text-dark mb-0">Rs.{{ number_format($selectedPayment->sale->subtotal, 2) }}</p>
                                                </div>
                                                @if($selectedPayment->sale->discount_amount > 0)
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Discount</label>
                                                    <p class="fw-bold text-danger mb-0">Rs.{{ number_format($selectedPayment->sale->discount_amount, 2) }}</p>
                                                </div>
                                                @endif
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Total</label>
                                                    <p class="fw-bold text-success h5 mb-0">Rs.{{ number_format($selectedPayment->sale->total_amount, 2) }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Due Amount</label>
                                                    <p class="fw-bold text-dark mb-0">Rs.{{ number_format($selectedPayment->sale->due_amount, 2) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer & Staff Information -->
                            <div class="row g-4 mb-4">
                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="fw-bold text-dark mb-0">
                                                <i class="bi bi-person text-primary me-2"></i> Customer Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="icon-container bg-primary bg-opacity-10 me-3">
                                                    <i class="bi bi-person text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $selectedPayment->sale->customer->name ?? 'Guest Customer' }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        {{ ucfirst($selectedPayment->sale->customer_type) }} Customer
                                                    </p>
                                                </div>
                                            </div>
                                            @if($selectedPayment->sale->customer)
                                                <div class="text-muted small">
                                                    <div class="mb-1">
                                                        <i class="bi bi-telephone me-2"></i>{{ $selectedPayment->sale->customer->phone }}
                                                    </div>
                                                    @if($selectedPayment->sale->customer->email)
                                                    <div class="mb-1">
                                                        <i class="bi bi-envelope me-2"></i>{{ $selectedPayment->sale->customer->email }}
                                                    </div>
                                                    @endif
                                                    @if($selectedPayment->sale->customer->address)
                                                    <div class="mb-0">
                                                        <i class="bi bi-geo-alt me-2"></i>{{ $selectedPayment->sale->customer->address }}
                                                    </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="fw-bold text-dark mb-0">
                                                <i class="bi bi-person-badge text-primary me-2"></i> Staff Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="icon-container bg-info bg-opacity-10 me-3">
                                                    <i class="bi bi-person-badge text-info"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $selectedPayment->sale->user->name ?? 'Unknown' }}</h6>
                                                    <p class="text-muted small mb-0">Staff Member</p>
                                                </div>
                                            </div>
                                            @if($selectedPayment->sale->user)
                                                <div class="text-muted small">
                                                    <div class="mb-0">
                                                        <i class="bi bi-envelope me-2"></i>{{ $selectedPayment->sale->user->email }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Attachments -->
                            @if($selectedPayment->payment_method == 'cheque' || $selectedPayment->payment_method == 'bank_transfer')
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="bi bi-paperclip text-primary me-2"></i>
                                        {{ $selectedPayment->payment_method == 'cheque' ? 'Cheque Details' : 'Bank Transfer Details' }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($selectedPayment->payment_reference)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-muted small">Payment Reference</label>
                                        @if(pathinfo($selectedPayment->payment_reference, PATHINFO_EXTENSION) == 'pdf')
                                        <div class="d-flex align-items-center">
                                            <div class="icon-container bg-danger bg-opacity-10 me-3">
                                                <i class="bi bi-file-pdf text-danger"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="fw-bold text-dark mb-1">PDF Document</p>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ asset('storage/' . $selectedPayment->payment_reference) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="bi bi-eye me-1"></i> View
                                                    </a>
                                                    <a href="{{ asset('storage/' . $selectedPayment->payment_reference) }}" 
                                                       class="btn btn-sm btn-outline-secondary" 
                                                       download>
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $selectedPayment->payment_reference) }}" 
                                                class="img-fluid border rounded" 
                                                style="max-height: 200px; cursor: pointer;"
                                                alt="Payment reference" 
                                                onclick="openFullImage('{{ asset('storage/' . $selectedPayment->payment_reference) }}')">
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $selectedPayment->payment_reference) }}" 
                                                   class="btn btn-sm btn-outline-primary me-2" 
                                                   download>
                                                    <i class="bi bi-download me-1"></i> Download
                                                </a>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="openFullImage('{{ asset('storage/' . $selectedPayment->payment_reference) }}')">
                                                    <i class="bi bi-zoom-in me-1"></i> Zoom
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    @if($selectedPayment->bank_name)
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold text-muted small">Bank Name</label>
                                        <p class="fw-bold text-dark mb-0">{{ $selectedPayment->bank_name }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($selectedPayment->card_number)
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold text-muted small">Account/Card Number</label>
                                        <p class="fw-bold text-dark mb-0">{{ $selectedPayment->card_number }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Sale Items -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="bi bi-list-ul text-primary me-2"></i> Purchased Items
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4">#</th>
                                                    <th>Item</th>
                                                    <th>Code</th>
                                                    <th class="text-center">Price</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-center">Discount</th>
                                                    <th class="text-end pe-4">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($selectedPayment->sale->items as $index => $item)
                                                <tr>
                                                    <td class="ps-4">{{ $index + 1 }}</td>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->product_code }}</td>
                                                    <td class="text-center">Rs.{{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-center">Rs.{{ number_format($item->discount, 2) }}</td>
                                                    <td class="text-end pe-4">Rs.{{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            @if ($selectedPayment->sale->notes)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="bi bi-sticky text-primary me-2"></i> Notes
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $selectedPayment->sale->notes }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Footer -->
                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="mb-0 text-muted small">This is a computer-generated receipt.</p>
                                <p class="mb-0 text-muted small">{{ now()->format('d/m/Y h:i A') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading payment information...</p>
>>>>>>> Stashed changes
                        </div>
                    </div>
                </div>

                <!-- Customer & Staff Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">CUSTOMER INFORMATION</h6>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar rounded-circle bg-primary text-white me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <span>{{ substr($selectedPayment->sale->customer->name ?? 'G', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $selectedPayment->sale->customer->name ?? 'Guest Customer' }}</h6>
                                        <p class="text-muted mb-0 small">
                                            {{ ucfirst($selectedPayment->sale->customer_type) }} Customer
                                        </p>
                                    </div>
                                </div>
                                @if($selectedPayment->sale->customer)
                                <p class="mb-1 small"><i class="bi bi-telephone me-2"></i>{{ $selectedPayment->sale->customer->phone }}</p>
                                @if($selectedPayment->sale->customer->email)
                                <p class="mb-1 small"><i class="bi bi-envelope me-2"></i>{{ $selectedPayment->sale->customer->email }}</p>
                                @endif
                                @if($selectedPayment->sale->customer->address)
                                <p class="mb-0 small"><i class="bi bi-geo-alt me-2"></i>{{ $selectedPayment->sale->customer->address }}</p>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">STAFF INFORMATION</h6>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar rounded-circle bg-info text-white me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <span>{{ substr($selectedPayment->sale->user->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $selectedPayment->sale->user->name ?? 'Unknown' }}</h6>
                                        <p class="text-muted mb-0 small">Staff Member</p>
                                    </div>
                                </div>
                                @if($selectedPayment->sale->user)
                                <p class="mb-1 small"><i class="bi bi-envelope me-2"></i>{{ $selectedPayment->sale->user->email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sale Items Section -->
                <h6 class="text-muted mb-2">PURCHASED ITEMS</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Item</th>
                                <th scope="col">Code</th>
                                <th scope="col">Price</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Discount</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selectedPayment->sale->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->product_code }}</td>
                                <td>Rs.{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rs.{{ number_format($item->discount, 2) }}</td>
                                <td>Rs.{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Notes Section -->
                @if ($selectedPayment->sale->notes)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Notes</h6>
                                <p class="card-text">{{ $selectedPayment->sale->notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Footer -->
                <div class="text-center mt-4 pt-3 border-top">
                    <p class="mb-0 text-muted small">This is a computer-generated receipt.</p>
                    <p class="mb-0 text-muted small">{{ now()->format('d/m/Y h:i A') }}</p>
                </div>
            </div>
            @else
            <div class="text-center p-5">
                <p class="text-muted">Loading payment information...</p>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

<<<<<<< Updated upstream
@push('scripts')
<script>
    // Define printReceiptContent in global scope
    window.printReceiptContent = function() {
        const printContent = document.getElementById('receiptContent').cloneNode(true);

        // Create an iframe for printing
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        // Get the iframe's document
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        // Write the print content to the iframe
        iframeDoc.open();
        iframeDoc.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Payment Receipt</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 20px;
                            font-size: 14px;
                        }
                        .receipt-container {
                            width: 100%;
                            max-width: 800px;
                            margin: 0 auto;
                        }
                        .row {
                            display: flex;
                            flex-wrap: wrap;
                            margin-right: -15px;
                            margin-left: -15px;
                        }
                        .col-md-6 {
                            flex: 0 0 50%;
                            max-width: 50%;
                            padding-right: 15px;
                            padding-left: 15px;
                        }
                        .col-12 {
                            flex: 0 0 100%;
                            max-width: 100%;
                            padding-right: 15px;
                            padding-left: 15px;
                        }
                        .card {
                            border: 1px solid #eee;
                            border-radius: 8px;
                            margin-bottom: 15px;
                        }
                        .card-body {
                            padding: 15px;
                        }
                        .table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 1rem;
                        }
                        .table th, .table td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        .table thead th {
                            background-color: #f5f5f5;
                        }
                        .text-center {
                            text-align: center;
                        }
                        .badge {
                            display: inline-block;
                            padding: 0.25em 0.4em;
                            font-weight: bold;
                            line-height: 1;
                            text-align: center;
                            white-space: nowrap;
                            vertical-align: baseline;
                            border-radius: 0.25rem;
                        }
                        .bg-success {
                            background-color: #28a745;
                            color: white;
                        }
                        .bg-warning {
                            background-color: #ffc107;
                            color: black;
                        }
                        .bg-danger {
                            background-color: #dc3545;
                            color: white;
                        }
                        .avatar {
                            display: none;
                        }
                        .border-top {
                            border-top: 1px solid #dee2e6;
                        }
                        .pt-3 {
                            padding-top: 1rem;
                        }
                        .mt-4 {
                            margin-top: 1.5rem;
                        }
                        .mb-4 {
                            margin-bottom: 1.5rem;
                        }
                        /* Hide modal elements */
                        .modal-header, .modal-footer, button {
                            display: none !important;
                        }
                    </style>
                </head>
                <body>
                    ${printContent.innerHTML}
                </body>
                </html>
            `);
        iframeDoc.close();

        // Wait for the iframe content to load
        iframe.onload = function() {
            try {
                // Focus iframe window
                iframe.contentWindow.focus();
                // Print the iframe
                iframe.contentWindow.print();
            } catch (e) {
                console.error('Print error:', e);
                alert('Print failed. Please try again.');
            }

            // Remove the iframe after printing
            setTimeout(function() {
                document.body.removeChild(iframe);
            }, 1000);
        };
    };

    function openFullImage(imageUrl) {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        const fullSizeImage = document.getElementById('fullSizeImage');
        const downloadLink = document.getElementById('downloadImageLink');

        // Set image source
        fullSizeImage.src = imageUrl;

        // Set download link attributes
        downloadLink.href = imageUrl;
        downloadLink.setAttribute('download', 'payment-reference-' + new Date().getTime() + '.jpg');

        // Show modal
        modal.show();

        // Initialize zoom functionality
        let currentZoom = 1;
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');

        zoomInBtn.onclick = function() {
            currentZoom += 0.25;
            if (currentZoom > 3) currentZoom = 3;
            fullSizeImage.style.transform = `scale(${currentZoom})`;
        };

        zoomOutBtn.onclick = function() {
            currentZoom -= 0.25;
            if (currentZoom < 0.5) currentZoom = 0.5;
            fullSizeImage.style.transform = `scale(${currentZoom})`;
        };

        // Reset zoom when modal is closed
        document.getElementById('imageModal').addEventListener('hidden.bs.modal', function() {
            fullSizeImage.style.transform = 'scale(1)';
            currentZoom = 1;
        });
    }

    document.addEventListener('livewire:initialized', () => {
        // Helper function to handle modal operations safely
        const handleModal = (modalId, action) => {
            const modalElement = document.getElementById(modalId);

            // First, check if a modal instance already exists and dispose it
            const existingModal = bootstrap.Modal.getInstance(modalElement);
            if (existingModal) {
                existingModal.dispose();
            }

            if (action === 'show') {
                // Create a fresh modal instance and show it
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        };

        Livewire.on('openModal', (modalId) => {
            handleModal(modalId, 'show');
        });
    });
</script>
@endpush

<!-- Image Modal for viewing attachments -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Attachment</h5>
                <div class="ms-auto">
                    <a id="downloadImageLink" href="#" class="btn btn-sm btn-primary me-2" download>
                        <i class="bi bi-download"></i> Download
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
=======
    <!-- Image Modal for viewing attachments -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Payment Attachment</h5>
                    <div class="ms-auto">
                        <a id="downloadImageLink" href="#" class="btn btn-sm btn-primary me-2" download>
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
>>>>>>> Stashed changes
                </div>
            </div>
            <div class="modal-body text-center p-0">
                <div class="position-relative">
                    <img id="fullSizeImage" src="" class="img-fluid w-100" alt="Payment proof">
                    <div class="position-absolute top-0 end-0 p-3">
                        <button id="zoomInBtn" class="btn btn-sm btn-light rounded-circle me-1">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <button id="zoomOutBtn" class="btn btn-sm btn-light rounded-circle">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<<<<<<< Updated upstream
</div>
=======

@push('styles')
<style>
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

    .summary-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .summary-card.total {
        border-left-color: #4361ee;
    }

    .summary-card.pending {
        border-left-color: #ffc107;
    }

    .summary-card.approved {
        border-left-color: #198754;
    }

    .summary-card.completed {
        border-left-color: #0dcaf0;
    }

    .icon-container {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    .btn-outline-primary {
        color: #4361ee;
        border-color: #4361ee;
    }

    .btn-outline-primary:hover {
        background-color: #4361ee;
        border-color: #4361ee;
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 8px;
        border: none;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
    }

    .input-group-text {
        border-radius: 8px 0 0 8px;
    }

    .dropdown-menu {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    /* Print Styles */
    @media print {
        body * {
            visibility: hidden;
        }
        #receiptContent,
        #receiptContent * {
            visibility: visible;
        }
        #receiptContent {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .modal-header,
        .modal-footer,
        .btn {
            display: none !important;
        }
        .modal-body {
            padding: 0 !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Define printReceiptContent in global scope
    window.printReceiptContent = function() {
        const printContent = document.getElementById('receiptContent').cloneNode(true);
        
        // Create an iframe for printing
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        
        // Get the iframe's document
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        
        // Write the print content to the iframe
        iframeDoc.open();
        iframeDoc.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print Payment Receipt</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                        font-size: 14px;
                    }
                    .receipt-container {
                        width: 100%;
                        max-width: 800px;
                        margin: 0 auto;
                    }
                    .row {
                        display: flex;
                        flex-wrap: wrap;
                        margin-right: -15px;
                        margin-left: -15px;
                    }
                    .col-md-6 {
                        flex: 0 0 50%;
                        max-width: 50%;
                        padding-right: 15px;
                        padding-left: 15px;
                    }
                    .col-12 {
                        flex: 0 0 100%;
                        max-width: 100%;
                        padding-right: 15px;
                        padding-left: 15px;
                    }
                    .card {
                        border: 1px solid #eee;
                        border-radius: 8px;
                        margin-bottom: 15px;
                    }
                    .card-body {
                        padding: 15px;
                    }
                    .table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 1rem;
                    }
                    .table th, .table td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    .table thead th {
                        background-color: #f5f5f5;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .badge {
                        display: inline-block;
                        padding: 0.25em 0.4em;
                        font-weight: bold;
                        line-height: 1;
                        text-align: center;
                        white-space: nowrap;
                        vertical-align: baseline;
                        border-radius: 0.25rem;
                    }
                    .bg-warning { background-color: #ffc107; color: #000; }
                    .bg-success { background-color: #198754; color: #fff; }
                    .bg-danger { background-color: #dc3545; color: #fff; }
                    .bg-secondary { background-color: #6c757d; color: #fff; }
                    .text-primary { color: #4361ee !important; }
                    .text-success { color: #198754 !important; }
                    .text-danger { color: #dc3545 !important; }
                    .fw-bold { font-weight: bold; }
                    .fw-semibold { font-weight: 600; }
                    .text-muted { color: #6c757d; }
                    .border-bottom { border-bottom: 1px solid #dee2e6; }
                    .border-top { border-top: 1px solid #dee2e6; }
                    .mb-0 { margin-bottom: 0; }
                    .mb-1 { margin-bottom: 0.25rem; }
                    .mb-2 { margin-bottom: 0.5rem; }
                    .mb-3 { margin-bottom: 1rem; }
                    .mb-4 { margin-bottom: 1.5rem; }
                    .mt-3 { margin-top: 1rem; }
                    .mt-4 { margin-top: 1.5rem; }
                    .pt-3 { padding-top: 1rem; }
                    .pb-2 { padding-bottom: 0.5rem; }
                    .small { font-size: 0.875rem; }
                    .h5 { font-size: 1.25rem; }
                    .h4 { font-size: 1.5rem; }
                    .h3 { font-size: 1.75rem; }
                </style>
            </head>
            <body>
                ${printContent.innerHTML}
            </body>
            </html>
        `);
        iframeDoc.close();
        
        // Print the iframe content
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        
        // Remove the iframe after printing
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000);
    }

    // Image modal functionality
    function openFullImage(imageSrc) {
        const fullSizeImage = document.getElementById('fullSizeImage');
        const downloadImageLink = document.getElementById('downloadImageLink');
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        
        fullSizeImage.src = imageSrc;
        downloadImageLink.href = imageSrc;
        
        // Reset zoom
        fullSizeImage.style.transform = 'scale(1)';
        fullSizeImage.style.transformOrigin = 'center center';
        
        // Zoom functionality
        let currentScale = 1;
        const zoomStep = 0.2;
        
        zoomInBtn.onclick = function() {
            currentScale += zoomStep;
            fullSizeImage.style.transform = `scale(${currentScale})`;
        };
        
        zoomOutBtn.onclick = function() {
            if (currentScale > 1) {
                currentScale -= zoomStep;
                fullSizeImage.style.transform = `scale(${currentScale})`;
            }
        };
        
        // Initialize modal
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    // Livewire event listeners for opening payment modal
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('open-payment-modal', function() {
            const modal = new bootstrap.Modal(document.getElementById('payment-receipt-modal'));
            modal.show();
        });
    });
</script>
@endpush
>>>>>>> Stashed changes
