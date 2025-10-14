<div>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <h4 class="card-title mb-2 mb-sm-0">Supplier List</h4>
                    <div class="card-tools">
                        <button class="btn btn-primary w-100 w-sm-auto" data-bs-toggle="modal"
                            data-bs-target="#createSupplierModal">
                            <i class="bi bi-plus-circle me-1"></i> Create Supplier
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Supplier Name</th>
                                <th>Business Name</th>
                                <th>Contact Number</th>
                                <th>Email</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td class="text-center">John Doe</td>
                                <td class="text-center">Global Tech Supplies</td>
                                <td class="text-center">123-456-7890</td>
                                <td class="text-center">john.doe@globaltech.com</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editSupplierModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete()">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td class="text-center">Jane Smith</td>
                                <td class="text-center">Innovate Solutions Ltd.</td>
                                <td class="text-center">987-654-3210</td>
                                <td class="text-center">jane.s@innovatesol.com</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editSupplierModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete()">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="createSupplierModalLabel">Create Supplier</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="supplierName" class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="supplierName" placeholder="Enter supplier name">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="businessName" class="form-label">Business Name</label>
                            <input type="text" class="form-control" id="businessName" placeholder="Enter Business Name">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contactNumber" placeholder="Enter contact number">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email">
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-column flex-sm-row">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto mb-2 mb-sm-0"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100 w-sm-auto">Add Supplier</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editSupplierModalLabel">Edit Supplier</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="editName" class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="editName" value="John Doe">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editBusinessName" class="form-label">Business Name</label>
                            <input type="text" class="form-control" id="editBusinessName" value="Global Tech Supplies">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="editContactNumber" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="editContactNumber" value="123-456-7890">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" value="john.doe@globaltech.com">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Update Supplier</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // This function can be called by the delete buttons
    function confirmDelete() {
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
                // Here you would typically make an API call to delete the item
                // For example: fetch('/api/suppliers/1', { method: 'DELETE' });
                
                Swal.fire({
                    title: "Deleted!",
                    text: "Supplier has been deleted.",
                    icon: "success"
                });
            }
        });
    }
</script>
<script>
    // This script is useful if you need to load data into the edit modal dynamically
    // For now, it just ensures the Bootstrap modal functionality works correctly.
    window.addEventListener('open-edit-modal', event => {
        const modal = new bootstrap.Modal(document.getElementById('editSupplierModal'));
        modal.show();
    });
</script>
@endpush

@push('styles')
<style>
    /* Improve table display on mobile */
    @media (max-width: 767.98px) {

        .table td,
        .table th {
            font-size: 0.85rem;
            padding: 0.5rem;
        }

        /* Ensure action buttons are properly sized on small screens */
        .btn-sm {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }

        /* Hide less important columns on very small screens */
        @media (max-width: 575.98px) {
            .table td:nth-child(5),
            .table th:nth-child(5) { /* Hides the Email column */
                display: none;
            }
        }
    }

    /* Better form handling on small screens */
    @media (max-width: 575.98px) {
        .modal-footer {
            justify-content: center;
            padding-top: 1rem;
        }

        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        .modal-body {
            padding: 1rem;
        }
    }
</style>
@endpush