<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-shop text-primary me-2"></i> Product Brand Management
            </h3>
            <p class="text-muted mb-0">Manage and organize your product brands efficiently</p>
        </div>
        <div>
            <button class="btn btn-primary" wire:click="createBrand">
                <i class="bi bi-plus-lg me-2"></i> Add Brand
            </button>
        </div>
    </div>

    <!-- Brand List Table -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-list-ul text-primary me-2"></i> Brand List
                        </h5>
                        <p class="text-muted small mb-0">View and manage all product brands</p>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Brand Name</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($brands->count() > 0)
                                    @foreach ($brands as $brand)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="fw-medium text-dark">{{ $loop->iteration }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ $brand->brand_name }}</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-link text-primary p-0 me-2" wire:click="editBrand({{ $brand->id }})">
                                                    <i class="bi bi-pencil fs-6"></i>
                                                </button>
                                                <button class="btn btn-link text-danger p-0" wire:click="confirmDelete({{ $brand->id }})">
                                                    <i class="bi bi-trash fs-6"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div class="alert alert-primary bg-opacity-10">
                                                <i class="bi bi-info-circle me-2"></i> No product brands found.
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Brand Modal -->
    <div wire:ignore.self class="modal fade" id="createBrandModal" tabindex="-1" aria-labelledby="createBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Add Brand
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveBrand">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Brand Name</label>
                            <input type="text" class="form-control" wire:model="brandName" placeholder="Enter brand name" required>
                            @error('brandName')
                                <span class="text-danger small">* {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i> Save Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div wire:ignore.self class="modal fade" id="editBrandModal" tabindex="-1" aria-labelledby="editBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Brand
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateBrand({{ $editBrandId }})">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Brand Name</label>
                            <input type="text" class="form-control" wire:model="editBrandName" placeholder="Enter brand name" required>
                            @error('editBrandName')
                                <span class="text-danger small">* {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i> Update Brand
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

    .form-control {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus {
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
</style>
@endpush

@push('scripts')
<script>
    window.addEventListener('confirm-delete', event => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.dispatch('confirmDelete');
                Swal.fire({
                    title: "Deleted!",
                    text: "Brand has been deleted.",
                    icon: "success"
                });
            }
        });
    });

    window.addEventListener('edit-brand', event => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('editBrandModal'));
            modal.show();
        }, 300);
    });

    window.addEventListener('create-brand-modal', event => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('createBrandModal'));
            modal.show();
        }, 200);
    });
</script>
@endpush
