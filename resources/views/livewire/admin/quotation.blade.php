<div>
    <div class="container-fluid p-4">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm stat-card border-primary">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted text-uppercase">Pending Purchase Orders</h6>
                            <h2 class="card-title fw-bold">7</h2>
                        </div>
                        <div class="fs-1 text-primary opacity-50"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm stat-card border-success">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted text-uppercase">Completed Orders</h6>
                            <h2 class="card-title fw-bold">42</h2>
                        </div>
                        <div class="fs-1 text-success opacity-50"><i class="bi bi-patch-check-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light p-3">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                    <div>
                        <h4 class="card-title mb-1">Purchase Orders</h4>
                        <p class="text-muted mb-0 d-none d-sm-block">Manage all orders sent to suppliers.</p>
                    </div>
                    <div class="d-flex align-items-center mt-2 mt-sm-0">
                        <input type="text" class="form-control me-2" placeholder="Search PO # or supplier...">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseOrderModal">
                            <i class="bi bi-plus-lg me-1"></i> New Purchase Order
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order No</th>
                                <th>Supplier</th>
                                <th>Created By</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ORD-2025-051</td>
                                <td>Global Tech Supplies</td>
                                <td>Jane Smith</td>
                        
                                <td><span class="badge bg-success-subtle text-success-emphasis text-capitalize">Received</span></td>
                                <td>2025-10-12</td>
                                <td><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-right"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>ORD-2025-052</td>
                                <td>Innovate Solutions Ltd.</td>
                                <td>Peter Jones</td>
                                <td><span class="badge bg-warning-subtle text-warning-emphasis text-capitalize">Pending</span></td>
                                <td>2025-10-13</td>
                                <td><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-right"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#PO-2025-050</td>
                                <td>Office Essentials Co.</td>
                                <td>Jane Smith</td>
                                
                                <td><span class="badge bg-success-subtle text-success-emphasis text-capitalize">Received</span></td>
                                <td>2025-10-10</td>
                                <td><a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-right"></i></a>
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
                      <li class="page-item"><a class="page-link" href="#">3</a></li>
                      <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addPurchaseOrderModal" tabindex="-1" aria-labelledby="addPurchaseOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPurchaseOrderModalLabel">Create New Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="supplierSelect" class="form-label">Select Supplier</label>
                            <select class="form-select" id="supplierSelect">
                                <option value="">Choose a supplier...</option>
                                <option value="1">Global Tech Supplies</option>
                                <option value="2">Innovate Solutions Ltd.</option>
                                <option value="3">Office Essentials Co.</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="productSearch" class="form-label">Search Product</label>
                            <input class="form-control" list="productListOptions" id="productSearch" placeholder="Type to search for a product...">
                            <datalist id="productListOptions">
                                <option value="Laptop - Model X"></option>
                                <option value="Wireless Mouse"></option>
                                <option value="Mechanical Keyboard"></option>
                            </datalist>
                        </div>
                    </div>

                    <div class="row align-items-end g-3 mb-4 p-3 bg-light rounded border">
                        <div class="col-md-6">
                            <label class="form-label">Selected Product</label>
                            <input type="text" class="form-control" value="Laptop - Model X" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle me-1"></i> Add to PO
                            </button>
                        </div>
                    </div>

                    <h5 class="mt-4">Order Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th>Product Name</th>
                                    <th class="text-center" style="width: 15%;">Quantity</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Wireless Mouse</td>
                                    <td class="text-center">50</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Mechanical Keyboard</td>
                                    <td class="text-center">25</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>