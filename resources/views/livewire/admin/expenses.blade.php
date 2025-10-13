<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-bank text-primary me-2"></i> Financing - Expenses Overview
            </h3>
            <p class="text-muted mb-0">Easily track and manage your daily and monthly company expenses</p>
        </div>
        <div>
            <button class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-download me-1"></i> Export Monthly Expenses
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-calendar-day text-success fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">Today's Expenses</p>
                        <h5 class="fw-bold mb-0">$240</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-calendar3 text-info fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">This Month's Expenses</p>
                        <h5 class="fw-bold mb-0">$1,280</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-cash-coin text-primary fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">Total Expenses</p>
                        <h5 class="fw-bold mb-0">$12,540</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Split Tables -->
    <div class="row g-4">
        <!-- Daily Expenses -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative">
                <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-journal-text text-primary me-2"></i> Daily Expenses
                        </h5>
                        <p class="text-muted small mb-0">Small daily costs like snacks, printouts, or stationery</p>
                    </div>
                    <button class="btn btn-light border rounded-circle shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#addDailyExpenseModal" title="Add Daily Expense">
                        <i class="bi bi-plus-lg text-primary"></i>
                    </button>
                </div>

                <div class="card-body px-0 py-2">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Snacks</td>
                                    <td>Tea and biscuits</td>
                                    <td>$15</td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Stationery</td>
                                    <td>Printer ink & A4 sheets</td>
                                    <td>$35</td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Expenses -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative">
                <div class="card-header bg-transparent border-bottom-0 pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-calendar2-check text-info me-2"></i> Monthly Expenses
                        </h5>
                        <p class="text-muted small mb-0">Regular monthly costs like bills, rent, and subscriptions</p>
                    </div>
                    <button class="btn btn-light border rounded-circle shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#addMonthlyExpenseModal" title="Add Monthly Expense">
                        <i class="bi bi-plus-lg text-info"></i>
                    </button>
                </div>

                <div class="card-body px-0 py-2">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2025-10-01</td>
                                    <td>Electricity Bill</td>
                                    <td>$150</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>2025-10-05</td>
                                    <td>Internet Bill</td>
                                    <td>$50</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
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
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Add Daily Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveDailyExpense">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" wire:model="category" placeholder="e.g., Snacks, Stationery" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="description" rows="2" placeholder="Add description..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" wire:model="amount" placeholder="e.g., 100" required>
                        </div>
                        <div class="text-end">
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
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle-fill text-info me-2"></i> Add Monthly Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveMonthlyExpense">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" wire:model="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" wire:model="category" placeholder="e.g., Internet Bill" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" wire:model="amount" placeholder="e.g., 500" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="">Select</option>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-check2-circle me-1"></i> Save Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
