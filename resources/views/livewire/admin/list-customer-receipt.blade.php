<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-receipt text-success me-2"></i> Customer Payment List
            </h3>
            <p class="text-muted mb-0">View all customer receipts and payment allocations</p>
        </div>
    </div>

    {{-- Customer List Table --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-people me-2"></i> Customers with Payments
            </h5>
            <span class="badge bg-primary">{{ $customers->total() }} customers</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Customer Name</th>
                            <th class="text-center">Total Paid</th>
                            <th class="text-center">No. of Receipts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr wire:key="customer-{{ $customer->id }}" style="cursor:pointer" wire:click="showCustomerPayments({{ $customer->id }})">
                            <td class="ps-4 fw-semibold">{{ $customer->name }}</td>
                            <td class="text-center">Rs.{{ number_format($customer->total_paid, 2) }}</td>
                            <td class="text-center">{{ $customer->receipts_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-x-circle display-4 d-block mb-2"></i>
                                No customer payments found.
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

    {{-- Payment Details Modal --}}
    @if($showPaymentModal && $selectedCustomer)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt me-2"></i> Payment Details - {{ $selectedCustomer->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closePaymentModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <span class="fw-bold">Mobile:</span> {{ $selectedCustomer->mobile }}<br>
                        <span class="fw-bold">Email:</span> {{ $selectedCustomer->email }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Allocated Invoices</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->payment_date ? date('M d, Y', strtotime($payment->payment_date)) : '-' }}</td>
                                    <td>Rs.{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>
                                        @if($payment->allocations && count($payment->allocations) > 0)
                                        <ul class="mb-0 ps-3">
                                            @foreach($payment->allocations as $alloc)
                                            <li>Invoice #{{ $alloc->sale_id }} - Rs.{{ number_format($alloc->amount, 2) }}</li>
                                            @endforeach
                                        </ul>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No payments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closePaymentModal">Close</button>
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

        .table th {
            font-weight: 600;
        }

        .badge {
            font-size: 0.75em;
        }

        .modal.show {
            display: block !important;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
        }

        .input-group-lg .form-control {
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
</div>