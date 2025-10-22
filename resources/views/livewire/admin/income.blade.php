<div>
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="fw-bold text-dark mb-2">
                    <i class="bi bi-graph-up-arrow text-success me-2"></i> Income Management
                </h3>
                <p class="text-muted mb-0">Track and manage your company revenue efficiently</p>
            </div>
            <div>
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addCashInHandModal">
                    <i class="bi bi-wallet2 me-2"></i> Add Cash-in-Hand
                </button>
                <button class="btn btn-primary">
                    <i class="bi bi-download me-2"></i> Export Report
                </button>
            </div>
        </div>

        <!-- Main Income Metrics Cards -->
        <div class="row mb-5">
            <!-- Today's Income Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card summary-card today h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-container bg-success bg-opacity-10 me-3">
                                <i class="bi bi-currency-dollar text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Today's Income</p>
                                <h4 class="fw-bold text-dark mb-2">
                                    Rs. {{ number_format($todayIncome, 2) }}
                                </h4>
                                <div class="d-flex justify-content-between small">
                                    <div>
                                        <span class="text-muted" style="font-size: 12px;">Cash Sales:</span>
                                        <span class="fw-medium" style="font-size: 12px;">Rs. {{ number_format($cashIncome, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted" style="font-size: 12px;">Cheque:</span>
                                        <span class="fw-medium text-primary" style="font-size: 12px;">Rs. {{ number_format($chequeIncome, 2) }}</span>
                                    </div>
                                </div>

                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash in Hand Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card summary-card cash h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-container bg-warning bg-opacity-10 me-3">
                                <i class="bi bi-wallet2 text-warning fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Cash in Hand</p>
                                <h4 class="fw-bold text-dark mb-2">
                                  Rs. {{ number_format($cashInHand + $todayIncome, 2) }}

                                </h4>

                                <div class="d-flex justify-content-between small">
                                    <div>
                                        <span class="text-muted" style="font-size: 12px;">Base:</span>
                                        <span class="fw-medium" style="font-size: 12px;">Rs. {{ number_format($cashInHand, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted" style="font-size: 12px;">Today:</span>
                                        <span class="fw-medium text-success" style="font-size: 12px;">+ Rs. {{ number_format($cashIncome, 2) }}</span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Income Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card summary-card total h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-container bg-primary bg-opacity-10 me-3">
                                <i class="bi bi-cash-stack text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Total Deposit</p>
                                <h4 class="fw-bold text-dark mb-2">Rs. {{number_format($totalDeposits, 2)}}</h4>
                                <div class="d-flex justify-content-between small">
                                    <div>
                                        <span class="text-muted" style="font-size: 12px;">This Month:</span>
                                        <span class="fw-medium" style="font-size: 12px;">Rs. {{ number_format($thisMonthDeposit, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted" style="font-size: 12px;">Previous:</span>
                                        <span class="fw-medium text-primary" style="font-size: 12px;">Rs. {{ number_format($previousMonthDeposit, 2) }}</span>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Daily Income & Cash Deposit Section -->
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-calendar2-day text-success me-2"></i> Daily Income Entries
                    </h5>
                    <p class="text-muted small mb-0">Record daily sales and automatically deposit cash into Cash in Hand balance</p>
                </div>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addIncomeModal">
                    <i class="bi bi-plus-lg me-1"></i> Add & Deposit
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Source/Description</th>
                                <th class="text-end">Amount (Rs.)</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $deposit)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-medium text-dark">{{ $deposit->date->format('Y-m-d') }}</span>
                                </td>
                                <td>{{ $deposit->description ?? '—' }}</td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($deposit->amount, 2) }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-link text-danger p-0" wire:click="deleteDeposit({{ $deposit->id }})">
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No deposits recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Daily Income -->
    <div class="modal fade" id="addIncomeModal" tabindex="-1" aria-labelledby="addIncomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle text-success me-2"></i> Add & Deposit Daily Income
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="addDeposit">
                    <div class="modal-body">

                        <!-- Cash Summary Section -->
                        <div class="p-3 mb-4 bg-light rounded-3">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi bi-wallet2 text-success me-2"></i> Current Cash Summary
                            </h6>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Base Cash:</span>
                                <span class="fw-semibold text-dark">Rs. {{ number_format($cashInHand, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Today Cash:</span>
                                <span class="fw-semibold text-primary">Rs. {{ number_format($todayIncome, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Total Cash in Hand:</span>
                                <span class="fw-semibold text-success">Rs. {{ number_format($cashInHand + $todayIncome, 2) }}</span>
                            </div>
                        </div>

                        <!-- Date & Amount in One Row -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control" wire:model="depositDate">
                                @error('depositDate') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Amount (Rs.)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rs.</span>
                                    <input type="number" step="0.01" class="form-control" wire:model="depositAmount" placeholder="0.00">
                                </div>
                                @error('depositAmount') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description / Source</label>
                            <input type="text" class="form-control" wire:model="depositDescription" placeholder="e.g., POS Sales / Invoice #125">
                            @error('depositDescription') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="alert alert-info bg-info bg-opacity-10 border-0 small" role="alert">
                            <i class="bi bi-info-circle me-1"></i>
                            This income will be added to today's records and automatically deposited into Cash in Hand.
                        </div>

                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle me-1"></i> Record & Deposit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Cash-in-Hand -->
    <div class="modal fade" id="addCashInHandModal" tabindex="-1" aria-labelledby="addCashInHandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-wallet2 text-warning me-2"></i> Add Cash-in-Hand
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="updateCashInHand">
                    <div class="modal-body">

                        <!-- Current Cash Status -->
                        <div class="p-3 mb-4 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi bi-info-circle text-warning me-2"></i> Current Cash Status
                            </h6>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Current Base Cash:</span>
                                <span class="fw-semibold text-dark">Rs. {{ number_format($cashInHand, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Today's Income:</span>
                                <span class="fw-semibold text-primary">Rs. {{ number_format($todayIncome, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Total Available:</span>
                                <span class="fw-semibold text-success">Rs. {{ number_format($cashInHand + $todayIncome, 2) }}</span>
                            </div>
                        </div>

                        <!-- Amount Input -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Add Cash Amount (Rs.)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rs.</span>
                                <input type="number" step="0.01" class="form-control"
                                    placeholder="Enter amount to add" wire:model="newCashInHand">
                            </div>
                            <div class="form-text text-muted small">
                                Enter the amount you want to add to your base cash-in-hand.
                            </div>
                        </div>



                        <!-- New Total Preview -->
                        <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="bi bi-calculator text-success me-2"></i> New Total Preview
                            </h6>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">New Base Cash:</span>
                                <span class="fw-semibold text-success">Rs. {{ number_format($cashInHand, 2) }}</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="bi bi-check2-circle me-1"></i> Update Cash-in-Hand
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

  



</div>

@push('styles')
<style>
    .summary-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .summary-card.today {
        border-left-color: #198754;
    }

    .summary-card.total {
        border-left-color: #4361ee;
    }

    .summary-card.cash {
        border-left-color: #ffc107;
    }

    .icon-container {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

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
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
        border-color: #198754;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }

    .btn-success:hover {
        background-color: #157347;
        border-color: #157347;
        transform: translateY(-2px);
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-warning:hover {
        background-color: #ffca2c;
        border-color: #ffca2c;
        transform: translateY(-2px);
    }
</style>
@endpush
@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('close-modal', (event) => {
            const modalId = event.modalId || event; // handle both old/new formats
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
                }, 1500); // Refresh after 1.5 seconds to show success message
            });

    // Auto-hide success alert after 3 seconds
    document.addEventListener('livewire:navigated', () => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show');
                alert.remove();
            }, 3000);
        }
    });
</script>
@endpush