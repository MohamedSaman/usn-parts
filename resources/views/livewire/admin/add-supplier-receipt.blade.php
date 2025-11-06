<div class="container-fluid py-4">
    {{-- Toast Alert --}}
    <div
        x-data="{ show: false, type: '', message: '' }"
        x-init="
            window.addEventListener('show-toast', e => {
                console.log('Toast event received:', e.detail);
                const data = Array.isArray(e.detail) ? e.detail[0] : e.detail;
                type = data.type || 'info';
                message = data.message || 'Notification';
                show = true;
                setTimeout(() => show = false, 4000);
            });
        "
        style="position: fixed; top: 24px; right: 24px; z-index: 9999; min-width: 350px;">
        <template x-if="show">
            <div :class="type === 'success' ? 'alert alert-success shadow-lg' : 'alert alert-danger shadow-lg'" class="fade show border-0" role="alert">
                <div class="d-flex align-items-center">
                    <i :class="type === 'success' ? 'bi bi-check-circle-fill me-2' : 'bi bi-exclamation-triangle-fill me-2'" style="font-size: 1.5rem;"></i>
                    <div class="flex-grow-1" x-text="message"></div>
                    <button type="button" class="btn-close ms-2" @click="show = false" aria-label="Close"></button>
                </div>
            </div>
        </template>
    </div>

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-receipt text-success me-2"></i> Supplier Payment Receipt
            </h3>
            <p class="text-muted mb-0">Record supplier payments and allocate to due purchase orders</p>
        </div>
    </div>

    {{-- Supplier Search --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-search me-2"></i> Find Supplier with Due Payments
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Search Supplier</label>
                    <div class="input-group">
                        <input type="text" class="form-control"  wire:model.live="search" placeholder="Type supplier name or mobile...">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Selected Supplier</label>
                    @if($selectedSupplier)
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ $selectedSupplier->name }}</div>
                                <div class="text-muted small">{{ $selectedSupplier->mobile }}</div>
                                <div class="text-muted small">{{ $selectedSupplier->email }}</div>
                            </div>
                            <button class="btn btn-outline-danger btn-sm" wire:click="clearSelectedSupplier">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="border rounded p-3 text-center text-muted">
                        <i class="bi bi-person-x display-6 d-block mb-2"></i>
                        No supplier selected
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Supplier List --}}
    @if(!$selectedSupplier)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-people me-2"></i> Suppliers with Due Payments
            </h5>
            <span class="badge bg-primary">{{ $suppliers->total() }} suppliers</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Supplier Name</th>
                            <th class="text-center">Total Due</th>
                            <th class="text-center">Due Orders</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        @php
                        $dueOrders = $supplier->orders->count();
                        $totalDue = $supplier->orders->sum('due_amount');
                        @endphp
                        <tr wire:key="supplier-{{ $supplier->id }}">
                            <td class="ps-4 fw-semibold">{{ $supplier->name }}</td>
                            <td class="text-center text-danger fw-bold">
                                Rs.{{ number_format($totalDue, 2) }}
                            </td>
                            <td class="text-center"><span class="badge bg-warning">{{ $dueOrders }}</span></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-primary btn-sm" wire:click="selectSupplier({{ $supplier->id }})">
                                    <i class="bi bi-credit-card me-1"></i> Pay
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-search display-4 d-block mb-2"></i>
                                @if($search)
                                No suppliers found for "<span class="fw-bold">{{ $search }}</span>".
                                @else
                                No suppliers with due payments found.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($suppliers->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $suppliers->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Due Orders and Payment Allocation --}}
    @if($selectedSupplier && count($supplierOrders) > 0)
    <div class="row">
        {{-- Due Orders List --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-receipt me-2"></i> Due Orders - {{ $selectedSupplier->name }}
                    </h5>
                    <div>
                        @if(count($selectedOrders) > 0)
                            <button class="btn btn-light btn-sm me-2" wire:click="clearOrderSelection">
                                <i class="bi bi-x-circle me-1"></i> Clear Selection
                            </button>
                        @endif
                        <button class="btn btn-light btn-sm" wire:click="selectAllOrders">
                            <i class="bi bi-check-all me-1"></i> Select All
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="50">
                                        <i class="bi bi-check-square"></i>
                                    </th>
                                    <th>Order Code</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Due Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplierOrders as $order)
                                @php
                                    $isSelected = in_array($order->id, $selectedOrders);
                                @endphp
                                <tr 
                                    wire:key="order-{{ $order->id }}" 
                                    class="{{ $isSelected ? 'table-success' : '' }}"
                                    style="cursor: pointer;"
                                    wire:click="toggleOrderSelection({{ $order->id }})">
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox" 
                                                {{ $isSelected ? 'checked' : '' }}
                                                style="pointer-events: none;">
                                        </div>
                                    </td>
                                    <td class="fw-bold">{{ $order->order_code }}</td>
                                    <td>{{ $order->order_date ? date('M d, Y', strtotime($order->order_date)) : '-' }}</td>
                                    <td>Rs.{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="fw-bold {{ $isSelected ? 'text-success' : 'text-danger' }}">
                                        Rs.{{ number_format($order->due_amount, 2) }}
                                    </td>
                                    <td>
                                        @if($order->due_amount == 0)
                                            <span class="badge bg-success">Complete</span>
                                        @elseif($order->due_amount > 0 && $order->due_amount < $order->total_amount)
                                            <span class="badge bg-warning text-dark">Partial Paid</span>
                                        @else
                                            <span class="badge bg-danger">Payment Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                wire:click.stop="viewOrderDetails({{ $order->id }})"
                                                title="View Order Details">
                                            <i class="bi bi-eye "></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($selectedOrders) > 0)
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    {{ count($selectedOrders) }} order(s) selected
                                </span>
                                <span class="fw-bold text-success">
                                    Total Due: Rs.{{ number_format($totalDueAmount, 2) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="card-footer bg-light text-center text-muted">
                            <i class="bi bi-hand-index me-2"></i>
                            Click on rows to select orders for payment
                        </div>
                    @endif
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
                    @if(count($selectedOrders) > 0)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total Due (Selected)</span>
                                <span class="fw-bold text-danger">Rs.{{ number_format($totalDueAmount, 2) }}</span>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ count($selectedOrders) }} order(s) selected for payment
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Enter Payment Amount <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" 
                                       wire:model.live="totalPaymentAmount" 
                                       min="0.01" 
                                       max="{{ $totalDueAmount }}" 
                                       step="0.01" 
                                       placeholder="0.00">
                            </div>
                            <small class="text-muted">Maximum: Rs.{{ number_format($totalDueAmount, 2) }}</small>
                            @error('totalPaymentAmount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($totalPaymentAmount > 0)
                        <div class="alert alert-{{ $remainingAmount > 0 ? 'warning' : 'success' }} mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Remaining Amount</span>
                                <span class="fw-bold">Rs.{{ number_format($remainingAmount, 2) }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="d-grid mt-3">
                            <button class="btn btn-success btn-lg" 
                                    wire:click="openPaymentModal" 
                                    wire:loading.attr="disabled" 
                                    wire:loading.class="disabled"
                                    @if($totalPaymentAmount <= 0 || $totalPaymentAmount > $totalDueAmount) disabled @endif>
                                <span wire:loading.remove wire:target="openPaymentModal">
                                    <i class="bi bi-credit-card me-1"></i> Process Payment
                                    @if($totalPaymentAmount > 0)
                                    <span class="ms-1">(Rs.{{ number_format($totalPaymentAmount, 2) }})</span>
                                    @endif
                                </span>
                                <span wire:loading wire:target="openPaymentModal">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Processing...
                                </span>
                            </button>
                        </div>

                        {{-- Quick Payment Options --}}
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2">Quick Options:</small>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" 
                                        wire:click="$set('totalPaymentAmount', {{ $totalDueAmount }})">
                                    Pay Full Amount (Rs.{{ number_format($totalDueAmount, 2) }})
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle-fill display-4 d-block mb-3"></i>
                            <h6 class="fw-bold mb-2">No Orders Selected</h6>
                            <p class="mb-0 small">Please select at least one order from the list to make a payment.</p>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="bi bi-lock me-1"></i> Select Orders First
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @elseif($selectedSupplier && count($supplierOrders) == 0)
    <div class="alert alert-success">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-2 me-3"></i>
            <div>
                <h5 class="mb-1">No Pending Payments</h5>
                <p class="mb-0">{{ $selectedSupplier->name }} has no pending purchase orders. All orders are fully paid.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- View Order Details Modal --}}
    @if($showViewModal && $selectedOrder)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-eye me-2"></i> Order Details - {{ $selectedOrder->order_code }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">ORDER INFORMATION</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Order Code:</strong></td>
                                    <td>{{ $selectedOrder->order_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Order Date:</strong></td>
                                    <td>{{ $selectedOrder->order_date ? \Carbon\Carbon::parse($selectedOrder->order_date)->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="fw-bold">Rs.{{ number_format($selectedOrder->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount:</strong></td>
                                    <td class="text-success fw-bold">Rs.{{ number_format($selectedOrder->total_amount - $selectedOrder->due_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Due Amount:</strong></td>
                                    <td class="fw-bold text-danger">Rs.{{ number_format($selectedOrder->due_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status:</strong></td>
                                    <td>
                                        @if($selectedOrder->due_amount == 0)
                                            <span class="badge bg-success">Fully Paid</span>
                                        @elseif($selectedOrder->due_amount > 0 && $selectedOrder->due_amount < $selectedOrder->total_amount)
                                            <span class="badge bg-warning text-dark">Partial Paid</span>
                                        @else
                                            <span class="badge bg-danger">Payment Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">SUPPLIER INFORMATION</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Name:</strong></td>
                                    <td>{{ $selectedOrder->supplier->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>{{ $selectedOrder->supplier->mobile ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $selectedOrder->supplier->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $selectedOrder->supplier->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <h6 class="text-muted mb-3">ORDER ITEMS</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($selectedOrder->items && count($selectedOrder->items) > 0)
                                    @foreach($selectedOrder->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $item->product->name ?? 'N/A' }}</div>
                                            @if($item->product->code ?? false)
                                            <small class="text-muted">Code: {{ $item->product->code }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($item->quantity) }}</td>
                                        <td class="text-end">Rs.{{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end fw-bold">Rs.{{ number_format($item->total_cost, 2) }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            No items found for this order
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold">Rs.{{ number_format($selectedOrder->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Paid Amount:</td>
                                    <td class="text-end text-success fw-bold">Rs.{{ number_format($selectedOrder->total_amount - $selectedOrder->due_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Due Amount:</td>
                                    <td class="text-end text-danger fw-bold">Rs.{{ number_format($selectedOrder->due_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Payment History --}}
                    <h6 class="text-muted mb-3">PAYMENT HISTORY</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Payment Date</th>
                                    <th>Method</th>
                                    <th class="text-end">Amount</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($paymentHistory && count($paymentHistory) > 0)
                                    @foreach($paymentHistory as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                        <td>
                                            <span class="text-capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                                            @if($payment->payment_method === 'cheque' && $payment->cheque_number)
                                            <br><small class="text-muted">Cheque: {{ $payment->cheque_number }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end text-success fw-bold">Rs.{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->payment_reference ?: 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="bi bi-credit-card display-4 d-block mb-2"></i>
                                            No payment history found
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeViewModal">
                        <i class="bi bi-x-circle me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal && $selectedSupplier)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-credit-card me-2"></i> Confirm Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closePaymentModal"></button>
                </div>
                <div class="modal-body">
                    {{-- Supplier Info --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">SUPPLIER INFORMATION</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Name:</strong></td>
                                    <td>{{ $selectedSupplier?->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>{{ $selectedSupplier?->mobile }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $selectedSupplier?->email }}</td>
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
                            <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('paymentData.payment_date') is-invalid @enderror" wire:model="paymentData.payment_date">
                            @error('paymentData.payment_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('paymentData.payment_method') is-invalid @enderror" wire:model.live="paymentData.payment_method">
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                            @error('paymentData.payment_method') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Cheque Details --}}
                        @if($paymentData['payment_method'] === 'cheque')
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="fw-semibold mb-3 text-success">
                                    <i class="bi bi-receipt me-2"></i>Cheque Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Cheque Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('cheque.cheque_number') is-invalid @enderror"
                                            wire:model="cheque.cheque_number"
                                            placeholder="Enter cheque number">
                                        @error('cheque.cheque_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('cheque.bank_name') is-invalid @enderror"
                                            wire:model="cheque.bank_name"
                                            placeholder="Enter bank name">
                                        @error('cheque.bank_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Cheque Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('cheque.cheque_date') is-invalid @enderror"
                                            wire:model="cheque.cheque_date">
                                        @error('cheque.cheque_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Bank Transfer Details --}}
                        @if($paymentData['payment_method'] === 'bank_transfer')
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="fw-semibold mb-3 text-primary">
                                    <i class="bi bi-bank me-2"></i>Bank Transfer Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('bankTransfer.bank_name') is-invalid @enderror"
                                            wire:model="bankTransfer.bank_name"
                                            placeholder="Enter bank name">
                                        @error('bankTransfer.bank_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Transaction Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('bankTransfer.transfer_date') is-invalid @enderror"
                                            wire:model="bankTransfer.transfer_date">
                                        @error('bankTransfer.transfer_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Reference Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('bankTransfer.reference_number') is-invalid @enderror"
                                            wire:model="bankTransfer.reference_number"
                                            placeholder="Transaction reference">
                                        @error('bankTransfer.reference_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Optional Reference (Always visible) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Reference (Optional)</label>
                            <input type="text" class="form-control @error('paymentData.reference_number') is-invalid @enderror"
                                wire:model="paymentData.reference_number"
                                placeholder="Enter payment reference if any">
                            @error('paymentData.reference_number') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Notes (Always visible) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes (Optional)</label>
                            <textarea class="form-control @error('paymentData.notes') is-invalid @enderror"
                                rows="1"
                                wire:model="paymentData.notes"
                                placeholder="Additional notes"></textarea>
                            @error('paymentData.notes') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Allocation Breakdown --}}
                    <h6 class="text-muted mt-4 mb-3">PAYMENT ALLOCATION BREAKDOWN</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Code</th>
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
                                    <td class="fw-bold">{{ $allocation['order_code'] }}</td>
                                    <td class="text-end">Rs.{{ number_format($allocation['due_amount'], 2) }}</td>
                                    <td class="text-end text-success fw-bold">Rs.{{ number_format($allocation['payment_amount'], 2) }}</td>
                                    <td class="text-end text-danger">Rs.{{ number_format($allocation['due_amount'] - $allocation['payment_amount'], 2) }}</td>
                                    <td>
                                        @php
                                            $remaining = $allocation['due_amount'] - $allocation['payment_amount'];
                                        @endphp
                                        @if($remaining == 0)
                                            <span class="badge bg-success">Complete</span>
                                        @elseif($allocation['payment_amount'] > 0 && $remaining > 0)
                                            <span class="badge bg-warning text-dark">Partial Paid</span>
                                        @else
                                            <span class="badge bg-danger">Payment Pending</span>
                                        @endif
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
                    <button
                        type="button"
                        class="btn btn-success"
                        wire:click="processPayment"
                        wire:loading.attr="disabled"
                        wire:loading.class="disabled">
                        <span wire:loading.remove wire:target="processPayment">
                            Confirm & Save Payment
                        </span>
                        <span wire:loading wire:target="processPayment">
                            <span class="spinner-border spinner-border-sm me-1"></span> Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
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

    /* Order selection styles */
    .table tbody tr[style*="cursor: pointer"]:hover {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .table tbody tr.table-success {
        background-color: rgba(25, 135, 84, 0.15) !important;
    }

    .table tbody tr.table-success:hover {
        background-color: rgba(25, 135, 84, 0.25) !important;
    }

    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        cursor: pointer;
    }
</style>
@endpush