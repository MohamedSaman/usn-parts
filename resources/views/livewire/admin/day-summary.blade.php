<div class="container-fluid py-4" style="background-color:#f5fdf1ff;">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold" style="color:#3b5b0c;">
                                <i class="bi bi-calendar-check me-2" style="color:#8eb922;"></i>Day Summary List & Deposit by Cash
                            </h4>
                            <small class="text-muted">View all closed POS sessions</small>
                        </div>
                        <div>
                            <button class="btn btn-sm rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); color:white;" wire:click="resetFilters">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filters
                            </button>
                            <button type="button" class="btn btn-success btn-sm rounded-0 ms-2" data-bs-toggle="modal" data-bs-target="#addDepositModal">
                                <i class="bi bi-plus-lg me-1"></i>Add & Deposit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-semibold" style="color:#3b5b0c;">
                        <i class="bi bi-search me-1"></i>Search
                    </label>
                    <input type="text" class="form-control rounded-0" wire:model.live="search" placeholder="Search by date or user...">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-semibold" style="color:#3b5b0c;">
                        <i class="bi bi-calendar-event me-1"></i>From Date
                    </label>
                    <input type="date" class="form-control rounded-0" wire:model.live="dateFrom">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-semibold" style="color:#3b5b0c;">
                        <i class="bi bi-calendar-event me-1"></i>To Date
                    </label>
                    <input type="date" class="form-control rounded-0" wire:model.live="dateTo">
                </div>
            </div>
        </div>
    </div>

    {{-- Sessions List --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold" style="color:#3b5b0c;">
                        <i class="bi bi-list-ul me-2" style="color:#8eb922;"></i>POS Sessions ({{ $sessions->total() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($sessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color:#f8f9fa;">
                                <tr>
                                    <th class="border-0 py-3">#</th>
                                    <th class="border-0 py-3">Session Date</th>
                                    <th class="border-0 py-3">Cashier</th>
                                    <th class="border-0 py-3">Opening Cash</th>
                                    <th class="border-0 py-3">Closing Cash</th>
                                    <th class="border-0 py-3">Total Sales</th>
                                    <th class="border-0 py-3">Cash Sales</th>
                                    <th class="border-0 py-3">Expenses</th>
                                    <th class="border-0 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $index => $session)
                                <tr wire:click="viewDetails({{ $session->id }})"
                                    style="cursor: pointer; transition: background-color 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f0f8f0';"
                                    onmouseout="this.style.backgroundColor='';">
                                    <td>{{ $sessions->firstItem() + $index }}</td>
                                    <td>
                                        <strong style="color:#3b5b0c;">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($session->session_date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <i class="bi bi-person-circle me-1" style="color:#8eb922;"></i>
                                        {{ $session->user->name ?? 'N/A' }}
                                    </td>
                                    <td>Rs.{{ number_format($session->opening_cash, 2) }}</td>
                                    <td>Rs.{{ number_format($session->closing_cash, 2) }}</td>
                                    <td>
                                        <strong style="color:#3b5b0c;">Rs.{{ number_format($session->total_sales, 2) }}</strong>
                                    </td>
                                    <td>Rs.{{ number_format($session->cash_sales, 2) }}</td>
                                    <td>Rs.{{ number_format($session->expenses, 2) }}</td>
                                    <td>
                                        <span class="badge bg-success rounded-0">
                                            <i class="bi bi-check-circle me-1"></i>{{ ucfirst($session->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="card-footer bg-white">
                        {{ $sessions->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="text-muted mt-3">No closed sessions found</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Adding Deposit -->
    <div class="modal fade" id="addDepositModal" tabindex="-1" aria-labelledby="addDepositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); color:white;">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add & Deposit
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="addDeposit">
                    <div class="modal-body">

                        <!-- Today's Summary Section -->
                        <div class="p-3 mb-4 rounded-0" style="background-color: #f5fdf1ff; border: 1px solid #8eb922;">
                            <h6 class="fw-bold mb-3" style="color:#3b5b0c;">
                                <i class="bi bi-wallet2 me-2" style="color:#8eb922;"></i>Today's Cash Summary
                            </h6>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Opening Cash:</span>
                                <span class="fw-semibold" style="color:#3b5b0c;">Rs. {{ number_format($openingCash, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Today's Cash Amount:</span>
                                <span class="fw-semibold text-success">Rs. {{ number_format($todayCashAmount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Today's Expenses:</span>
                                <span class="fw-semibold text-danger">Rs. {{ number_format($todayExpenses, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Today's Refunds:</span>
                                <span class="fw-semibold text-danger">Rs. {{ number_format($todayRefunds, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Today's Deposit Amount:</span>
                                <span class="fw-semibold" style="color:#8eb922;">Rs. {{ number_format($todayDepositAmount, 2) }}</span>
                            </div>
                            <hr style="border-color: #8eb922;">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted fw-bold">Remaining Cash:</span>
                                <span class="fw-bold text-success">Rs. {{ number_format($openingCash + $todayCashAmount - $todayExpenses - $todayRefunds - $todayDepositAmount, 2) }}</span>
                            </div>
                        </div>

                        <!-- Date & Amount in One Row -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold" style="color:#3b5b0c;">Date</label>
                                <input type="date" class="form-control rounded-0" wire:model="depositDate">
                                @error('depositDate') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold" style="color:#3b5b0c;">Amount (Rs.)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light rounded-0">Rs.</span>
                                    <input type="number" step="0.01" class="form-control rounded-0" wire:model="depositAmount" placeholder="0.00">
                                </div>
                                @error('depositAmount') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Description / Source</label>
                            <input type="text" class="form-control rounded-0" wire:model="depositDescription" placeholder="e.g., POS Sales / Invoice #125">
                            @error('depositDescription') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="alert alert-info rounded-0 border-0 small" role="alert">
                            <i class="bi bi-info-circle me-1"></i>
                            This deposit will be added to your records.
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); color:white;">
                            <i class="bi bi-check2-circle me-1"></i>Add Deposit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@push('styles')
<style>
    .form-control,
    .btn {
        border-radius: 0 !important;
    }

    .table tbody tr {
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('close-modal', (event) => {
            const modalId = event.modalId || event;
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });
    });

    Livewire.on('refreshPage', () => {
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    });
</script>
@endpush