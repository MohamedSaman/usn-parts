<div>
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Outstanding Accounts Summary</h5>
        <p class="text-muted mb-0 small">
            <i class="bi bi-calendar-event me-1"></i>As of {{ now()->format('F d, Y') }}
        </p>
    </div>

    <!-- Customer Outstanding Section -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">
                <i class="bi bi-people-fill text-primary me-2"></i>Customer Outstanding
            </h6>
            <span class="badge bg-primary fs-6">
                {{ $reportData['customers']->count() }} Customers
            </span>
        </div>

        @if($reportData['customers']->isEmpty())
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>No outstanding customer payments
        </div>
        @else
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1 small">Total Outstanding</h6>
                        <h3 class="mb-0 fw-bold text-danger">
                            Rs.{{ number_format($reportData['customers']->sum('total_due'), 2) }}
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted mb-1 small">Total Invoices</h6>
                        <h3 class="mb-0 fw-bold">
                            {{ $reportData['customers']->sum('invoices') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Outstanding Invoices</th>
                        <th>Total Due Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['customers'] as $customer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $customer['customer']->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $customer['customer']->business_name ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="bi bi-telephone me-1"></i>{{ $customer['customer']->phone ?? 'N/A' }}
                            </div>
                            @if($customer['customer']->email)
                            <small class="text-muted">
                                <i class="bi bi-envelope me-1"></i>{{ $customer['customer']->email }}
                            </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-warning">{{ $customer['invoices'] }} invoices</span>
                        </td>
                        <td class="fw-bold text-danger fs-5">
                            Rs.{{ number_format($customer['total_due'], 2) }}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View Details
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Total Customer Outstanding:</td>
                        <td class="text-danger">Rs.{{ number_format($reportData['customers']->sum('total_due'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    <!-- Supplier Outstanding Section -->
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">
                <i class="bi bi-truck text-info me-2"></i>Supplier Outstanding
            </h6>
            <span class="badge bg-info fs-6">
                {{ $reportData['suppliers']->count() }} Suppliers
            </span>
        </div>

        @if($reportData['suppliers']->isEmpty())
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>No outstanding supplier payments
        </div>
        @else
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1 small">Total Outstanding</h6>
                        <h3 class="mb-0 fw-bold text-warning">
                            Rs.{{ number_format($reportData['suppliers']->sum('total_due'), 2) }}
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted mb-1 small">Total Orders</h6>
                        <h3 class="mb-0 fw-bold">
                            {{ $reportData['suppliers']->sum('orders') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Supplier</th>
                        <th>Contact</th>
                        <th>Pending Orders</th>
                        <th>Total Due Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['suppliers'] as $supplier)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $supplier['supplier']->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $supplier['supplier']->businessname ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="bi bi-telephone me-1"></i>{{ $supplier['supplier']->phone ?? 'N/A' }}
                            </div>
                            @if($supplier['supplier']->email)
                            <small class="text-muted">
                                <i class="bi bi-envelope me-1"></i>{{ $supplier['supplier']->email }}
                            </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-warning">{{ $supplier['orders'] }} orders</span>
                        </td>
                        <td class="fw-bold text-warning fs-5">
                            Rs.{{ number_format($supplier['total_due'], 2) }}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye me-1"></i>View Details
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Total Supplier Outstanding:</td>
                        <td class="text-warning">Rs.{{ number_format($reportData['suppliers']->sum('total_due'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    <!-- Overall Summary -->
    <div class="row g-3 mt-4">
        <div class="col-md-6">
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Receivables (Customers)</h6>
                    <h2 class="mb-0 text-danger">Rs.{{ number_format($reportData['customers']->sum('total_due'), 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Payables (Suppliers)</h6>
                    <h2 class="mb-0 text-warning">Rs.{{ number_format($reportData['suppliers']->sum('total_due'), 2) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>