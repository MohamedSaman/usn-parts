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
            color: #0d6efd;
            border-bottom-color: #0d6efd;
        }

        .content-tab:hover:not(.active) {
            color: #0d6efd;
            border-bottom-color: #dee2e6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Inventory header styles */
        .inventory-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            width: 300px;
        }

        /* Add Product button */
        .add-product-btn {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Improved action buttons */
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
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .action-btn.delete {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }

        .action-btn:hover.edit {
            background-color: rgba(13, 110, 253, 0.2);
        }

        .action-btn:hover.delete {
            background-color: rgba(220, 53, 69, 0.2);
        }

        /* Update the tab navigation styles for better mobile experience */
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
                background-color: #0d6efd;
                color: white;
                border-color: #0d6efd;
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
        <!-- Navigation Tabs -->
        <div class="content-tabs">
            <div class="content-tab active" data-tab="products">Products</div>
        </div>

        <!-- Products Content -->
        <div id="products" class="tab-content active">
            <div class="inventory-header">
                <h2 class="mb-3">Product Inventory</h2>
                <div class="d-flex flex-column flex-md-row gap-3">
                    <div class="search-bar w-100">
                        <div class="input-group">
                            <input type="text" class="form-control" id="product-search" wire:model.live="search"
                                placeholder="Search Products...">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary add-product-btn" wire:click="openCreateModal">
                            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Add Product</span>
                        </button>
                    </div>
                </div>
            </div>

            <p class="text-muted mb-4">Manage your product catalog and inventory levels</p>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">Product Name</th>
                            <th scope="col" class="text-center">Code</th>
                            <th scope="col" class="text-center">Brand</th>
                            <th scope="col" class="text-center">Model</th>
                            <th scope="col" class="text-center">Stock</th>
                            <th scope="col" class="text-center">Supplier Price</th>
                            <th scope="col" class="text-center">Selling Price</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody wire:key="products-{{ now() }}">
                        @if ($products->count() > 0)
                        @foreach ($products as $product)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $product->product_name }}</td>
                            <td class="text-center">{{ $product->code }}</td>
                            <td class="text-center">{{ $product->brand }}</td>
                            <td class="text-center">{{ $product->model }}</td>
                            <td class="text-center">
                                @if ($product->available_stock > 0)
                                <span class="badge bg-success rounded-pill">In Stock</span>
                                @else
                                <span class="badge bg-danger rounded-pill">Out of Stock</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($product->supplier_price, 2) }}</td>
                            <td class="text-center">{{ number_format($product->selling_price, 2) }}</td>
                            <td class="text-center">
                                @if ($product->status == 'active')
                                <span class="badge bg-success rounded-pill">Active</span>
                                @else
                                <span class="badge bg-danger rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
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
                            <td colspan="10" class="text-center">
                                <div class="alert alert-primary bg-opacity-10 my-2">
                                    <i class="bi bi-info-circle me-2"></i> No products found.
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $products->links('livewire.custom-pagination') }}
                </div>
            </div>

            <!-- Create Product Modal -->
            <div wire:ignore.self class="modal fade" id="createProductModal" tabindex="-1"
                aria-labelledby="createProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h1 class="modal-title fs-5 text-white" id="createProductModalLabel">Create New Product</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body bg-light p-4">
                            <!-- Basic Information Card -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Basic Information</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="name" class="form-label fw-bold">Name:</label>
                                                <input type="text" class="form-control" id="name" wire:model="name">
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="model" class="form-label fw-bold">Model:</label>
                                                <input type="text" class="form-control" id="model" wire:model="model">
                                                @error('model')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="status" class="form-label fw-bold">Status:</label>
                                                <select class="form-select" id="status" wire:model="status">
                                                    <option value="">Select Status</option>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                                @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Classification Card -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Classification</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="brand" class="form-label fw-bold">Brand:</label>
                                                <select class="form-select" id="brand" wire:model="brand">
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('brand')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="category" class="form-label fw-bold">Category:</label>
                                                <select class="form-select" id="category" wire:model="category">
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="supplier" class="form-label fw-bold">Supplier:</label>
                                                <select class="form-select" id="supplier" wire:model="supplier">
                                                    <option value="">Select Supplier</option>
                                                    @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('supplier')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Information Card -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Product Information</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="image" class="form-label fw-bold">Product Image:</label>
                                                <input type="file" class="form-control" id="image" wire:model="image" accept="image/*">
                                                <div wire:loading wire:target="image">Uploading...</div>
                                                @if ($image)
                                                <div class="mt-2">
                                                    <img src="{{ $image->temporaryUrl() }}" class="mt-2 img-thumbnail" style="height: 100px">
                                                </div>
                                                @endif
                                                @error('image')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="barcode" class="form-label fw-bold">Barcode:</label>
                                                <input type="text" class="form-control" id="barcode" wire:model="barcode">
                                                @error('barcode')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label fw-bold">Description:</label>
                                                <textarea class="form-control" id="description" rows="3" wire:model="description"></textarea>
                                                @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing and Inventory Card -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Pricing and Inventory</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="supplier_price" class="form-label fw-bold">Supplier Price:</label>
                                                <input type="number" step="0.01" class="form-control" id="supplier_price" wire:model="supplier_price">
                                                @error('supplier_price')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="selling_price" class="form-label fw-bold">Selling Price:</label>
                                                <input type="number" step="0.01" class="form-control" id="selling_price" wire:model="selling_price">
                                                @error('selling_price')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="discount_price" class="form-label fw-bold">Discount Price:</label>
                                                <input type="number" step="0.01" class="form-control" id="discount_price" wire:model="discount_price">
                                                @error('discount_price')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="available_stock" class="form-label fw-bold">Available Stock:</label>
                                                <input type="number" class="form-control" id="available_stock" wire:model="available_stock">
                                                @error('available_stock')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="damage_stock" class="form-label fw-bold">Damage Stock:</label>
                                                <input type="number" class="form-control" id="damage_stock" wire:model="damage_stock">
                                                @error('damage_stock')
                                                <span class="text-danger">{{ $message }}</span>
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
                        <div class="modal-header bg-primary">
                            <h1 class="modal-title fs-5 text-white" id="editProductModalLabel">Edit Product</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body bg-light p-4">
                            <!-- Basic Information -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Basic Information</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editCode" class="form-label fw-bold">Code:</label>
                                                <input type="text" class="form-control" id="editCode" wire:model="editCode">
                                                @error('editCode')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editName" class="form-label fw-bold">Name:</label>
                                                <input type="text" class="form-control" id="editName" wire:model="editName">
                                                @error('editName')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editBrand" class="form-label fw-bold">Brand:</label>
                                                <select class="form-select" id="editBrand" wire:model="editBrand">
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('editBrand')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editModel" class="form-label fw-bold">Model:</label>
                                                <input type="text" class="form-control" id="editModel" wire:model="editModel">
                                                @error('editModel')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Classification -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Classification</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editCategory" class="form-label fw-bold">Category:</label>
                                                <select class="form-select" id="editCategory" wire:model="editCategory">
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('editCategory')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Information -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Product Information</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editImage" class="form-label fw-bold">Image:</label>
                                                <input type="file" class="form-control" id="editImage" wire:model="editImage" accept="image/*">
                                                <div wire:loading wire:target="editImage">Uploading...</div>
                                                <div class="mt-2">
                                                    @if ($editImage)
                                                    <div class="mb-2">New image preview:</div>
                                                    <img src="{{ $editImage->temporaryUrl() }}" class="img-thumbnail" style="height: 100px">
                                                    @elseif ($existingImage)
                                                    <div class="mb-2">Current image:</div>
                                                    <img src="{{ asset('storage/' . $existingImage) }}" class="img-thumbnail" style="height: 100px">
                                                    @endif
                                                </div>
                                                <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                                                @error('editImage')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editBarcode" class="form-label fw-bold">Barcode:</label>
                                                <input type="text" class="form-control" id="editBarcode" wire:model="editBarcode">
                                                @error('editBarcode')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="editDescription" class="form-label fw-bold">Description:</label>
                                                <textarea class="form-control" id="editDescription" rows="3" wire:model="editDescription"></textarea>
                                                @error('editDescription')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing and Inventory -->
                            <div class="card mb-4 shadow border border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h5 class="card-title mb-0 text-primary">Pricing and Inventory</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editSupplierPrice" class="form-label fw-bold">Supplier Price:</label>
                                                <input type="number" step="0.01" class="form-control" id="editSupplierPrice" wire:model="editSupplierPrice">
                                                @error('editSupplierPrice')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editSellingPrice" class="form-label fw-bold">Selling Price:</label>
                                                <input type="number" step="0.01" class="form-control" id="editSellingPrice" wire:model="editSellingPrice">
                                                @error('editSellingPrice')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editDiscountPrice" class="form-label fw-bold">Discount Price:</label>
                                                <input type="number" step="0.01" class="form-control" id="editDiscountPrice" wire:model="editDiscountPrice">
                                                @error('editDiscountPrice')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editDamageStock" class="form-label fw-bold">Damage Stock:</label>
                                                <input type="number" class="form-control" id="editDamageStock" wire:model="editDamageStock">
                                                @error('editDamageStock')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="editStatus" class="form-label fw-bold">Status:</label>
                                                <select class="form-select" id="editStatus" wire:model="editStatus">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                    <option value="discontinued">Discontinued</option>
                                                </select>
                                                @error('editStatus')
                                                <span class="text-danger">{{ $message }}</span>
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
                                <i class="bi bi-copy"></i> Duplicate Product
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