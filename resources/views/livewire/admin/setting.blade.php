<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-gear-fill text-primary" style="font-size: 2.2rem;"></i>
        <div class="ms-3">
            <h1 class="h3 fw-bold mb-0">System Settings</h1>
            <p class="text-muted mb-0">Manage daily and monthly expense categories.</p>
        </div>
    </div>

    {{-- Accordion --}}
    <div class="accordion" id="settingsAccordion">
        <div class="accordion-item border-0 mb-4 shadow-sm rounded-3">
            <h2 class="accordion-header" id="headingExpenseTypes">
                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseExpenseTypes" aria-expanded="true"
                    aria-controls="collapseExpenseTypes">
                    <i class="bi bi-tags-fill fs-5 me-3"></i>
                    Manage Expense Categories
                </button>
            </h2>

            <div id="collapseExpenseTypes" class="accordion-collapse collapse show"
                aria-labelledby="headingExpenseTypes" data-bs-parent="#settingsAccordion">
                <div class="accordion-body">

                    {{-- Add Category Form --}}
                    <div class="bg-light p-3 p-md-4 rounded-3 mb-4 border">
                        <h3 class="h5 fw-semibold mb-3">Add New Expense Category</h3>
                        <form wire:submit.prevent="addCategory" id="expenseForm">
                            <div class="row g-3 align-items-end">

                                <div class="col-12 col-md-4">
                                    <label class="form-label">Category Type</label>
                                    <select wire:model="expense_type"
                                        class="form-select @error('expense_type') is-invalid @enderror">
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    @error('expense_type')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label">Category Name</label>
                                    <input type="text" wire:model="category"
                                        class="form-control @error('category') is-invalid @enderror"
                                        placeholder="e.g. Groceries, Rent">
                                    @error('category')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4 d-flex justify-content-start justify-content-md-end mt-3 mt-md-0">
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <i class="bi bi-plus-circle me-1"></i> 
                                        <span wire:loading.remove>Add Category</span>
                                        <span wire:loading>Adding...</span>
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>

                    {{-- Horizontal Tables: Daily | Monthly --}}
                    <div class="row g-4">

                        {{-- Daily --}}
                        <div class="col-12 col-md-6">
                            <h3 class="h6 fw-semibold mb-3">Daily Expense Categories</h3>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Category Name</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expenseCategories->where('expense_type', 'daily') as $item)
                                            <tr wire:key="daily-{{ $item->id }}">
                                                <td>{{ $item->category }}</td>
                                                <td class="text-end">
                                                    <button wire:click="editCategory({{ $item->id }})" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button wire:click="deleteCategory({{ $item->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($expenseCategories->where('expense_type', 'daily')->isEmpty())
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-4">No daily categories added yet.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Monthly --}}
                        <div class="col-12 col-md-6">
                            <h3 class="h6 fw-semibold mb-3">Monthly Expense Categories</h3>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Category Name</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expenseCategories->where('expense_type', 'monthly') as $item)
                                            <tr wire:key="monthly-{{ $item->id }}">
                                                <td>{{ $item->category }}</td>
                                                <td class="text-end">
                                                    <button wire:click="editCategory({{ $item->id }})" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button wire:click="deleteCategory({{ $item->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($expenseCategories->where('expense_type', 'monthly')->isEmpty())
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-4">No monthly categories added yet.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div wire:ignore.self class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Expense Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeEditModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateCategory">
                        <input type="hidden" wire:model="editingId">

                        <div class="mb-3">
                            <label class="form-label">Category Type</label>
                            <select wire:model="expense_type" class="form-select">
                                <option value="daily">Daily</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" wire:model="category" class="form-control" placeholder="e.g. Groceries, Rent">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeEditModal">
                        Cancel
                    </button>
                    <button type="button" wire:click="updateCategory" class="btn btn-primary">
                        Update Category
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@script
<script>
    Livewire.on('confirm-delete', event => {
        const id = event.id;
        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the category permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirmDelete', { id: id });
            }
        });
    });

    Livewire.on('resetForm', () => {
        const form = document.getElementById('expenseForm');
        if (form) form.reset();
    });

    Livewire.on('openModal', () => {
        const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    });

    Livewire.on('closeModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
        if (modal) modal.hide();
        const form = document.querySelector('#editCategoryModal form');
        if (form) form.reset();
    });
</script>
@endscript
