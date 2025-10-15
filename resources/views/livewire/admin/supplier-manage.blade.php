<div>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <h4 class="card-title mb-2 mb-sm-0">Supplier List</h4>
                    <div class="card-tools">
                        <button class="btn btn-primary w-100 w-sm-auto" wire:click="createSupplier">
                            <i class="bi bi-plus-circle me-1"></i> Create Supplier
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Supplier Name</th>
                                <th>Business Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $supplier->name }}</td>
                                <td class="text-center">{{ $supplier->businessname }}</td>
                                <td class="text-center">{{ $supplier->contact }}</td>
                                <td class="text-center">{{ $supplier->email }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-primary" wire:click="edit({{ $supplier->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>


                                        <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $supplier->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>



                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No suppliers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Supplier Modal -->
    <div wire:ignore.self class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="createSupplierModalLabel">Create Supplier</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" wire:model.defer="name" placeholder="Enter supplier name">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Business Name</label>
                                <input type="text" class="form-control" wire:model.defer="businessname" placeholder="Enter business name">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" wire:model.defer="contact" placeholder="Enter contact number">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model.defer="email" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" wire:model.defer="address" rows="2" placeholder="Enter address"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" wire:model.defer="notes" rows="2" placeholder="Additional notes (optional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer flex-column flex-sm-row">
                        <button type="button" class="btn btn-secondary w-100 w-sm-auto mb-2 mb-sm-0" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div wire:ignore.self class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="updateSupplier">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Edit Supplier</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Business Name</label>
                                <input type="text" class="form-control" wire:model="businessname">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" wire:model="contact">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="email">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" wire:model="address" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer flex-column flex-sm-row">
                        <button type="button" class="btn btn-secondary w-100 w-sm-auto mb-2 mb-sm-0" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Update Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>

@push('scripts')
<script>
    // Open modal when Livewire dispatches event
    window.addEventListener('open-modal', event => {
        const modalId = event.detail.modal;
        const el = document.getElementById(modalId);
        if (!el) return;

        // Longer delay to ensure Livewire updates input values
        setTimeout(() => {
            const modal = new bootstrap.Modal(el);
            modal.show();
        }, 200);
    });


    // Close modal
    window.addEventListener('close-modal', event => {
        const modalId = event.detail.modal;
        const el = document.getElementById(modalId);
        if (!el) return;

        const modal = bootstrap.Modal.getInstance(el);
        if (modal) modal.hide();
    });

    // Reset form when modals are closed manually
    document.addEventListener('DOMContentLoaded', function() {
        const createModal = document.getElementById('createSupplierModal');
        const editModal = document.getElementById('editSupplierModal');

        if (createModal) {
            createModal.addEventListener('hidden.bs.modal', function() {
                Livewire.find('{{ $this->getId() }}').call('resetForm');
            });
        }

        if (editModal) {
            editModal.addEventListener('hidden.bs.modal', function() {
                Livewire.find('{{ $this->getId() }}').call('resetForm');
            });
        }
    });

    // Global SweetAlert
    window.addEventListener('swal', event => {
        const detail = event.detail || {};
        Swal.fire(detail.title || '', detail.text || '', detail.icon || 'info');
    });

    // Delete confirmation
    window.addEventListener('swal-delete', event => {
        const id = event.detail.id;
        Swal.fire({
            title: "Are you sure?",
            text: "This will permanently delete the supplier.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.find('{{ $this->getId() }}').call('delete', id);
            }
        });
    });
</script>

@endpush