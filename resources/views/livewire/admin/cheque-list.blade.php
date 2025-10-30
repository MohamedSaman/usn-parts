<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-journal-check text-primary me-2"></i> Cheque Management
            </h3>
            <p class="text-muted mb-0">View and manage all customer cheques</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-5">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pending Cheques</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $pendingCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-clock-history fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Completed Cheques</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $completeCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-check2-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-danger border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Overdue Cheques</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $overdueCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cheque Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i> Cheque List
            </h5>
            <span class="badge bg-primary">{{ $cheques->total() ?? 0 }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Cheque No</th>
                            <th>Customer</th>
                            <th class="text-center">Bank</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cheques as $cheque)
                        <tr wire:key="cheque-{{ $cheque->id }}">
                            <td class="ps-4">{{ $cheque->cheque_number }}</td>
                            <td>{{ $cheque->customer->name ?? '-' }}</td>
                            <td class="text-center">{{ $cheque->bank_name }}</td>
                            <td class="text-center">Rs.{{ number_format($cheque->cheque_amount, 2) }}</td>
                            <td class="text-center">{{ $cheque->cheque_date ? date('M d, Y', strtotime($cheque->cheque_date)) : '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $cheque->status == 'pending' ? 'warning' : ($cheque->status == 'complete' ? 'success' : 'danger') }}">
                                    {{ ucfirst($cheque->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($cheque->status == 'pending' || $cheque->status == 'overdue')
                                <div class="btn-group btn-group-sm">
                                    <!-- Button triggers -->
                                    <button class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmCompleteModal"
                                        wire:click="setSelectedCheque({{ $cheque->id }})">
                                        <i class="bi bi-check2-circle"></i> Complete
                                    </button>

                                    <button class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmReturnModal"
                                        wire:click="setSelectedCheque({{ $cheque->id }})">
                                        <i class="bi bi-arrow-counterclockwise"></i> Return
                                    </button>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-x-circle display-4 d-block mb-2"></i>
                                No cheques found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($cheques->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $cheques->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Confirm Complete Modal -->
    <div wire:ignore.self class="modal fade" id="confirmCompleteModal" tabindex="-1" aria-labelledby="confirmCompleteModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered"> <!-- 👈 Centered here -->
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-check2-circle me-2"></i> Confirm Complete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                </div>
                <div class="modal-body text-center"> <!-- 👈 Centered text -->
                    <p class="fw-semibold fs-5">Are you sure you want to mark this cheque as <strong>Complete</strong>?</p>
                </div>
                <div class="modal-footer justify-content-center"> <!-- 👈 Center footer buttons -->
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="completeCheque">Yes, Complete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Return Modal -->
    <div wire:ignore.self class="modal fade" id="confirmReturnModal" tabindex="-1"
        aria-labelledby="confirmReturnModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered"> <!-- 👈 Centered -->
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-counterclockwise me-2"></i> Confirm Return
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fw-semibold fs-5">Are you sure you want to <strong>Return</strong> this cheque?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="returnCheque">Yes, Return</button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('script')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('showModal', (modalId) => {
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        });

        Livewire.on('hideModal', (modalId) => {
            const modalEl = document.getElementById(modalId);
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    });
</script>
@endpush