<div>
    <div class="container-fluid p-4">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm stat-card border-warning">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted text-uppercase">Awaiting Receipt</h6>
                            <h2 class="card-title fw-bold">3</h2>
                        </div>
                        <div class="fs-1 text-warning opacity-50"><i class="bi bi-box-seam"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm stat-card border-success">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted text-uppercase">Fully Received Orders</h6>
                            <h2 class="card-title fw-bold">18</h2>
                        </div>
                        <div class="fs-1 text-success opacity-50"><i class="bi bi-archive-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light p-3">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                    <div>
                        <h4 class="card-title mb-1">Goods Received Notes</h4>
                        <p class="text-muted mb-0 d-none d-sm-block">Process incoming orders from suppliers.</p>
                    </div>
                    <div class="d-flex align-items-center mt-2 mt-sm-0">
                        <input type="text" class="form-control me-2" placeholder="Search PO # or supplier...">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ORD-2025-051</td>
                                <td>Global Tech Supplies</td>
                                <td>2025-10-12</td>
                                <td><span class="badge bg-success-subtle text-success-emphasis">PO Complete</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#grnModal"
                                        data-po-id="ORD-2025-051"
                                        data-supplier="Global Tech Supplies"
                                        data-products='[
                                            {"name": "Wireless Mouse", "qty": 50},
                                            {"name": "Mechanical Keyboard", "qty": 25}
                                        ]'>
                                        <i class="bi bi-check-lg me-1"></i> Process GRN
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>ORD-2025-050</td>
                                <td>Office Essentials Co.</td>
                                <td>2025-10-10</td>
                                <td><span class="badge bg-success-subtle text-success-emphasis">PO Complete</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#grnModal"
                                        data-po-id="ORD-2025-050"
                                        data-supplier="Office Essentials Co."
                                        data-products='[
                                            {"name": "A4 Paper Ream (Box of 5)", "qty": 100},
                                            {"name": "Stapler - Heavy Duty", "qty": 20},
                                            {"name": "Pen Box (Blue)", "qty": 40}
                                        ]'>
                                        <i class="bi bi-check-lg me-1"></i> Process GRN
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>ORD-2025-049</td>
                                <td>Innovate Solutions Ltd.</td>
                                <td>2025-10-09</td>
                                <td><span class="badge bg-info-subtle text-info-emphasis">Partially Received</span></td>
                                <td class="text-center">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination justify-content-end">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="grnModal" tabindex="-1" aria-labelledby="grnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="grnModalLabel">Create Goods Received Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 mb-4 rounded border bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>PO Number:</strong> <span id="modalPoId"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Supplier:</strong> <span id="modalSupplier"></span></p>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4">Received Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%;">Product</th>
                                    <th class="text-center" style="width: 15%;">Ordered Qty</th>
                                    <th class="text-center" style="width: 15%;">Received Qty</th>
                                    <th style="width: 20%;">Supplier Price</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="grnProductTableBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 border rounded bg-light">
                        <h5 class="mb-3">Add Mismatched / Unplanned Item</h5>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" placeholder="Enter product name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success w-100"><i class="bi bi-plus-circle me-1"></i> Add</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save GRN</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Get the modal element
    const grnModal = document.getElementById('grnModal');

    // Add an event listener for when the modal is about to be shown
    grnModal.addEventListener('show.bs.modal', event => {
        // Get the button that triggered the modal
        const button = event.relatedTarget;

        // Extract info from data-* attributes
        const poId = button.getAttribute('data-po-id');
        const supplier = button.getAttribute('data-supplier');
        // Parse the JSON string from the data-products attribute
        const products = JSON.parse(button.getAttribute('data-products'));

        // Update the modal's static content
        const modalTitle = grnModal.querySelector('.modal-title');
        const modalPoIdSpan = grnModal.querySelector('#modalPoId');
        const modalSupplierSpan = grnModal.querySelector('#modalSupplier');

        modalTitle.textContent = `Create GRN for Purchase Order: ${poId}`;
        modalPoIdSpan.textContent = poId;
        modalSupplierSpan.textContent = supplier;

        // Get the table body to populate with product rows
        const tableBody = grnModal.querySelector('#grnProductTableBody');
        // Clear out any old rows from previous modals
        tableBody.innerHTML = '';

        // Loop through the products and create a table row for each
        products.forEach(product => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${product.name}</td>
                <td class="text-center">${product.qty}</td>
                <td><input type="number" class="form-control text-center" value="${product.qty}" min="0"></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" placeholder="0.00">
                    </div>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-warning" title="Report Mismatch">
                        <i class="bi bi-exclamation-triangle"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    });
</script>
@endpush