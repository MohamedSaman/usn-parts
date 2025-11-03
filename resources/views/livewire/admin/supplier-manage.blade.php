<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-people-fill text-success me-2"></i> Supplier Management
            </h3>
            <p class="text-muted mb-0">Manage your suppliers and business contacts efficiently</p>
        </div>
        <div>
            <button class="btn btn-primary" wire:click="createSupplier">
                <i class="bi bi-plus-circle me-2"></i> Add Supplier
            </button>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-list-ul text-primary me-2"></i> Supplier List
                    </h5>
                    <p class="text-muted small mb-0">View and manage all registered suppliers</p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Supplier Name</th>
                                <th>Business Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $supplier->name }}</span>
                                </td>
                                <td>{{ $supplier->businessname }}</td>
                                <td>{{ $supplier->contact }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td class="text-end pe-4">
                                    <!-- View Button -->
                                    <button class="text-info me-2 bg-opacity-0 border-0" wire:click="view({{ $supplier->id }})" 
                                            title="View Supplier Details">
                                        <i class="bi bi-eye fs-6"></i>
                                    </button>
                                    <!-- Edit Button -->
                                    <button class="text-primary me-2 bg-opacity-0 border-0" wire:click="edit({{ $supplier->id }})"
                                            title="Edit Supplier">
                                        <i class="bi bi-pencil fs-6"></i>
                                    </button>
                                    <!-- Delete Button -->
                                    <button class="text-danger me-2 bg-opacity-0 border-0" wire:click="confirmDelete({{ $supplier->id }})"
                                            title="Delete Supplier">
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No suppliers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Supplier Modal -->
    @if($showCreateModal)
    <div class="modal fade show d-block" tabindex="-1" aria-labelledby="createSupplierModalLabel" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-plus-circle text-white me-2"></i> Create Supplier
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Enter supplier name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Business Name</label>
                                <input type="text" class="form-control" wire:model="businessname" placeholder="Enter business name">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Contact Number</label>
                                <input type="text" class="form-control" wire:model="contact" placeholder="Enter contact number">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="Enter email">
                                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" class="form-control" wire:model="phone" placeholder="Enter phone number">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea class="form-control" wire:model="address" rows="2" placeholder="Enter address"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" wire:model="notes" rows="2" placeholder="Additional notes (optional)"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i> Save Supplier
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- View Supplier Modal -->
    @if($showViewModal)
    <div class="modal fade show d-block" tabindex="-1" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-eye text-white me-2"></i> Supplier Details
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-muted">Supplier Name</label>
                            <p class="form-control-plaintext fw-medium">{{ $name }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-muted">Business Name</label>
                            <p class="form-control-plaintext">{{ $businessname ?: 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-muted">Contact Number</label>
                            <p class="form-control-plaintext">{{ $contact ?: 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-muted">Email</label>
                            <p class="form-control-plaintext">{{ $email ?: 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-muted">Phone</label>
                            <p class="form-control-plaintext">{{ $phone ?: 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-muted">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Address</label>
                        <p class="form-control-plaintext">{{ $address ?: 'N/A' }}</p>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Notes</label>
                        <p class="form-control-plaintext">{{ $notes ?: 'No notes available' }}</p>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="bi bi-x-circle me-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Supplier Modal -->
    @if($showEditModal)
    <div class="modal fade show d-block" tabindex="-1" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="updateSupplier">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil-square text-white me-2"></i> Edit Supplier
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Business Name</label>
                                <input type="text" class="form-control" wire:model="businessname">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Contact Number</label>
                                <input type="text" class="form-control" wire:model="contact">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" wire:model="email">
                                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" class="form-control" wire:model="phone">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea class="form-control" wire:model="address" rows="2"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i> Update Supplier
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>

@push('styles')
<style>
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
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }
    
    .form-control:focus, .form-select:focus {
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
    
    .form-control-plaintext {
        padding: 0.5rem 0;
        border: none;
        background: transparent;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Toast notifications
        Livewire.on('show-toast', ([type, message]) => {
            Swal.fire({
                title: type === 'success' ? 'Success!' : 'Error!',
                text: message,
                icon: type,
                timer: 2000,
                showConfirmButton: false
            });
        });
 Livewire.on('refreshPage', () => {
                setTimeout(() => {
                    window.location.reload();
                }, 1500); // Refresh after 1.5 seconds to show success message
            });
        // Delete confirmation
        Livewire.on('swal:confirm', ([data]) => {
            Swal.fire({
                title: data.title || 'Are you sure?',
                text: data.text || 'You won\'t be able to revert this!',
                icon: data.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete-supplier', { id: data.id });
                }
            });
        });
    });
</script>
@endpush