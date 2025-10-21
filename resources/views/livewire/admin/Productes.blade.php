<div>
    @push('styles')
    <style>
        /* Tab navigation styles */
        .content-tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .content-tab {
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 500;
            color: #495057;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .content-tab.active {
            color: #4361ee;
            border-bottom-color: #4361ee;
        }

        .content-tab:hover:not(.active) {
            color: #4361ee;
            border-bottom-color: #dee2e6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Modern card styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem 1.5rem;
        }

        /* Modern header styling */
        .inventory-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            width: 300px;
        }

        /* Modern table styling */
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

        /* Modern action buttons */
        .action-btns {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .action-btn.edit {
            color: #4361ee;
            background-color: rgba(67, 97, 238, 0.1);
        }

        .action-btn.delete {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }

        .action-btn:hover.edit {
            background-color: rgba(67, 97, 238, 0.2);
        }

        .action-btn:hover.delete {
            background-color: rgba(220, 53, 69, 0.2);
        }

        /* Modern modal styling */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-radius: 12px 12px 0 0;
        }

        /* Modern form styling */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            border-color: #4361ee;
        }

        /* Modern button styling */
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

        .btn-success {
            background-color: #4cc9f0;
            border-color: #4cc9f0;
        }

        .btn-success:hover {
            background-color: #3db8e0;
            border-color: #3db8e0;
            transform: translateY(-2px);
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .content-tabs {
                flex-direction: column;
                border-bottom: none;
            }

            .content-tab {
                text-align: center;
                border: 1px solid #dee2e6;
                border-radius: 4px;
                margin-bottom: 8px;
                padding: 12px 15px;
            }

            .content-tab.active {
                background-color: #4361ee;
                color: white;
                border-color: #4361ee;
            }

            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-item {
                margin-bottom: 0.25rem;
            }

            h2,
            .h2 {
                font-size: 1.5rem;
            }

            h5,
            .h5 {
                font-size: 1.1rem;
            }

            .fs-5 {
                font-size: 1rem !important;
            }

            .card-title {
                margin-bottom: 0.5rem;
            }

            .form-select,
            .form-control {
                font-size: 0.95rem;
                padding: 0.375rem 0.5rem;
            }

            .badge {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 767.98px) {
            .modal-body .row .col-md-4.border-end {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }

            .accordion-button {
                padding: 0.75rem;
            }

            .accordion-body {
                padding: 0.75rem;
            }

            .table-borderless th {
                width: auto !important;
                min-width: 120px;
            }
        }
    </style>
    @endpush

    <div class="container-fluid p-3">

        <!-- Products Content -->
        <div id="products" class="tab-content active">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h3 class="fw-bold text-dark mb-2">
                        <i class="bi bi-box-seam text-primary me-2"></i> Product Inventory Management
                    </h3>
                    <p class="text-muted mb-0">Manage your product catalog and inventory levels efficiently</p>
                </div>
            </div>


                
            <!-- Search and Actions -->
<div class="inventory-header w-100 mb-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 gap-3">
        
        <!-- 🔍 Search Bar -->
        <div class="search-bar flex-grow-1">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input 
                    type="text" 
                    class="form-control border-start-0" 
                    id="product-search" 
                    wire:model.live="search"
                    placeholder="Search Products..."
                >
            </div>
        </div>

        <!-- ➕ Add Button -->
        <div class="d-flex justify-content-md-end">
            <button class="btn btn-primary add-product-btn" wire:click="openCreateModal">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-sm-inline">Add Product</span>
            </button>
        </div>

    </div>
</div>


            <!-- Products Table -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-list-ul text-primary me-2"></i> Products List
                                </h5>
                                <p class="text-muted small mb-0">View and manage all products in your inventory</p>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Product Name</th>
                                            <th>Code</th>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Stock</th>
                                            <th>Supplier Price</th>
                                            <th>Selling Price</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($products->count() > 0)
                                        @foreach ($products as $product)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="fw-medium text-dark">{{ $loop->iteration }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ $product->product_name }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ $product->code }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ $product->brand }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ $product->model }}</span>
                                            </td>
                                            <td>
                                                @if ($product->available_stock > 0)
                                                <span class="badge bg-success">In Stock</span>
                                                @else
                                                <span class="badge bg-danger">Out of Stock</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">${{ number_format($product->supplier_price, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">${{ number_format($product->selling_price, 2) }}</span>
                                            </td>
                                            <td>
                                                @if ($product->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                                @else
                                                <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="action-btns">
                                                    <button class="btn action-btn edit" title="Edit"
                                                        wire:click="editProduct({{ $product->id }})"
                                                        wire:loading.attr="disabled">
                                                        <i class="bi bi-pencil" wire:loading.class="d-none"
                                                            wire:target="editProduct({{ $product->id }})"></i>
                                                        <span wire:loading wire:target="editProduct({{ $product->id }})">
                                                            <i class="spinner-border spinner-border-sm"></i>
                                                        </span>
                                                    </button>
                                                    <button class="btn action-btn delete" title="Delete"
                                                        wire:click="confirmDeleteProduct({{ $product->id }})"
                                                        wire:loading.attr="disabled">
                                                        <i class="bi bi-trash" wire:loading.class="d-none"
                                                            wire:target="confirmDeleteProduct({{ $product->id }})"></i>
                                                        <span wire:loading wire:target="confirmDeleteProduct({{ $product->id }})">
                                                            <i class="spinner-border spinner-border-sm"></i>
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="alert alert-primary bg-opacity-10">
                                                    <i class="bi bi-info-circle me-2"></i> No products found.
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-center">
                                    {{ $products->links('livewire.custom-pagination') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Product Modal -->
            <div wire:ignore.self class="modal fade" id="createProductModal" tabindex="-1"
                aria-labelledby="createProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-plus-circle text-primary me-2"></i> Create New Product
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Basic Information Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-info-circle text-primary me-2"></i> Basic Information
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="code" class="form-label fw-semibold">Code:</label>
                                                <input type="text" class="form-control" id="code" wire:model="code">
                                                @error('code')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="name" class="form-label fw-semibold">Name:</label>
                                                <input type="text" class="form-control" id="name" wire:model="name">
                                                @error('name')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="model" class="form-label fw-semibold">Model:</label>
                                                <input type="text" class="form-control" id="model" wire:model="model">
                                                @error('model')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Classification Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-diagram-3 text-primary me-2"></i> Classification
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="brand" class="form-label fw-semibold">Brand:</label>
                                                <select class="form-select" id="brand" wire:model="brand">
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('brand')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="category" class="form-label fw-semibold">Category:</label>
                                                <select class="form-select" id="category" wire:model="category">
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="supplier" class="form-label fw-semibold">Supplier:</label>
                                                <select class="form-select" id="supplier" wire:model="supplier">
                                                    <option value="">Select Supplier</option>
                                                    @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('supplier')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Information Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-card-text text-primary me-2"></i> Product Information
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="image" class="form-label fw-semibold">Image:</label>
                                                <input type="text" class="form-control" id="image" wire:model="image">
                                                @error('image')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="barcode" class="form-label fw-semibold">Barcode:</label>
                                                <input type="text" class="form-control" id="barcode" wire:model="barcode">
                                                @error('barcode')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label fw-semibold">Description:</label>
                                                <textarea class="form-control" id="description" rows="3" wire:model="description"></textarea>
                                                @error('description')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing and Inventory Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-cash text-primary me-2"></i> Pricing and Inventory
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="supplier_price" class="form-label fw-semibold">Supplier Price:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="supplier_price" wire:model="supplier_price">
                                                </div>
                                                @error('supplier_price')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="selling_price" class="form-label fw-semibold">Selling Price:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="selling_price" wire:model="selling_price">
                                                </div>
                                                @error('selling_price')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="discount_price" class="form-label fw-semibold">Discount Price:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="discount_price" wire:model="discount_price">
                                                </div>
                                                @error('discount_price')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="available_stock" class="form-label fw-semibold">Available Stock:</label>
                                                <input type="number" class="form-control" id="available_stock" wire:model="available_stock">
                                                @error('available_stock')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="damage_stock" class="form-label fw-semibold">Damage Stock:</label>
                                                <input type="number" class="form-control" id="damage_stock" wire:model="damage_stock">
                                                @error('damage_stock')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" wire:click="createProduct">Save Product</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div wire:ignore.self wire:key="edit-modal-{{ $editId ?? 'new' }}" class="modal fade" id="editProductModal" tabindex="-1"
                aria-labelledby="editProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-pencil-square text-primary me-2"></i> Edit Product
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Basic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-info-circle text-primary me-2"></i> Basic Information
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editCode" class="form-label fw-semibold">Code:</label>
                                                <input type="text" class="form-control" id="editCode" wire:model="editCode">
                                                @error('editCode')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editName" class="form-label fw-semibold">Name:</label>
                                                <input type="text" class="form-control" id="editName" wire:model="editName">
                                                @error('editName')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editBrand" class="form-label fw-semibold">Brand:</label>
                                                <select class="form-select" id="editBrand" wire:model="editBrand">
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('editBrand')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editModel" class="form-label fw-semibold">Model:</label>
                                                <input type="text" class="form-control" id="editModel" wire:model="editModel">
                                                @error('editModel')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Classification -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-diagram-3 text-primary me-2"></i> Classification
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editCategory" class="form-label fw-semibold">Category:</label>
                                                <select class="form-select" id="editCategory" wire:model="editCategory">
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('editCategory')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-card-text text-primary me-2"></i> Product Information
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editImage" class="form-label fw-semibold">Image:</label>
                                                <input type="text" class="form-control" id="editImage" wire:model="editImage">
                                                @error('editImage')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editBarcode" class="form-label fw-semibold">Barcode:</label>
                                                <input type="text" class="form-control" id="editBarcode" wire:model="editBarcode">
                                                @error('editBarcode')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="editDescription" class="form-label fw-semibold">Description:</label>
                                                <textarea class="form-control" id="editDescription" rows="3" wire:model="editDescription"></textarea>
                                                @error('editDescription')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing and Inventory -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-cash text-primary me-2"></i> Pricing and Inventory
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editSupplierPrice" class="form-label fw-semibold">Supplier Price:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="editSupplierPrice" wire:model="editSupplierPrice">
                                                </div>
                                                @error('editSupplierPrice')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editSellingPrice" class="form-label fw-semibold">Selling Price:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="editSellingPrice" wire:model="editSellingPrice">
                                                </div>
                                                @error('editSellingPrice')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editDiscountPrice" class="form-label fw-semibold">Discount Price:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="editDiscountPrice" wire:model="editDiscountPrice">
                                                </div>
                                                @error('editDiscountPrice')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editDamageStock" class="form-label fw-semibold">Damage Stock:</label>
                                                <input type="number" class="form-control" id="editDamageStock" wire:model="editDamageStock">
                                                @error('editDamageStock')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editStatus" class="form-label fw-semibold">Status:</label>
                                                <select class="form-select" id="editStatus" wire:model="editStatus">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                    <option value="discontinued">Discontinued</option>
                                                </select>
                                                @error('editStatus')
                                                <span class="text-danger small">* {{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" wire:click="duplicateProduct({{ $editId }})">
                                <i class="bi bi-copy me-1"></i> Duplicate Product
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="updateProduct">Update Product</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.content-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
    @endpush
</div>