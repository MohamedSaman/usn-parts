<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-arrow-return-left text-primary me-2"></i> Product Returns List
            </h3>
            <p class="text-muted mb-0">View and manage all product returns</p>
        </div>
    </div>

    <!-- Returns Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i> Returns List
            </h5>
            {{-- You can add a badge for total records if needed --}}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Invoice Number</th>
                            <th>Product</th>
                            <th>Return Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $index => $return)
                        <tr style="cursor:pointer" wire:click="showReturnDetails({{ $return->id }})">
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td>{{ $return->sale?->invoice_number ?? '-' }}</td>
                            <td>{{ $return->product?->name ?? '-' }}</td>
                            <td>{{ $return->return_quantity }}</td>
                            <td>Rs.{{ number_format($return->selling_price, 2) }}</td>
                            <td>Rs.{{ number_format($return->total_amount, 2) }}</td>
                            <td>{{ $return->created_at?->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                {{-- No view button as requested --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-arrow-return-left display-4 d-block mb-2"></i>
                                No returns found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Return Details Modal (Bill/Receipt Style) -->
    <div wire:ignore.self class="modal fade" id="returnDetailsModal" tabindex="-1" aria-labelledby="returnDetailsModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt me-2"></i> Return Bill
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                @if($selectedReturn)
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">RETURN/INVOICE INFO</h6>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td width="40%"><strong>Invoice Number:</strong></td>
                                    <td>{{ $selectedReturn->sale?->invoice_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date & Time:</strong></td>
                                    <td>{{ $selectedReturn->created_at?->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">CUSTOMER INFO</h6>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td width="40%"><strong>Name:</strong></td>
                                    <td>{{ $selectedReturn->sale?->customer?->name ?? 'Walk-in Customer' }}</td>
                                </tr>
                                @if($selectedReturn->sale && $selectedReturn->sale->customer)
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $selectedReturn->sale->customer->phone }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3">RETURNED ITEM</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Code</th>
                                    <th class="text-center">Return Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $selectedReturn->product?->name ?? '-' }}</td>
                                    <td>{{ $selectedReturn->product?->code ?? '-' }}</td>
                                    <td class="text-center">{{ $selectedReturn->return_quantity }}</td>
                                    <td class="text-end">Rs.{{ number_format($selectedReturn->selling_price, 2) }}</td>
                                    <td class="text-end">Rs.{{ number_format($selectedReturn->total_amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">NOTES</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0">{{ $selectedReturn->notes ?? 'No notes.' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column align-items-end justify-content-end h-100">
                                <table class="table table-borderless w-auto mb-0">
                                    <tr>
                                        <td class="text-end"><strong>Total Return:</strong></td>
                                        <td class="text-end">Rs.{{ number_format($selectedReturn->total_amount, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
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

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
        }

        .badge {
            font-size: 0.75em;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showModal', (modalId) => {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            });
            Livewire.on('hideModal', (modalId) => {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });
        });
    </script>
    @endpush
</div>