<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-pie-chart-fill text-primary me-2"></i> Expense Management
            </h3>
            <p class="text-muted mb-0">Track and manage your company expenses efficiently</p>
        </div>
        <div>
            <button class="btn btn-primary">
                <i class="bi bi-download me-2"></i> Export ToDay Report
            </button>
            <button class="btn btn-primary">
                <i class="bi bi-download me-2"></i> Export Month Report
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-5">
        <!-- Today's Expenses -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card summary-card today h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-container bg-success bg-opacity-10 me-3">
                            <i class="bi bi-calendar-day text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Today's Expenses</p>
                            <h4 class="fw-bold mb-0">Rs.{{ number_format($todayTotal, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month's Expenses -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card summary-card month h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-container bg-info bg-opacity-10 me-3">
                            <i class="bi bi-calendar3 text-info fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">This Month's Expenses</p>
                            <h4 class="fw-bold mb-0">Rs.{{ number_format($monthTotal, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card summary-card total h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-container bg-primary bg-opacity-10 me-3">
                            <i class="bi bi-cash-coin text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Expenses</p>
                            <h4 class="fw-bold mb-0">Rs.{{ number_format($overallTotal, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Summary Cards -->

    <!-- Split Tables -->
    <div class="row g-4">
        <!-- Daily Expenses -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-journal-text text-primary me-2"></i> Daily Expenses
                        </h5>
                        <p class="text-muted small mb-0">Small daily costs like snacks, printouts, or stationery</p>
                    </div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addDailyExpenseModal">
                        <i class="bi bi-plus-lg me-1"></i> Add
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dailyExpenses as $expense)
                                <tr>
                                    <td class="ps-4"><span class="fw-medium text-dark">{{ $expense->category }}</span></td>
                                    <td>{{ $expense->description ?? '—' }}</td>
                                    <td><span class="fw-bold text-dark">Rs.{{ $expense->amount }}</span></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-link text-primary p-0" wire:click="editExpense({{ $expense->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-link text-danger p-0" wire:click="deleteExpense({{ $expense->id }})">
                                            <i class="bi bi-trash fs-6"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No daily expenses found</td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Expenses -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-calendar2-check text-info me-2"></i> Monthly Expenses
                        </h5>
                        <p class="text-muted small mb-0">Regular monthly costs like bills, rent, and subscriptions</p>
                    </div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addMonthlyExpenseModal">
                        <i class="bi bi-plus-lg me-1"></i> Add
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($monthlyExpenses as $expense)
                                <tr>
                                    <td class="ps-4">{{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}
                                    </td>
                                    <td><span class="fw-medium text-dark">{{ $expense->category }}</span></td>
                                    <td><span class="fw-bold text-dark">Rs.{{ $expense->amount }}</span></td>
                                    <td>
                                        @if($expense->status == 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                        @elseif($expense->status == 'Pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                        <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-link text-primary p-0" wire:click="editExpense({{ $expense->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-link text-danger p-0" wire:click="deleteExpense({{ $expense->id }})">
                                            <i class="bi bi-trash fs-6"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No monthly expenses found</td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Daily Expense Modal -->
    <div class="modal fade" id="addDailyExpenseModal" tabindex="-1" aria-labelledby="addDailyExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Add Daily Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveDailyExpense">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select" wire:model="category" required>
                                <option value="">Select Category</option>
                                <option value="Snacks">Snacks</option>
                                <option value="Stationery">Stationery</option>
                                <option value="Transport">Transport</option>
                                <option value="Meals">Meals</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" wire:model="description" rows="2" placeholder="Add description..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" wire:model="amount" placeholder="e.g., 100" required>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i> Save Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Monthly Expense Modal -->
    <div class="modal fade" id="addMonthlyExpenseModal" tabindex="-1" aria-labelledby="addMonthlyExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle-fill text-info me-2"></i> Add Monthly Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveMonthlyExpense">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" wire:model="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select" wire:model="category" required>
                                <option value="">Select Category</option>
                                <option value="Electricity Bill">Electricity Bill</option>
                                <option value="Internet Bill">Internet Bill</option>
                                <option value="Office Rent">Office Rent</option>
                                <option value="Software Subscription">Software Subscription</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" wire:model="amount" placeholder="e.g., 500" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="">Select Status</option>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-check2-circle me-1"></i> Save Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Daily Expense Modal -->
    <div wire:ignore.self class="modal fade" id="editDailyExpenseModal" tabindex="-1" aria-labelledby="editDailyExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Daily Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateExpense">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select" wire:model="category" required>
                                <option value="">Select Category</option>
                                <option value="Snacks">Snacks</option>
                                <option value="Stationery">Stationery</option>
                                <option value="Transport">Transport</option>
                                <option value="Meals">Meals</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" wire:model="description" rows="2" placeholder="Add description..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" wire:model="amount" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i> Update Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Monthly Expense Modal -->
    <div wire:ignore.self class="modal fade" id="editMonthlyExpenseModal" tabindex="-1" aria-labelledby="editMonthlyExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-info me-2"></i> Edit Monthly Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateExpense">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" wire:model="date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select" wire:model="category" required>
                                <option value="">Select Category</option>
                                <option value="Electricity Bill">Electricity Bill</option>
                                <option value="Internet Bill">Internet Bill</option>
                                <option value="Office Rent">Office Rent</option>
                                <option value="Software Subscription">Software Subscription</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" wire:model="amount" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="">Select Status</option>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-check2-circle me-1"></i> Update Expense
                            </button>
                        </div>
                    </form>
                </div>
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
        border-left-color: #4cc9f0;
    }

    .summary-card.month {
        border-left-color: #4895ef;
    }

    .summary-card.total {
        border-left-color: #4361ee;
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
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        border-color: #4361ee;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .btn-primary:hover {
        background-color: #3f37c9;
        border-color: #3f37c9;
        transform: translateY(-2px);
    }

    .btn-info {
        background-color: #4895ef;
        border-color: #4895ef;
    }
</style>
@endpush
@push('scripts')
<script>
    Livewire.on('close-modal', ({
        id
    }) => {
        const modal = bootstrap.Modal.getInstance(document.getElementById(id));
        if (modal) modal.hide();
    });

    // Show Edit Expense Modal
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('close-modal', ({
            id
        }) => {
            const modal = bootstrap.Modal.getInstance(document.getElementById(id));
            if (modal) modal.hide();
        });

        Livewire.on('open-modal', ({
            id
        }) => {
            const modal = new bootstrap.Modal(document.getElementById(id));
            modal.show();
        });
    });
</script>
@endpush