<div class="container-fluid py-3">
    {{-- ==================== PAGE HEADER ==================== --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-cash-stack text-success me-2"></i> Admin Sales Management
            </h3>
            <p class="text-muted mb-0">View and manage admin sales</p>
        </div>
        <div>
            <a href="{{ route('admin.sales-system') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> New Admin Sale
            </a>
        </div>
    </div>

    {{-- ==================== STATISTICS CARDS ==================== --}}
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Admin Sales</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['total_sales'] }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-cart-check fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rs.{{ number_format($stats['total_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-currency-dollar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pending Payments</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rs.{{ number_format($stats['pending_payments'], 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-clock-history fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Today's Sales</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['today_sales'] }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-calendar-day fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== FILTERS ==================== --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control"
                            placeholder="Search by invoice, customer name or phone..."
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
                    <button class="btn btn-outline-secondary w-100"
                        wire:click="$set('dateFilter', '')">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== SALES TABLE ==================== --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i> Admin Sales List
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
                        <tr wire:key="sale-{{ $sale->id }}"
                            style="cursor:pointer;"
                            >
                            <td class="ps-4" wire:click="viewSale({{ $sale->id }})">
                                <div class="fw-bold text-primary">{{ $sale->invoice_number }}</div>
                                <small class="text-muted">#{{ $sale->sale_id }}</small>
                            </td>
                            <td wire:click="viewSale({{ $sale->id }})">
                                @if($sale->customer)
                                <div class="fw-medium">{{ $sale->customer->name }}</div>
                                <small class="text-muted">{{ $sale->customer->phone }}</small>
                                @else
                                <span class="text-muted">Walk-in Customer</span>
                                @endif
                            </td>
                            <td class="text-center" wire:click="viewSale({{ $sale->id }})">{{ $sale->created_at->format('M d, Y') }}</td>
                            <td class="text-center fw-bold" wire:click="viewSale({{ $sale->id }})">Rs.{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-center" wire:click="viewSale({{ $sale->id }})">
                                <span class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                            </td>
                            <td class="text-center" wire:click="viewSale({{ $sale->id }})"><span class="badge bg-warning">{{ strtoupper($sale->sale_type) }}</span></td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-gear-fill"></i> Actions
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">

                                        <!-- Download Invoice -->
                                        <li>
                                            <button class="dropdown-item"
                                                wire:click="downloadInvoice({{ $sale->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="downloadInvoice({{ $sale->id }})">

                                                <span wire:loading wire:target="downloadInvoice({{ $sale->id }})">
                                                    <i class="spinner-border spinner-border-sm me-2"></i>
                                                    Loading...
                                                </span>
                                                <span wire:loading.remove wire:target="downloadInvoice({{ $sale->id }})">
                                                    <i class="bi bi-download text-success me-2"></i>
                                                    Download Invoice
                                                </span>
                                            </button>
                                        </li>

                                        <!-- Delete Sale -->
                                        <li>
                                            <button class="dropdown-item"
                                                wire:click="deleteSale({{ $sale->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="deleteSale({{ $sale->id }})">

                                                <span wire:loading wire:target="deleteSale({{ $sale->id }})">
                                                    <i class="spinner-border spinner-border-sm me-2"></i>
                                                    Loading...
                                                </span>
                                                <span wire:loading.remove wire:target="deleteSale({{ $sale->id }})">
                                                    <i class="bi bi-trash text-danger me-2"></i>
                                                    Delete
                                                </span>
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-cart-x display-4 d-block mb-2"></i>
                                No admin sales found.
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

    {{-- ==================== VIEW SALE MODAL (same structure as the photo) ==================== --}}
    <div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1"
        aria-labelledby="viewModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="printableInvoice">

                {{-- Header – logo + company name --}}
                <div class="modal-header text-center border-0">
                    <div class="w-100">
                        <img src="{{ asset('images/USN.png') }}" alt="Logo"
                            class="img-fluid mb-2" style="max-height:60px;">
                        <h4 class="mb-0 fw-bold">USN AUTO PARTS</h4>
                        <p class="mb-0 small text-muted">
                        </p>
                    </div>
                    <button type="button" class="btn-close btn-close-white closebtn"
                        wire:click="closeModals"></button>
                </div>

                @if($selectedSale)
                <div class="modal-body">

                    {{-- Customer + Invoice info (two columns) --}}
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
                                    <td><strong>Invoice No :</strong></td>
                                    <td>{{ $selectedSale->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sale Status :</strong></td>
                                    <td>Completed</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status :</strong></td>
                                    <td>Pending</td>
                                </tr>
                                <tr>
                                    <td><strong>Invoice Date :</strong></td>
                                    <td>{{ $selectedSale->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Due Date :</strong></td>
                                    <td>00-00-00</td>
                                </tr>
                                <tr>
                                    <td><strong>Sales Person :</strong></td>
                                    <td>ART STORE</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Items table --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:15%">ITEM CODE</th>
                                    <th>DESCRIPTION</th>
                                    <th class="text-center" style="width:12%">QTY</th>
                                    <th class="text-end" style="width:12%">UNIT PRICE</th>
                                    <th class="text-end" style="width:12%">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedSale->items as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->product_code }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td class="text-center">{{ $item->quantity }} Pc(s)</td>
                                    <td class="text-end">{{ number_format($item->unit_price,2) }}</td>
                                    <td class="text-end">{{ number_format($item->total,2) }}</td>
                                </tr>
                                @endforeach
                                @if($selectedSale->items->count() == 0)
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No items</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals – right-aligned block --}}
                    <div class="row">
                        <div class="col-7"></div>
                        <div class="col-5">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-end"><strong>Total Amount (LKR)</strong></td>
                                    <td class="text-end">{{ number_format($selectedSale->total_amount,2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>Returns</strong></td>
                                    <td class="text-end">(0.00)</td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>Paid (LKR)</strong></td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>Balance (LKR)</strong></td>
                                    <td class="text-end">{{ number_format($selectedSale->due_amount,2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Footer – logos + address + note --}}
                    <div class="mt-4 text-center small">

                        <p class="mb-0">
                            <strong>ADDRESS :</strong> 103 H, Yatiyanthota Road, Seethawaka, Avissawella<br>
                            <strong>TEL :</strong> (076) 9085252, <strong>EMAIL :</strong> autopartsusn@gmail.com
                        </p>
                        <p class="mt-1 text-muted">
                            Goods return will be accepted within 10 days only. Electrical and body parts non-returnable.
                        </p>
                    </div>

                </div>
                @endif

                {{-- Modal footer buttons --}}
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">
                        <i class="bi bi-x-circle me-1"></i> Close
                    </button>
                    <div>

                        <button type="button" class="btn btn-outline-primary"
                            onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== EDIT SALE MODAL (unchanged) ==================== --}}
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Sale</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer</label>
                            <select class="form-select" wire:model="editCustomerId">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Status</label>
                            <select class="form-select" wire:model="editPaymentStatus">
                                <option value="pending">Pending</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" rows="3" wire:model="editNotes"
                                placeholder="Additional notes..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Due Amount</label>
                            <input type="number" class="form-control" wire:model="editDueAmount" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Paid Amount</label>
                            <input type="number" class="form-control" wire:model="editPaidAmount" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pay Balance Amount</label>
                            <input type="number" class="form-control" wire:model="editPayBalanceAmount" min="0">
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    wire:click="payFullBalance">Pay Full Balance</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    wire:click="resetPayBalance">Reset Payment</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancel</button>
                    <button type="button" class="btn btn-warning" wire:click="updateSale">
                        <i class="bi bi-check-circle me-1"></i> Update Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE CONFIRM MODAL (unchanged) ==================== --}}
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1"
        aria-labelledby="deleteModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Deletion</h5>
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

    {{-- ==================== TOAST ==================== --}}
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="livewire-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>
</div>

{{-- ==================== STYLES (unchanged – only print tweaks) ==================== --}}
@push('styles')
<style>
    .modal-header {
        background: linear-gradient(90deg, #3b5b0c, #8eb922);
        color: #fff;
    }

    .modal-title i {
        color: #ffc107;
    }

    .closebtn {
        top: 3%;
        right: 3%;
        position: absolute;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #ffffff;
        background: #3B5B0C;
        background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;

    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printableInvoice,
        #printableInvoice * {
            visibility: visible;
        }

        #printableInvoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            background: #fff;
            font-size: 11pt;
            color: #000;
        }

        .modal,
        .modal-dialog,
        .modal-content {
            all: unset;
        }

        .modal-footer,
        .btn,
        .btn-close {
            display: none !important;
        }

        .modal-header {
            border: none;
            padding: 0;
            text-align: center;
            margin-bottom: 1rem;
        }

        .modal-header img {
            max-height: 55px;
        }

        .modal-header h4 {
            margin: 4px 0;
            font-size: 1.4rem;
        }

        .modal-header p {
            margin: 0;
            font-size: 0.85rem;
        }

        .row>.col-6 {
            page-break-inside: avoid;
        }

        .row>.col-6:first-child {
            text-align: left;
        }

        .row>.col-6:last-child {
            text-align: right;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: .8rem;
        }

        .table th,
        .table td {
            border: 1px solid #999;
            padding: 4px 6px;
        }

        .table th {
            background: #e9ecef;
            -webkit-print-color-adjust: exact;
        }

        .table-sm {
            font-size: 0.9rem;
        }

        .table-sm td {
            border: none;
            padding: 2px 4px;
        }

        .table-sm strong {
            min-width: 110px;
            display: inline-block;
        }

        .d-flex img {
            height: 30px;
            margin: 0 8px;
        }

        .text-muted {
            font-size: 0.8rem;
        }
    }
</style>
@endpush

{{-- ==================== SCRIPTS (unchanged) ==================== --}}
@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showModal', (modalId) => {
            const el = document.getElementById(modalId);
            if (el) new bootstrap.Modal(el).show();
        });
        Livewire.on('hideModal', (modalId) => {
            const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
            if (modal) modal.hide();
        });
        Livewire.on('showToast', (e) => {
            const toast = document.getElementById('livewire-toast');
            toast.querySelector('.toast-body').textContent = e.message;
            toast.querySelector('.toast-header').className = 'toast-header text-white bg-' + e.type;
            new bootstrap.Toast(toast).show();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') Livewire.dispatch('closeModals');
        });
    });

    document.addEventListener('livewire:request-start', e => {
        if (e.detail.component.get('$wire').__instance.__livewire_requests?.downloadInvoice) {
            document.querySelectorAll('[wire\\:click="downloadInvoice"]').forEach(b => {
                b.disabled = true;
                b.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            });
        }
    });
    document.addEventListener('livewire:request-finish', () => {
        document.querySelectorAll('[wire\\:click="downloadInvoice"]').forEach(b => {
            b.disabled = false;
            b.innerHTML = '<i class="bi bi-download"></i>';
        });
    });
</script>
@endpush