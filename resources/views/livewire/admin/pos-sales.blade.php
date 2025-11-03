@php
use App\Models\Sale;
@endphp

<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-cash-stack text-success me-2"></i> POS Sales Management
            </h3>
            <p class="text-muted mb-0">View and manage POS sales</p>
        </div>
        <div>
            <a href="{{ route('admin.store-billing') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> New POS Sale
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total POS Sales
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['total_sales'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rs.{{ number_format($stats['total_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Pending Payments
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rs.{{ number_format($stats['pending_payments'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Today's Sales
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['today_sales'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Search</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search by invoice, customer name or phone..."
                            wire:model.live="search">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Payment Status</label>
                    <select class="form-select" wire:model.live="paymentStatusFilter">
                        <option value="all">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" class="form-control" wire:model.live="dateFilter">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold invisible">Actions</label>
                    <button class="btn btn-outline-secondary w-100" wire:click="$set('dateFilter', '')">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i> POS Sales List
            </h5>
            <span class="badge bg-primary">{{ $sales->total() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Customer</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">Sale Type</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr wire:key="sale-{{ $sale->id }}" style="cursor:pointer" wire:click="viewSale({{ $sale->id }})">
                            <td class="ps-4">
                                <div class="fw-bold text-primary">{{ $sale->invoice_number }}</div>
                                <small class="text-muted">#{{ $sale->sale_id }}</small>
                            </td>
                            <td>
                                @if($sale->customer)
                                <div class="fw-medium">{{ $sale->customer->name }}</div>
                                <small class="text-muted">{{ $sale->customer->phone }}</small>
                                @else
                                <span class="text-muted">Walk-in Customer</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div>{{ $sale->created_at->format('M d, Y') }}</div>
                            </td>

                            <td class="text-center">
                                <div class="fw-bold">Rs.{{ number_format($sale->total_amount, 2) }}</div>
                                @if($sale->due_amount > 0)
                                <small class="text-danger">Due: Rs.{{ number_format($sale->due_amount, 2) }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ strtoupper($sale->sale_type) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <button class=" text-success me-2 bg-opacity-0 border-0" wire:click.stop="downloadInvoice({{ $sale->id }})"
                                        wire:target="downloadInvoice({{ $sale->id }})"
                                        title="Download Invoice">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button class="text-danger me-2 bg-opacity-0 border-0" wire:click.stop="deleteSale({{ $sale->id }})"
                                        title="Delete Sale">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-cart-x display-4 d-block mb-2"></i>
                                No POS sales found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($sales->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $sales->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- View Sale Modal --}}
    <div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="printableInvoice">
                {{-- ==================== HEADER ==================== --}}
                <div class="modal-header text-center border-0" style="background: linear-gradient(90deg, #3b5b0c, #8eb922); color: #fff;">
                    <div class="w-100">
                        <img src="{{ asset('images/USN.png') }}" alt="Logo"
                             class="img-fluid mb-2" style="max-height:60px;">
                        <h4 class="mb-0 fw-bold">USN AUTO PARTS</h4>
                        
                    </div>
                    <button type="button" class="btn-close btn-close-white closebtn"
                            wire:click="closeModals"></button>
                </div>
                @if($selectedSale)
                <div class="modal-body">
                    {{-- ==================== CUSTOMER + INVOICE INFO ==================== --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Customer :</strong><br>
                            {{ $selectedSale->customer->name ?? 'Walk-in Customer' }}<br>
                            {{ $selectedSale->customer->address ?? '' }}<br>
                            Tel: {{ $selectedSale->customer->phone ?? '' }}
                        </div>
                        <div class="col-6 text-end">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Invoice #</strong></td>
                                    <td>{{ $selectedSale->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sale ID</strong></td>
                                    <td>{{ $selectedSale->sale_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date</strong></td>
                                    <td>{{ $selectedSale->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sale Type</strong></td>
                                    <td><span class="badge bg-primary">{{ strtoupper($selectedSale->sale_type) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Created By</strong></td>
                                    <td>{{ $selectedSale->user->name ?? 'System' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- ==================== ITEMS TABLE ==================== --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedSale->items as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td class="text-center">{{ $item->product_code }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->discount_per_unit * $item->quantity, 2) }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                                @if($selectedSale->items->count() == 0)
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No items found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- ==================== TOTALS (right-aligned) ==================== --}}
                    <div class="row">
                        <div class="col-7"></div>
                        <div class="col-5">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td class="text-end">Rs.{{ number_format($selectedSale->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Discount</strong></td>
                                    <td class="text-end">- Rs.{{ number_format($selectedSale->discount_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Grand Total</strong></td>
                                    <td class="text-end fw-bold">Rs.{{ number_format($selectedSale->total_amount, 2) }}</td>
                                </tr>
                                @if($selectedSale->due_amount > 0)
                                <tr>
                                    <td><strong class="text-danger">Due Amount</strong></td>
                                    <td class="text-end fw-bold text-danger">Rs.{{ number_format($selectedSale->due_amount, 2) }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>


                    {{-- ==================== RETURNED ITEMS TABLE ==================== --}}
                    <h6 class="text-muted mb-3 mt-4">RETURNED ITEMS</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Return Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $returnAmount = 0; @endphp
                                @if(isset($selectedSale->returns) && count($selectedSale->returns) > 0)
                                @foreach($selectedSale->returns as $rIndex => $return)
                                @php $returnAmount += $return->total_amount; @endphp
                                <tr>
                                    <td>{{ $rIndex + 1 }}</td>
                                    <td>{{ $return->product?->name ?? '-' }}</td>
                                    <td class="text-center">{{ $return->product?->code ?? '-' }}</td>
                                    <td class="text-center">{{ $return->return_quantity }}</td>
                                    <td class="text-end">Rs.{{ number_format($return->selling_price, 2) }}</td>
                                    <td class="text-end">Rs.{{ number_format($return->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No returned items.</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Return Amount:</strong></td>
                                    <td class="text-end">- Rs.@php echo number_format($returnAmount, 2); @endphp</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Net Amount:</strong></td>
                                    <td class="text-end fw-bold">
                                        Rs.@php echo number_format(($selectedSale->subtotal - $selectedSale->discount_amount) - $returnAmount, 2); @endphp
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- ==================== GRAND TOTAL & DUE AMOUNT ==================== --}}
                    <div class="row">
                        <div class="col-7"></div>
                        <div class="col-5">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Grand Total</strong></td>
                                    <td class="text-end">Rs.{{ number_format($selectedSale->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Due Amount</strong></td>
                                    <td class="text-end">Rs.{{ number_format($selectedSale->due_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($selectedSale->notes)
                    <h6 class="text-muted mb-2">NOTES</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0">{{ $selectedSale->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                {{-- ==================== FOOTER BUTTONS ==================== --}}
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">
                        <i class="bi bi-x-circle me-1"></i> Close
                    </button>
                    <div>
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Delete Confirmation Modal --}}
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i> Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                </div>
                <div class="modal-body">
                    @if($selectedSale)
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Warning!</h6>
                        <p class="mb-0">You are about to delete the following sale. This action cannot be undone and will restore product stock.</p>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <p><strong>Invoice:</strong> {{ $selectedSale->invoice_number }}</p>
                            <p><strong>Customer:</strong> {{ $selectedSale->customer->name ?? 'Walk-in Customer' }}</p>
                            <p><strong>Amount:</strong> Rs.{{ number_format($selectedSale->total_amount, 2) }}</p>
                            <p><strong>Date:</strong> {{ $selectedSale->created_at->format('M d, Y') }}</p>
                            <p><strong>Items:</strong> {{ $selectedSale->items->count() }} products</p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="confirmDelete">
                        <i class="bi bi-trash me-1"></i> Delete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="livewire-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <!-- Toast message will be inserted here -->
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }

            .closebtn { top:3%; right:3%; position:absolute; }


    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
                

    }

    .modal-header {
        border-bottom: 1px solid #dee2e6;
    }

    .badge {
        font-size: 0.75em;
    }

    /* Hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.025);
    }
    .table th {
            border-top: none;
            font-weight: 600;
            color: #ffffff;
            background: #3B5B0C;
            background: linear-gradient(0deg,rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Modal management
        Livewire.on('showModal', (modalId) => {
            console.log('Showing modal:', modalId);
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();

                // Close modal when hidden
                modalElement.addEventListener('hidden.bs.modal', function() {
                    Livewire.dispatch('closeModals');
                });
            }
        });

        Livewire.on('hideModal', (modalId) => {
            console.log('Hiding modal:', modalId);
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        });

        // Toast notifications
        Livewire.on('showToast', (event) => {
            const toastElement = document.getElementById('livewire-toast');
            if (toastElement) {
                const toastBody = toastElement.querySelector('.toast-body');
                const toastHeader = toastElement.querySelector('.toast-header');

                if (toastBody) toastBody.textContent = event.message;
                if (toastHeader) {
                    // Remove existing color classes
                    toastHeader.className = 'toast-header text-white';
                    // Add new color class
                    toastHeader.classList.add('bg-' + event.type);
                }

                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        });

        // Close modals when escape key is pressed
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                Livewire.dispatch('closeModals');
            }
        });
    });

    // Prevent multiple clicks on download button
    document.addEventListener('livewire:request-start', (event) => {
        const target = event.detail.component.get('$wire').__instance;
        if (target.__livewire_requests?.downloadInvoice) {
            const buttons = document.querySelectorAll('[wire\\:click="downloadInvoice"]');
            buttons.forEach(button => {
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            });
        }
    });

    document.addEventListener('livewire:request-finish', (event) => {
        const buttons = document.querySelectorAll('[wire\\:click="downloadInvoice"]');
        buttons.forEach(button => {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-download"></i>';
        });
    });
</script>
@endpush