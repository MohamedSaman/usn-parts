<div>
    <div class="container-fluid py-4">
        <!-- Stats Bar -->
        <div class="row mb-4">
            <!-- Pending Approvals Card -->
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card border-0 overflow-hidden shadow-sm hover-lift">
                    <div class="card-body p-0">
                        <div class="d-flex">
                            <div class="p-4 flex-grow-1">
                                <h6 class="text-sm text-uppercase fw-bold text-muted mb-1">Pending Sales</h6>
                                <div class="d-flex align-items-center">
                                    <h2 class="font-weight-bolder mb-0">
                                        {{ $pendingCount }}
                                    </h2>
                                    <span class="badge bg-primary-subtle text-primary ms-2 py-1 px-2">Awaiting Review
                                    </span>
                                </div>
                            </div>
                            <div class="bg-gradient-primary p-4 d-flex align-items-center">
                                <div class="icon-shape icon-lg bg-white bg-opacity-25 shadow-sm rounded-3 text-center">
                                    <i class="bi bi-hourglass-split text-white fs-4" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar indicator -->
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 65%"></div>
                    </div>
                </div>
            </div>

            <!-- Approved Card -->
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card border-0 overflow-hidden shadow-sm hover-lift">
                    <div class="card-body p-0">
                        <div class="d-flex">
                            <div class="p-4 flex-grow-1">
                                <h6 class="text-sm text-uppercase fw-bold text-muted mb-1">Total Approved</h6>
                                <div class="d-flex align-items-center">
                                    <h2 class="font-weight-bolder mb-0">
                                        {{ $approvedCount }}
                                    </h2>
                                    <span class="badge bg-success-subtle text-success ms-2 py-1 px-2">Confirmed</span>
                                </div>
                            </div>
                            <div class="bg-gradient-success p-4 d-flex align-items-center">
                                <div class="icon-shape icon-lg bg-white bg-opacity-25 shadow-sm rounded-3 text-center">
                                    <i class="bi bi-check-circle text-white fs-4" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar indicator -->
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 80%"></div>
                    </div>
                </div>
            </div>

            <!-- Rejected Card -->
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card border-0 overflow-hidden shadow-sm hover-lift">
                    <div class="card-body p-0">
                        <div class="d-flex">
                            <div class="p-4 flex-grow-1">
                                <h6 class="text-sm text-uppercase fw-bold text-muted mb-1">Rejected</h6>
                                <div class="d-flex align-items-center">
                                    <h2 class="font-weight-bolder mb-0">
                                        {{ $rejectedCount }}
                                    </h2>
                                    <span class="badge bg-danger-subtle text-danger ms-2 py-1 px-2">Declined</span>
                                </div>
                            </div>
                            <div class="bg-gradient-danger p-4 d-flex align-items-center">
                                <div class="icon-shape icon-lg bg-white bg-opacity-25 shadow-sm rounded-3 text-center">
                                    <i class="bi bi-x-circle text-white fs-4" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar indicator -->
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: 35%"></div>
                    </div>
                </div>
            </div>

            <!-- Today's Actions Card -->
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 overflow-hidden shadow-sm hover-lift">
                    <div class="card-body p-0">
                        <div class="d-flex">
                            <div class="p-4 flex-grow-1">
                                <h6 class="text-sm text-uppercase fw-bold text-muted mb-1">Today's Actions</h6>
                                <div class="d-flex align-items-center">
                                    <h2 class="font-weight-bolder mb-0">
                                        {{ $todayCount }}
                                    </h2>
                                    <span class="badge bg-info-subtle text-info ms-2 py-1 px-2">Today</span>
                                </div>
                            </div>
                            <div class="bg-gradient-info p-4 d-flex align-items-center">
                                <div class="icon-shape icon-lg bg-white bg-opacity-25 shadow-sm rounded-3 text-center">
                                    <i class="bi bi-calendar-check text-white fs-4" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar indicator -->
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 50%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 p-3">
                    <div class="card-header pb-2 bg-light">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h5 class="mb-0">Sales Approvals</h5>
                                <p class="text-sm mb-0 text-muted">Manage staff sales submissions</p>
                            </div>
                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                                <!-- Search bar -->
                                <div class="input-group input-group-merge border rounded-pill bg-white shadow-sm flex-grow-1 flex-md-grow-0"
                                    style="min-width: 200px; max-width: 100%;">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-0 py-2"
                                        placeholder="Search invoice, customer..."
                                        wire:model.live="search">
                                </div>

                                <!-- Filter dropdown -->
                                <div class="dropdown w-100 w-sm-auto">
                                    <button class="btn btn-outline-primary dropdown-toggle shadow-sm rounded-pill w-100"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-funnel me-1"></i>
                                        Filter
                                        @if($statusFilter || $dateFilter)
                                        <span class="badge bg-primary ms-1">{{ ($statusFilter ? 1 : 0) + ($dateFilter ? 1 : 0) }}</span>
                                        @endif
                                    </button>
                                    <div class="dropdown-menu p-3 shadow-lg dropdown-menu-end" style="width: 300px;">
                                        <!-- Status Filter -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark">Status</label>
                                            <select class="form-select" wire:model.live="statusFilter">
                                                <option value="">All Status</option>
                                                <option value="pending">Pending</option>
                                                <option value="confirm">Confirmed</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>

                                        <!-- Date Filter -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark">Date Range</label>
                                            <select class="form-select" wire:model.live="dateFilter">
                                                <option value="">All Dates</option>
                                                <option value="today">Today</option>
                                                <option value="yesterday">Yesterday</option>
                                                <option value="this_week">This Week</option>
                                                <option value="this_month">This Month</option>
                                            </select>
                                        </div>

                                        <!-- Clear Filters -->
                                        @if($statusFilter || $dateFilter)
                                        <button class="btn btn-sm btn-outline-secondary w-100"
                                            wire:click="$set('statusFilter', ''); $set('dateFilter', '')">
                                            <i class="bi bi-x-circle me-1"></i>Clear Filters
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Invoice</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Staff</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Date</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $sale->invoice_number }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">
                                                    {{ $sale->customer->name ?? 'Walk-in Customer' }}
                                                </h6>
                                                @if($sale->customer && $sale->customer->phone)
                                                <small class="text-muted">{{ $sale->customer->phone }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">{{ $sale->user->name ?? 'Unknown' }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">Rs.{{ number_format($sale->total_amount, 2) }}</p>
                                        </td>
                                        <td>
                                            @if($sale->status === 'pending')
                                            <span class="badge badge-sm bg-warning">Pending</span>
                                            @elseif($sale->status === 'confirm')
                                            <span class="badge badge-sm bg-success">Confirmed</span>
                                            @elseif($sale->status === 'rejected')
                                            <span class="badge badge-sm bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-secondary text-xs font-weight-bold">
                                                {{ $sale->created_at->format('M d, Y') }}
                                            </span>
                                            <br>
                                            <span class="text-secondary text-xs">
                                                {{ $sale->created_at->format('h:i A') }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-outline-info btn-sm"
                                                    wire:click="viewSale({{ $sale->id }})"
                                                    title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if($sale->status === 'pending')
                                                <button class="btn btn-outline-warning btn-sm"
                                                    wire:click="editSale({{ $sale->id }})"
                                                    title="Edit Sale">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-success btn-sm"
                                                    wire:click="viewSale({{ $sale->id }})"
                                                    title="Approve/Reject">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                                                <h6 class="text-muted">No sales found</h6>
                                                <p class="text-muted mb-0">Try adjusting your search or filters.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="px-4 pt-4">
                            {{ $sales->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Approval Modal -->
    <div wire:ignore.self class="modal fade" id="sale-approval-modal" tabindex="-1" role="dialog"
        aria-labelledby="sale-approval-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="sale-approval-modal-label">
                        <i class="bi bi-shield-check me-2"></i> Sale Review
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if ($selectedSale)
                    <div class="row g-0">
                        <!-- Left column - Details -->
                        <div class="col-md-8 p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-gradient-info text-white py-3">
                                            <h6 class="mb-0 fw-bold text-white">Sale Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 text-xs">Invoice Number</p>
                                                    <p class="fw-bold mb-3">{{ $selectedSale->invoice_number }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 text-xs">Sale Date</p>
                                                    <p class="fw-bold mb-3">{{ $selectedSale->created_at->format('M d, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 text-xs">Status</p>
                                                    @if($selectedSale->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @elseif($selectedSale->status === 'confirm')
                                                    <span class="badge bg-success">Confirmed</span>
                                                    @elseif($selectedSale->status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 text-xs">Staff Member</p>
                                                    <p class="fw-bold mb-3">{{ $selectedSale->user->name ?? 'Unknown' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-gradient-success text-white py-3">
                                            <h6 class="mb-0 fw-bold text-white">Customer Details</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($selectedSale->customer)
                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="text-muted mb-1 text-xs">Customer Name</p>
                                                    <p class="fw-bold mb-3">{{ $selectedSale->customer->name }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 text-xs">Phone</p>
                                                    <p class="fw-bold mb-3">{{ $selectedSale->customer->phone ?? 'N/A' }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-1 text-xs">Type</p>
                                                    <span class="badge bg-info">{{ ucfirst($selectedSale->customer->type) }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <p class="text-muted">Walk-in Customer</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Details -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-gradient-dark text-white py-3">
                                    <h6 class="mb-0 fw-bold text-white">Payment Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p class="text-muted mb-1 text-xs">Subtotal</p>
                                            <p class="fw-bold h6 text-primary">Rs.{{ number_format($selectedSale->subtotal, 2) }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="text-muted mb-1 text-xs">Discount</p>
                                            <p class="fw-bold h6 text-warning">Rs.{{ number_format($selectedSale->discount_amount, 2) }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="text-muted mb-1 text-xs">Total Amount</p>
                                            <p class="fw-bold h5 text-success">Rs.{{ number_format($selectedSale->total_amount, 2) }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="text-muted mb-1 text-xs">Payment Status</p>
                                            <span class="badge bg-{{ $selectedSale->payment_status === 'paid' ? 'success' : ($selectedSale->payment_status === 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($selectedSale->payment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action buttons for pending sales -->
                            @if ($selectedSale->status === 'pending')
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-3">
                                    <h6 class="mb-0 fw-bold">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button class="btn btn-success w-100 mb-2" wire:click="approveSale">
                                                <i class="bi bi-check-circle me-2"></i>Approve Sale
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-danger w-100 mb-2"
                                                onclick="$('#rejectionSection').toggleClass('d-none')">
                                                <i class="bi bi-x-circle me-2"></i>Reject Sale
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Rejection Reason Section -->
                                    <div id="rejectionSection" class="d-none mt-3">
                                        <div class="mb-3">
                                            <label class="form-label">Rejection Reason</label>
                                            <textarea class="form-control" rows="3"
                                                wire:model="rejectionReason"
                                                placeholder="Please provide a reason for rejecting this sale..."></textarea>
                                            @error('rejectionReason')
                                            <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button class="btn btn-danger" wire:click="rejectSale">
                                            <i class="bi bi-x-circle me-2"></i>Confirm Rejection
                                        </button>
                                        <button class="btn btn-secondary ms-2"
                                            onclick="$('#rejectionSection').addClass('d-none')">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                This sale has already been
                                <strong>{{ $selectedSale->status === 'confirm' ? 'approved' : 'rejected' }}</strong>.
                            </div>
                            @endif
                        </div>

                        <!-- Right column - Items -->
                        <div class="col-md-4 bg-light p-4 border-start">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-dark text-white py-3">
                                    <h6 class="mb-0 fw-bold text-white">Sale Items</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-xs text-uppercase text-muted fw-normal ps-3">Item</th>
                                                    <th class="text-xs text-uppercase text-muted fw-normal">Qty</th>
                                                    <th class="text-xs text-uppercase text-muted fw-normal text-end pe-3">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($selectedSale->items as $item)
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="d-flex flex-column">
                                                            <span class="text-sm fw-bold">{{ $item->product_name }}</span>
                                                            <small class="text-muted">{{ $item->product_code }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-sm">{{ $item->quantity }}</td>
                                                    <td class="text-sm text-end pe-3">Rs.{{ number_format($item->unit_price, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold text-sm">Total:</td>
                                                    <td class="text-end pe-3 fw-bold text-primary text-sm">
                                                        Rs.{{ number_format($selectedSale->total_amount, 2) }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if($selectedSale->notes)
                            <div class="card border-0 shadow-sm mt-4">
                                <div class="card-header bg-gradient-info text-white py-3">
                                    <h6 class="mb-0 fw-bold text-white">Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-sm mb-0">{{ $selectedSale->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading sale details...</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Edit Modal -->
    <div wire:ignore.self class="modal fade" id="sale-edit-modal" tabindex="-1" role="dialog"
        aria-labelledby="sale-edit-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-warning text-white">
                    <h5 class="modal-title" id="sale-edit-modal-label">
                        <i class="bi bi-pencil-square me-2"></i> Edit Sale
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if ($editingSale)
                    <!-- Sale Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-info text-white py-3">
                                    <h6 class="mb-0 fw-bold text-white">Sale Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="text-muted mb-1 text-xs">Invoice Number</p>
                                            <p class="fw-bold mb-3">{{ $editingSale->invoice_number }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted mb-1 text-xs">Sale Date</p>
                                            <p class="fw-bold mb-3">{{ $editingSale->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-success text-white py-3">
                                    <h6 class="mb-0 fw-bold text-white">Customer Details</h6>
                                </div>
                                <div class="card-body">
                                    @if($editingSale->customer)
                                    <p class="text-muted mb-1 text-xs">Customer Name</p>
                                    <p class="fw-bold mb-3">{{ $editingSale->customer->name }}</p>
                                    @if($editingSale->customer->phone)
                                    <p class="text-muted mb-1 text-xs">Phone</p>
                                    <p class="fw-bold mb-0">{{ $editingSale->customer->phone }}</p>
                                    @endif
                                    @else
                                    <p class="text-muted">Walk-in Customer</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Items Edit -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-dark text-white py-3">
                            <h6 class="mb-0 fw-bold text-white">Sale Items</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-xs text-uppercase text-muted fw-normal ps-3">Product</th>
                                            <th class="text-xs text-uppercase text-muted fw-normal">Quantity</th>
                                            <th class="text-xs text-uppercase text-muted fw-normal">Unit Price</th>
                                            <th class="text-xs text-uppercase text-muted fw-normal">Discount</th>
                                            <th class="text-xs text-uppercase text-muted fw-normal">Total</th>
                                            <th class="text-xs text-uppercase text-muted fw-normal text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($editItems as $itemId => $item)
                                        <tr wire:key="edit-item-{{ $itemId }}">
                                            <td class="ps-3">
                                                <div class="d-flex flex-column">
                                                    <span class="text-sm fw-bold">{{ $item->product_name }}</span>
                                                    <small class="text-muted">{{ $item->product_code }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    style="width: 80px;"
                                                    wire:model.live="editQuantities.{{ $itemId }}"
                                                    wire:change="updateEditItem({{ $itemId }})"
                                                    min="1">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    style="width: 100px;"
                                                    wire:model.live="editPrices.{{ $itemId }}"
                                                    wire:change="updateEditItem({{ $itemId }})"
                                                    step="0.01" min="0">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    style="width: 100px;"
                                                    wire:model.live="editDiscounts.{{ $itemId }}"
                                                    wire:change="updateEditItem({{ $itemId }})"
                                                    step="0.01" min="0">
                                            </td>
                                            <td>
                                                <span class="text-sm fw-bold">
                                                    Rs.{{ number_format(($editPrices[$itemId] ?? 0) * ($editQuantities[$itemId] ?? 0) - ($editDiscounts[$itemId] ?? 0) * ($editQuantities[$itemId] ?? 0), 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="removeEditItem({{ $itemId }})"
                                                    title="Remove Item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Totals Summary -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-primary text-white py-3">
                            <h6 class="mb-0 fw-bold text-white">Sale Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="text-muted mb-1 text-xs">Subtotal</p>
                                    <p class="fw-bold h6 text-primary">Rs.{{ number_format($editSubtotal, 2) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted mb-1 text-xs">Total Discount</p>
                                    <p class="fw-bold h6 text-warning">Rs.{{ number_format($editTotalDiscount, 2) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted mb-1 text-xs">Grand Total</p>
                                    <p class="fw-bold h5 text-success">Rs.{{ number_format($editGrandTotal, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="saveSaleEdit">
                            <i class="bi bi-check-circle me-2"></i>Save Changes
                        </button>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading sale details...</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-opacity-25 {
            opacity: 0.25;
        }

        .bg-primary-subtle {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        .bg-success-subtle {
            background-color: rgba(var(--bs-success-rgb), 0.1);
        }

        .bg-danger-subtle {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
        }

        .bg-info-subtle {
            background-color: rgba(var(--bs-info-rgb), 0.1);
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #4e73df, #224abe);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #1cc88a, #13855c);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74a3b, #a62c1f);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #36b9cc, #258391);
        }

        .bg-gradient-dark {
            background: linear-gradient(45deg, #5a5c69, #373840);
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #f6c23e, #e0a800);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const handleModal = (modalId, action) => {
                const modalElement = document.getElementById(modalId);

                const existingModal = bootstrap.Modal.getInstance(modalElement);
                if (existingModal) {
                    existingModal.dispose();
                }

                if (action === 'show') {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();

                    modalElement.addEventListener('hidden.bs.modal', function() {
                        const modalToDispose = bootstrap.Modal.getInstance(modalElement);
                        if (modalToDispose) {
                            modalToDispose.dispose();
                        }
                    }, {
                        once: true
                    });
                }
            };

            @this.on('openModal', (modalId) => {
                handleModal(modalId, 'show');
            });

            @this.on('closeModal', (modalId) => {
                const modalElement = document.getElementById(modalId);
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            });

            @this.on('showToast', ({
                type,
                message
            }) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            });
        });
    </script>
    @endpush
</div>