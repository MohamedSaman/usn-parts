<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-cash-stack text-primary me-2"></i> Billing System
            </h3>
            <p class="text-muted mb-0">Create and manage customer bills efficiently</p>
        </div>
        <div>
            <button class="btn btn-primary" wire:click="clearCart">
                <i class="bi bi-arrow-clockwise me-2"></i> Clear Cart
            </button>
        </div>
    </div>

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Search Section --}}
    <div class="card h-100 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-search text-primary me-2"></i> Search Products
                </h5>
                <p class="text-muted small mb-0">Search by code, model, barcode, brand or name</p>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" 
                               placeholder="Search by code, model, barcode, brand or name..."
                               wire:model.live="search" autocomplete="off">
                    </div>

<<<<<<< Updated upstream
                                <!-- Search Results Dropdown -->
                                @if ($search && count($searchResults) > 0)
                                <div class="search-results-container position-relative mt-1 w-100 bg-white shadow-lg rounded z-index-1000"
                                    style="max-height: 350px; overflow-y: auto;">
                                    @foreach ($searchResults as $result)
                                    <div class="search-result-item p-2 border-bottom"
                                        wire:key="result-{{ $result->id }}">
                                        <div class="d-flex align-items-stretch position-relative">
                                            <!-- Product Image - Full height -->
                                            <div class="product-image me-3" style="min-width: 60px;">
                                                @if ($result->image)
                                                <img src="{{ asset('storage/' . $result->image) }}"
                                                    alt="{{ $result->name }}" class="img-fluid rounded"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                <div class="no-image bg-light d-flex align-items-center justify-content-center rounded"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-Product text-muted"></i>
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Vertical divider -->
                                            <div class="vr mx-2 h-100"></div>

                                            <!-- Product Info - Middle section -->
                                            <div class="product-info flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold">
                                                        {{ $result->name ?? 'Unnamed Product' }}
                                                    </h6>
                                                    <div>
                                                        <span class="badge bg-success">Rs.{{ $result->selling_price ??
                                                            '-' }}</span>
                                                        <span class="badge bg-info">Available:
                                                            {{ $result->available_stock ?? 0 }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    <span class="me-2">Code: {{ $result->code }}</span> |
                                                    <span class="mx-2">Model: {{ $result->model }}</span>
                                                </div>
                                            </div>

                                            <!-- Vertical divider -->
                                            <div class="vr mx-2 h-100"></div>

                                            <!-- Product Action - Right side -->
                                            <div class="product-action d-flex align-items-center">
                                                <button class="btn btn-sm btn-primary"
                                                    wire:click="addToCart({{ $result->id }})" {{
                                                    $result->available_stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @elseif($search && count($searchResults) == 0)
                                <div
                                    class="search-results-container position-absolute mt-1 w-100 bg-white shadow-lg rounded">
                                    <div class="p-3 text-center text-muted">
                                        No Productes found matching "{{ $search }}"
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Cart Table -->
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Product</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Unit Price</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Quantity</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Discount (per unit)</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cart as $id => $item)
                                    <tr wire:key="cart-{{ $id }}">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    @if ($item['image'])
                                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                                        class="avatar avatar-sm me-3" alt="{{ $item['name'] }}">
                                                    @else
                                                    <div
                                                        class="avatar avatar-sm me-3 bg-gradient-secondary d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-Product text-lg text-white"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $item['code'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                Rs.{{ number_format($item['price'] ?: $item['price'], 2) }}
                                            </p>
                                        </td>
                                        <td>
                                            <!-- Improved quantity input with working buttons -->
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-primary btn-sm" type="button"
                                                    wire:click="updateQuantity({{ $id }}, {{ max(1, $quantities[$id] - 1) }})"
                                                    {{ $quantities[$id] <=1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center quantity-input"
                                                    data-Product-id="{{ $id }}" value="{{ $quantities[$id] }}" min="1"
                                                    max="{{ $item['inStock'] }}"
                                                    wire:change="validateQuantity({{ $id }})"
                                                    wire:model.live="quantities.{{ $id }}">
                                                <button class="btn btn-outline-primary btn-sm" type="button"
                                                    wire:click="updateQuantity({{ $id }}, {{ min($item['inStock'], $quantities[$id] + 1) }})"
                                                    {{ $quantities[$id]>= $item['inStock'] ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <!-- Improved validation message with better styling -->
                                            <div class="text-black mt-1 ml-2" style="font-size: 11px;">
                                                <i class="fas fa-info-circle" style="
                                                    margin-left: 18px;"></i> Max: {{ $item['inStock'] }} available
                                            </div>
                                            @if($quantities[$id] > $item['inStock'])
                                            <div class="text-danger mt-1" style="font-size: 12px;">
                                                <i class="fas fa-exclamation-triangle"></i> Exceeds available stock!
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control form-control-sm"
                                                    value="{{ $discounts[$id] ?? 0 }}" min="0"
                                                    max="{{ $item['price'] }}" step="0.01"
                                                    wire:change="updateDiscount({{ $id }}, $event.target.value)">
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                Rs.{{ number_format(($item['price'] ?: $item['price']) *
                                                $quantities[$id] - ($discounts[$id] ?? 0) * $quantities[$id], 2) }}
                                            </p>
                                        </td>
                                        <td>
                                            <button class="btn btn-link btn-sm text-info"
                                                wire:click="showDetail({{ $id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-link btn-sm text-danger"
                                                wire:click="removeFromCart({{ $id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                                <p>Your cart is empty. Search and add products to create a bill.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Customer & Payment Information -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <!-- Customer & Payment Information Card -->
                                <div class="card">
                                    <div class="card-header pb-0 bg-primary">
                                        <h6 class="text-white">Customer & Payment Information</h6>
                                    </div>
                                    <div class="card-body" style="height: 500px; overflow-y: auto;">
                                        <!-- Customer Selection - Only show if cart has items -->

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Select Customer</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="input-group flex-grow-1">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-person"></i>
                                                    </span>
                                                    <select class="form-select" wire:model.live="customerId">
                                                        <option value="">-- Select a customer --</option>
                                                        @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}">
                                                            {{ $customer->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button class="btn btn-primary d-flex align-items-center"
                                                    data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                                    <i class="bi bi-plus-circle me-1"></i>ADD
                                                </button>
                                            </div>
                                            @error('customerId')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <!-- Pay Amount Field - Show after customer selection -->

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pay Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-cash-coin"></i>
                                                </span>
                                                <input type="number" class="form-control" min="0" max="{{ $grandTotal }}"
                                                    step="0.01" wire:model.live="paymentAmount" placeholder="Enter payment amount">
                                            </div>
                                            <small class="text-muted">Maximum: Rs.{{ number_format($grandTotal, 2) }}</small>
                                        </div>

                                        <!-- Payment Method - Show only if pay amount > 0 -->
                                        @if ($customerId && $paymentAmount > 0)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Payment Method</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-credit-card"></i>
                                                </span>
                                                <select class="form-select" wire:model.live="paymentMethod">
                                                    <option value="">-- Select payment method --</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="cheque">Cheque</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Cheque File Upload - Show only if cheque is selected -->
                                        @if ($paymentMethod === 'cheque')
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Upload Cheque File</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-file-earmark-check"></i>
                                                </span>
                                                <input type="file" class="form-control" wire:model="chequeFile"
                                                    accept=".jpg,.jpeg,.png,.gif,.pdf">
                                            </div>
                                            @if ($chequeFilePreview)
                                            <div class="mt-2">
                                                <img src="{{ $chequeFilePreview }}" class="img-thumbnail" style="max-height: 100px">
                                            </div>
                                            @endif
                                        </div>
                                        @endif

                                        <!-- Payment Notes -->
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Notes</label>
                                            <textarea class="form-control" wire:model="paymentNotes" rows="3"
                                                placeholder="Add payment notes..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Order Summary -->
                                <div class="card mt-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Order Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span>Rs.{{ number_format($subtotal, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Discount:</span>
                                            <span>Rs.{{ number_format($totalDiscount, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Grand Total:</span>
                                            <span class="fw-bold">Rs.{{ number_format($grandTotal, 2) }}</span>
                                        </div>

                                        <div class="d-flex mt-4">
                                            <button class="btn btn-danger me-2" wire:click="clearCart">
                                                <i class="fas fa-times me-2"></i>Clear
                                            </button>
                                            <button class="btn btn-success flex-grow-1" wire:click="completeSale"
                                                {{ empty($cart) || !$customerId ? 'disabled' : '' }}>
                                                <i class="fas fa-check me-2"></i>Complete Sale
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
=======
                    <!-- Search Results Dropdown -->
                    @if ($search && count($searchResults) > 0)
                    <div class="search-results-container position-relative mt-1 w-100 bg-white shadow-lg rounded z-index-1000"
                         style="max-height: 350px; overflow-y: auto;">
                        @foreach ($searchResults as $result)
                        <div class="search-result-item p-2 border-bottom"
                             wire:key="result-{{ $result->id }}">
                            <div class="d-flex align-items-stretch position-relative">
                                <!-- Product Image - Full height -->
                                <div class="product-image me-3" style="min-width: 60px;">
                                    @if ($result->image)
                                    <img src="{{ asset('storage/' . $result->image) }}"
                                         alt="{{ $result->name }}" class="img-fluid rounded"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                    <div class="no-image bg-light d-flex align-items-center justify-content-center rounded"
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-Product text-muted"></i>
                                    </div>
                                    @endif
                                </div>

                                <!-- Vertical divider -->
                                <div class="vr mx-2 h-100"></div>

                                <!-- Product Info - Middle section -->
                                <div class="product-info flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">
                                            {{ $result->name ?? 'Unnamed Product' }}
                                        </h6>
                                        <div>
                                            <span class="badge bg-success">Rs.{{ $result->selling_price ?? '-' }}</span>
                                            <span class="badge bg-info">Available: {{ $result->available_stock ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span class="me-2">Code: {{ $result->code }}</span> |
                                        <span class="mx-2">Model: {{ $result->model }}</span>
                                    </div>
                                </div>

                                <!-- Vertical divider -->
                                <div class="vr mx-2 h-100"></div>

                                <!-- Product Action - Right side -->
                                <div class="product-action d-flex align-items-center">
                                    <button class="btn btn-sm btn-primary"
                                            wire:click="addToCart({{ $result->id }})" 
                                            {{ $result->available_stock <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @elseif($search && count($searchResults) == 0)
                    <div class="search-results-container position-absolute mt-1 w-100 bg-white shadow-lg rounded">
                        <div class="p-3 text-center text-muted">
                            No Products found matching "{{ $search }}"
>>>>>>> Stashed changes
                        </div>
                    </div>
                    @endif
                </div>
            </div>
<<<<<<< Updated upstream
            <!-- View Product Modal -->
            <div wire:ignore.self class="modal fade" id="viewDetailModal" tabindex="-1"
                aria-labelledby="viewDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h1 class="modal-title fs-5 text-white" id="viewDetailModalLabel">Product Details</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        @if ($ProductDetails)
                        <div class="modal-body p-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        <!-- Image Column -->
                                        <div class="col-md-4 border-end">
                                            <div class="position-relative h-100">
                                                @if ($ProductDetails->image)
                                                <img src="{{ asset('storage/' . $ProductDetails->image) }}"
                                                    alt="{{ $ProductDetails->name }}"
                                                    class="img-fluid rounded-start h-100 w-100 object-fit-cover">
                                                @else
                                                <div
                                                    class="bg-light d-flex align-items-center justify-content-center h-100">
                                                    <i class="bi bi-Product text-muted" style="font-size: 5rem;"></i>
                                                    <p class="text-muted">No image
                                                        available</p>
                                                </div>
                                                @endif

                                                <!-- Status badges in corner -->
                                                <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
                                                    <span
                                                        class="badge bg-{{ $ProductDetails->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($ProductDetails->status) }}
                                                    </span>

                                                    <!-- Stock Status Badge -->
                                                    @if ($ProductDetails->available_stock > 0)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        In Stock
                                                    </span>
                                                    @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                        Out of Stock
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Main Details Column -->
                                        <div class="col-md-8">
                                            <div class="p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h3 class="fw-bold mb-0 text-primary">
                                                        {{ $ProductDetails->name }}
                                                    </h3>
                                                </div>

                                                <!-- Display code prominently -->
                                                <div class="mb-3">
                                                    <span class="badge bg-dark p-2 fs-6">Code:
                                                        {{ $ProductDetails->code }}</span>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">
                                                            Brand</p>
                                                        <h5 class="fw-bold text-primary">
                                                            {{ $ProductDetails->brand }}
                                                        </h5>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">
                                                            Model</p>
                                                        <h5 class="text-primary">
                                                            {{ $ProductDetails->model }}
                                                        </h5>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">
                                                            Category</p>
                                                        <h5 class="text-primary">
                                                            {{ $ProductDetails->category }}
                                                        </h5>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">
                                                            Gender</p>
                                                        <h5 class="text-primary">
                                                            {{ ucfirst($ProductDetails->gender) }}
                                                        </h5>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <p class="text-muted mb-1">
                                                        Description</p>
                                                    <p>{{ $ProductDetails->description }}
                                                    </p>
                                                </div>

                                                <!-- Pricing with attractive discount display -->
                                                <div class="card bg-light p-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h5 class="text-danger fw-bold mb-0">
                                                                Rs.
                                                                {{ number_format($ProductDetails->selling_price, 2) }}
                                                            </h5>
                                                            @if ($ProductDetails->available_stock > 0)
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle-fill"></i>
                                                                {{ $ProductDetails->available_stock }}
                                                                units
                                                                available
                                                            </small>
                                                            @else
                                                            <small class="text-danger fw-bold">
                                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                                OUT
                                                                OF STOCK
                                                            </small>
                                                            @endif
                                                        </div>
                                                        @if ($ProductDetails->discount_price > 0)
                                                        <div class="position-relative">
                                                            <div
                                                                class="position-absolute top-0 start-50 translate-middle">
                                                                <span class="badge bg-danger p-2 rounded-pill">SPECIAL
                                                                    OFFER</span>
                                                            </div>
                                                            <div class="border border-success rounded-3 p-2 text-center mt-3"
                                                                style="background-color: rgba(25, 135, 84, 0.1);">
                                                                <span class="text-success fw-bold fs-5">
                                                                    SAVE Rs.
                                                                    {{ number_format($ProductDetails->discount_price, 2)
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Specifications using Accordion instead of Tabs -->
                            <div class="accordion mt-4" id="ProductDetailsAccordion">
                                <!-- Specifications Section -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#specs-collapse" aria-expanded="true"
                                            aria-controls="specs-collapse">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Specifications
                                        </button>
                                    </h2>
                                    <div id="specs-collapse" class="accordion-collapse collapse show"
                                        data-bs-parent="#ProductDetailsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <th class="text-muted" width="40%">
                                                                    Color</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->color }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Made By</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->made_by }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Movement</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->movement }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Type</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->type }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Dial Color</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->dial_color }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Water Resistance
                                                                </th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->water_resistance }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="col-md-6">
                                                    <table class="table table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <th class="text-muted" width="40%">Case
                                                                    Diameter
                                                                </th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->case_diameter_mm }}
                                                                    mm
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Case Thickness</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->case_thickness_mm }}
                                                                    mm
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Glass Type</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->glass_type }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Strap Material</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->strap_material }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Strap Color</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->strap_color }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Features</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->features }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory Section -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#inventory-collapse"
                                            aria-expanded="false" aria-controls="inventory-collapse">
                                            <i class="bi bi-box-seam me-2"></i>
                                            Inventory
                                        </button>
                                    </h2>
                                    <div id="inventory-collapse" class="accordion-collapse collapse"
                                        data-bs-parent="#ProductDetailsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card mb-3 border-primary">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Shop Stock</p>
                                                                <h4 class="card-title text-primary">
                                                                    {{ $ProductDetails->shop_stock }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card mb-3 border-primary">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Store Stock</p>
                                                                <h4 class="card-title text-primary">
                                                                    {{ $ProductDetails->store_stock }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card mb-3 border-danger">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Damage Stock</p>
                                                                <h4 class="card-title text-danger">
                                                                    {{ $ProductDetails->damage_stock }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div
                                                        class="card mb-3 {{ $ProductDetails->available_stock > 0 ? 'border-success' : 'border-danger' }}">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Available Stock</p>
                                                                <h4
                                                                    class="card-title {{ $ProductDetails->available_stock > 0 ? 'text-success' : 'text-danger' }}">
                                                                    {{ $ProductDetails->available_stock }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card mb-3 border-dark">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Total Stock</p>
                                                                <h4 class="card-title">
                                                                    {{ $ProductDetails->total_stock }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card mb-3 border-info">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Store Location</p>
                                                                <h5 class="card-title text-info">
                                                                    {{ $ProductDetails->location }}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Supplier Section -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#supplier-collapse"
                                            aria-expanded="false" aria-controls="supplier-collapse">
                                            <i class="bi bi-truck me-2"></i> Supplier
                                            Details
                                        </button>
                                    </h2>
                                    <div id="supplier-collapse" class="accordion-collapse collapse"
                                        data-bs-parent="#ProductDetailsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5 class="fw-bold mb-3">Supplier
                                                        Information</h5>
                                                    <table class="table table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <th class="text-muted" width="40%">
                                                                    Supplier</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->supplier_name }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Email</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->email ?: 'N/A' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Phone</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->contact ?: 'N/A' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Barcode</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->barcode }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-muted">
                                                                    Warranty</th>
                                                                <td class="text-primary fw-medium">
                                                                    {{ $ProductDetails->warranty }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="col-md-6">
                                                    <h5 class="fw-bold mb-3">Pricing
                                                        Information</h5>
                                                    <div class="card mb-3 border-secondary">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Supplier Price</p>
                                                                <h5 class="card-title">
                                                                    Rs.
                                                                    {{ number_format($ProductDetails->supplier_price, 2)
                                                                    }}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card mb-3 border-primary">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="card-text fw-bold">
                                                                    Selling Price</p>
                                                                <h5 class="card-title text-primary">
                                                                    Rs.
                                                                    {{ number_format($ProductDetails->selling_price, 2) }}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if ($ProductDetails->discount_price > 0)
                                                    <div class="card border-success bg-success bg-opacity-10">
                                                        <div
                                                            class="card-header bg-success bg-opacity-25 border-success">
                                                            <h6 class="text-success mb-0 fw-bold">
                                                                SPECIAL DISCOUNT
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <p class="card-text fw-bold text-success">
                                                                    Discount
                                                                    Amount</p>
                                                                <h5 class="card-title text-success">
                                                                    Rs.
                                                                    {{ number_format($ProductDetails->discount_price, 2)
                                                                    }}
                                                                </h5>
                                                            </div>
                                                            <div class="progress mt-2" style="height: 10px;">
                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                    style="width: {{ min(($ProductDetails->discount_price / $ProductDetails->selling_price) * 100, 100) }}%"
                                                                    aria-valuenow="{{ ($ProductDetails->discount_price / $ProductDetails->selling_price) * 100 }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between mt-1">
                                                                <small class="text-muted">0%</small>
                                                                <small class="text-muted">Save
                                                                    {{ number_format(($ProductDetails->discount_price /
                                                                    $ProductDetails->selling_price) * 100, 0) }}%</small>
                                                                <small class="text-muted">100%</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
=======
        </div>
    </div>

    {{-- Cart Section --}}
    <div class="card h-100 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-cart text-primary me-2"></i> Cart Items
                </h5>
                <p class="text-muted small mb-0">Manage items in your cart</p>
            </div>
            <div>
                <span class="badge bg-primary">{{ count($cart) }} items</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Product</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Discount (per unit)</th>
                            <th class="text-center">Total</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cart as $id => $item)
                        <tr wire:key="cart-{{ $id }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if ($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" 
                                             class="rounded" alt="{{ $item['name'] }}"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-box text-muted"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="fw-medium text-dark">{{ $item['name'] }}</span>
                                        <div class="text-muted small">{{ $item['code'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-medium text-dark">Rs.{{ number_format($item['price'] ?: $item['price'], 2) }}</span>
                            </td>
                            <td class="text-center">
                                <!-- Improved quantity input with working buttons -->
                                <div class="input-group input-group-sm mx-auto" style="width: 120px;">
                                    <button class="btn btn-outline-primary btn-sm" type="button"
                                            wire:click="updateQuantity({{ $id }}, {{ max(1, $quantities[$id] - 1) }})"
                                            {{ $quantities[$id] <=1 ? 'disabled' : '' }}>
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number"
                                           class="form-control form-control-sm text-center quantity-input"
                                           data-Product-id="{{ $id }}" value="{{ $quantities[$id] }}" min="1"
                                           max="{{ $item['inStock'] }}"
                                           wire:change="validateQuantity({{ $id }})"
                                           wire:model.live="quantities.{{ $id }}">
                                    <button class="btn btn-outline-primary btn-sm" type="button"
                                            wire:click="updateQuantity({{ $id }}, {{ min($item['inStock'], $quantities[$id] + 1) }})"
                                            {{ $quantities[$id]>= $item['inStock'] ? 'disabled' : '' }}>
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <!-- Improved validation message with better styling -->
                                <div class="text-black mt-1" style="font-size: 11px;">
                                    <i class="bi bi-info-circle me-1"></i> Max: {{ $item['inStock'] }} available
                                </div>
                                @if($quantities[$id] > $item['inStock'])
                                <div class="text-danger mt-1" style="font-size: 12px;">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Exceeds available stock!
                                </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="input-group input-group-sm mx-auto" style="width: 100px;">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control form-control-sm"
                                           value="{{ $discounts[$id] ?? 0 }}" min="0"
                                           max="{{ $item['price'] }}" step="0.01"
                                           wire:change="updateDiscount({{ $id }}, $event.target.value)">
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-dark">
                                    Rs.{{ number_format(($item['price'] ?: $item['price']) * $quantities[$id] - ($discounts[$id] ?? 0) * $quantities[$id], 2) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-link text-info p-0 me-2"
                                        wire:click="showDetail({{ $id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-link text-danger p-0"
                                        wire:click="removeFromCart({{ $id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-cart display-4 d-block mb-2"></i>
                                Your cart is empty. Search and add products to create a bill.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Customer & Payment Information --}}
    <div class="row">
        <div class="col-md-6">
            <!-- Customer & Payment Information Card -->
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-person text-primary me-2"></i> Customer & Payment Information
                        </h5>
                        <p class="text-muted small mb-0">Select customer and payment details</p>
                    </div>
                </div>
                <div class="card-body" style="height: 500px; overflow-y: auto;">
                    <!-- Customer Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Select Customer</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group flex-grow-1">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <select class="form-select" wire:model="customerId">
                                    <option value="">-- Select a customer --</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary d-flex align-items-center"
                                    data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                <i class="bi bi-plus-circle me-1"></i>ADD
                            </button>
                        </div>
                        @error('customerId')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Payment Type</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="paymentType"
                                       id="fullPayment" value="full" wire:model.live="paymentType"
                                       checked>
                                <label class="form-check-label" for="fullPayment">
                                    <span class="badge bg-success me-1">
                                        <i class="bi bi-currency-dollar me-1"></i>
                                    </span>
                                    Full Payment
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentType"
                                       id="partialPayment" value="partial"
                                       wire:model.live="paymentType">
                                <label class="form-check-label" for="partialPayment">
                                    <span class="badge bg-warning me-1">
                                        <i class="bi bi-percent me-1"></i>
                                    </span>
                                    Partial Payment
                                </label>
                            </div>
                        </div>
                    </div>

                    @if ($paymentType == 'full')
                    <!-- Full Payment - Single Payment Method -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-credit-card"></i>
                            </span>
                            <select class="form-select @error('paymentMethod') is-invalid @enderror"
                                    wire:model.live="paymentMethod">
                                <option value="">-- Select payment method --</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                            </select>
                            @error('paymentMethod')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Reference Fields based on payment method -->
                    @if ($paymentMethod == 'bank_transfer')
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Bank Transfer Receipt</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-image"></i>
                            </span>
                            <input type="file"
                                   class="form-control @error('paymentReceiptImage') is-invalid @enderror"
                                   wire:model="paymentReceiptImage" accept=".jpg,.jpeg,.png,.gif,.pdf">
                        </div>

                        @if ($paymentReceiptImage)
                        <div class="mt-2">
                            @if ($paymentReceiptImagePreview === 'pdf')
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"
                                   style="font-size: 2rem;"></i>
                                <div>
                                    <p class="fw-bold mb-0">PDF Document</p>
                                    <p class="text-muted small mb-0">
                                        {{ $paymentReceiptImage->getClientOriginalName() }}
                                    </p>
                                </div>
                            </div>
                            @elseif ($paymentReceiptImagePreview && $paymentReceiptImagePreview !== 'image')
                            <img src="{{ $paymentReceiptImagePreview }}" class="img-thumbnail"
                                 style="max-height: 100px">
                            @else
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <i class="bi bi-file-earmark-image text-primary me-2"
                                   style="font-size: 2rem;"></i>
                                <div>
                                    <p class="fw-bold mb-0">Image File</p>
                                    <p class="text-muted small mb-0">
                                        {{ $paymentReceiptImage->getClientOriginalName() }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @elseif($paymentMethod == 'cheque')
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Cheque Image</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-image"></i>
                            </span>
                            <input type="file"
                                   class="form-control @error('paymentReceiptImage') is-invalid @enderror"
                                   wire:model="paymentReceiptImage" accept=".jpg,.jpeg,.png,.gif,.pdf">
                        </div>

                        @if ($paymentReceiptImage)
                        <div class="mt-2">
                            @if ($paymentReceiptImagePreview === 'pdf')
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"
                                   style="font-size: 2rem;"></i>
                                <div>
                                    <p class="fw-bold mb-0">PDF Document</p>
                                    <p class="text-muted small mb-0">
                                        {{ $paymentReceiptImage->getClientOriginalName() }}
                                    </p>
                                </div>
                            </div>
                            @else
                            <img src="{{ $paymentReceiptImagePreview }}" class="img-thumbnail"
                                 style="max-height: 100px">
                            @endif
                        </div>
                        @endif

                        <!-- Bank name is still needed for cheque -->
                        <div class="mt-2">
                            <label class="form-label fw-semibold">Bank Name</label>
                            <input type="text"
                                   class="form-control form-control-sm @error('bankName') is-invalid @enderror"
                                   placeholder="Enter bank name" wire:model="bankName">
                        </div>
                    </div>
                    @endif
                    @else
                    <!-- Partial Payment - Split Payment System -->
                    <div class="card mb-4 border border-warning bg-light">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3">
                                <i class="bi bi-wallet2 me-2"></i>Initial Payment
                            </h6>

                            <!-- Initial Payment Amount -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Amount to Pay Now</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control"
                                           placeholder="Enter amount" wire:model="initialPaymentAmount"
                                           wire:change="calculateBalanceAmount" min="0" step="0.01"
                                           max="{{ $grandTotal }}">
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $initialPaymentAmount ? ($initialPaymentAmount / $grandTotal) * 100 : 0 }}%"
                                         aria-valuenow="{{ $initialPaymentAmount ? ($initialPaymentAmount / $grandTotal) * 100 : 0 }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Rs.0</small>
                                    <small class="text-success">{{ $initialPaymentAmount ? number_format(($initialPaymentAmount / $grandTotal) * 100, 0) : 0 }}%</small>
                                    <small class="text-muted">Rs.{{ number_format($grandTotal, 2) }}</small>
                                </div>
                            </div>

                            <!-- Initial Payment Method -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Initial Payment Method</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-credit-card"></i>
                                    </span>
                                    <select class="form-select @error('initialPaymentMethod') is-invalid @enderror"
                                            wire:model.live="initialPaymentMethod">
                                        <option value="">-- Select payment method --</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                    @error('initialPaymentMethod')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Initial Payment Reference Fields based on payment method -->
                            @if ($initialPaymentMethod == 'bank_transfer')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Bank Transfer Receipt</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-image"></i>
                                    </span>
                                    <input type="file"
                                           class="form-control @error('initialPaymentReceiptImage') is-invalid @enderror"
                                           wire:model="initialPaymentReceiptImage"
                                           accept=".jpg,.jpeg,.png,.gif,.pdf">
                                </div>

                                @if ($initialPaymentReceiptImage)
                                <div class="mt-2">
                                    @if ($initialPaymentReceiptImagePreview === 'pdf')
                                    <div class="d-flex align-items-center p-2 border rounded bg-light">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"
                                           style="font-size: 2rem;"></i>
                                        <div>
                                            <p class="fw-bold mb-0">PDF Document</p>
                                            <p class="text-muted small mb-0">
                                                {{ $initialPaymentReceiptImage->getClientOriginalName() }}
                                            </p>
>>>>>>> Stashed changes
                                        </div>
                                    </div>
                                    @else
                                    <img src="{{ $initialPaymentReceiptImagePreview }}"
                                         class="img-thumbnail" style="max-height: 100px">
                                    @endif
                                </div>
                                @endif
                            </div>
                            @elseif($initialPaymentMethod == 'cheque')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Cheque Image</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-image"></i>
                                    </span>
                                    <input type="file"
                                           class="form-control @error('initialPaymentReceiptImage') is-invalid @enderror"
                                           wire:model="initialPaymentReceiptImage"
                                           accept=".jpg,.jpeg,.png,.gif,.pdf">
                                </div>

                                @if ($initialPaymentReceiptImage)
                                <div class="mt-2">
                                    @if ($initialPaymentReceiptImagePreview === 'pdf')
                                    <div class="d-flex align-items-center p-2 border rounded bg-light">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"
                                           style="font-size: 2rem;"></i>
                                        <div>
                                            <p class="fw-bold mb-0">PDF Document</p>
                                            <p class="text-muted small mb-0">
                                                {{ $initialPaymentReceiptImage->getClientOriginalName() }}
                                            </p>
                                        </div>
                                    </div>
                                    @else
                                    <img src="{{ $initialPaymentReceiptImagePreview }}"
                                         class="img-thumbnail" style="max-height: 100px">
                                    @endif
                                </div>
                                @endif

                                <!-- Bank name is still needed for cheque -->
                                <div class="mt-2">
                                    <label class="form-label fw-semibold">Bank Name</label>
                                    <input type="text"
                                           class="form-control form-control-sm @error('initialBankName') is-invalid @enderror"
                                           placeholder="Enter bank name" wire:model="initialBankName">
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Balance Payment Section -->
                    <div class="card mb-4 border border-info bg-light">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-3">
                                <i class="bi bi-calendar me-2"></i>Balance Payment
                                <span class="badge bg-info float-end">Rs.{{ number_format($balanceAmount, 2) }}</span>
                            </h6>

                            <!-- Balance Payment Method -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Balance Payment Method</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-wallet2"></i>
                                    </span>
                                    <select class="form-select @error('balancePaymentMethod') is-invalid @enderror"
                                            wire:model.live="balancePaymentMethod">
                                        <option value="">-- Select payment method --</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="credit">Credit (Pay Later)</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                    @error('balancePaymentMethod')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Balance Due Date - always show for partial payments -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Balance Payment Due Date</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-date"></i>
                                    </span>
                                    <input type="date" class="form-control"
                                           wire:model="balanceDueDate" min="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <!-- Due Payment Method Selection -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Expected Payment Method for Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-wallet2"></i>
                                    </span>
                                    <select class="form-select" wire:model.live="duePaymentMethod">
                                        <option value="">-- Select expected payment method --</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="credit_card">Credit Card</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Due Payment Attachment (e.g., post-dated cheque image) -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Due Payment Document (if available)</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-file-earmark-image"></i>
                                    </span>
                                    <input type="file" class="form-control"
                                           wire:model="duePaymentAttachment"
                                           accept=".jpg,.jpeg,.png,.gif,.pdf">
                                </div>
                                <small class="form-text text-muted">Upload post-dated cheque or payment agreement document</small>

                                @if ($duePaymentAttachmentPreview)
                                <div class="mt-2">
                                    @if ($duePaymentAttachmentPreview === 'pdf')
                                    <div class="d-flex align-items-center p-2 border rounded bg-light">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"
                                           style="font-size: 2rem;"></i>
                                        <div>
                                            <p class="fw-bold mb-0">PDF Document</p>
                                            <p class="text-muted small mb-0">
                                                {{ $duePaymentAttachment->getClientOriginalName() }}
                                            </p>
                                        </div>
                                    </div>
                                    @else
                                    <img src="{{ $duePaymentAttachmentPreview }}"
                                         class="img-thumbnail" style="max-height: 100px">
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
<<<<<<< Updated upstream
                        @endif

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                        </div>
                    </div>
                </div>
            </div>
            <!-- View Product Modal End-->

            <!-- Add New Customer Modal -->
            <div wire:ignore.self class="modal fade" id="addCustomerModal" tabindex="-1"
                aria-labelledby="addCustomerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="addCustomerModalLabel">
                                <i class="bi bi-user-plus me-2"></i>Add New Customer
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="saveCustomer">
                                <div class="row g-3">
                                    <!-- Customer Type -->
                                    <div class="col-md-12">
                                        <label class="form-label">Customer Type</label>
                                        <div class="d-flex">
                                            <div class="form-check me-4">
                                                <input class="form-check-input" type="radio" name="newCustomerType"
                                                    id="newRetail" value="retail" wire:model="newCustomerType" checked>
                                                <label class="form-check-label" for="newRetail">Retail</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="newCustomerType"
                                                    id="newWholesale" value="wholesale" wire:model="newCustomerType">
                                                <label class="form-check-label" for="newWholesale">Wholesale</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Customer Name & Phone -->
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" placeholder="Enter customer name"
                                                wire:model="newCustomerName" required>
                                        </div>
                                        @error('newCustomerName')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input type="text" class="form-control" placeholder="Enter phone number"
                                                wire:model="newCustomerPhone" required>
                                        </div>
                                        @error('newCustomerPhone')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Email & Address -->
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                                            <input type="email" class="form-control" placeholder="Enter email address"
                                                wire:model="newCustomerEmail">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" class="form-control" placeholder="Enter address"
                                                wire:model="newCustomerAddress">
                                        </div>
                                    </div>

                                    <!-- Additional Information -->
                                    <div class="col-md-12">
                                        <label class="form-label">Additional Information</label>
                                        <textarea class="form-control" rows="3"
                                            placeholder="Add any additional information about this customer"
                                            wire:model="newCustomerNotes"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="saveCustomer">
                                <i class="fas fa-save me-1"></i>Save Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt Modal -->
            <div wire:ignore.self class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title text-white" id="receiptModalLabel">
                                <i class="bi bi-receipt me-2"></i>Sales Receipt
                            </h5>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-sm btn-light me-2" wire:click="downloadReceipt">
                                    <i class="bi bi-download me-1"></i>Download
                                </button>
                                <button type="button" class="btn btn-sm btn-light me-2" wire:click="printReceipt">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                                <button type="button" class="btn-close" wire:click="closeReceiptModal"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body p-4" id="receiptContent">
                            @if ($receipt)
                            <div class="receipt-container">
                                <!-- Receipt Header -->
                                <div class="text-center mb-4">
                                    <h3 class="mb-0">USN Auto Parts</h3>
                                    <p class="mb-0 text-muted small">103 H,Yatiyanthota Road,Seethawaka,avissawella.</p>
                                    <p class="mb-0 text-muted small">Phone: ( 076) 9085252| Email: autopartsusn@gmail.com </p>
                                    <h4 class="mt-3 border-bottom border-2 pb-2">SALES RECEIPT</h4>
                                </div>

                                <!-- Invoice Details -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">INVOICE DETAILS</h6>
                                        <p class="mb-1"><strong>Invoice Number:</strong>
                                            {{ $receipt->invoice_number }}
                                        </p>
                                        <p class="mb-1"><strong>Date:</strong>
                                            {{ $receipt->created_at->setTimezone('Asia/Colombo')->format('d/m/Y h:i A')
                                            }}
                                        </p>
                                        <p class="mb-1"><strong>Payment Status:</strong>
                                            <span
                                                class="badge bg-{{ $receipt->payment_status == 'paid' ? 'success' : ($receipt->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($receipt->payment_status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">CUSTOMER DETAILS</h6>
                                        @if ($receipt->customer)
                                        <p class="mb-1"><strong>Name:</strong>
                                            {{ $receipt->customer->name }}
                                        </p>
                                        <p class="mb-1"><strong>Phone:</strong>
                                            {{ $receipt->customer->phone }}
                                        </p>
                                        <p class="mb-1"><strong>Type:</strong>
                                            {{ ucfirst($receipt->customer_type) }}
                                        </p>
                                        @else
                                        <p class="text-muted">Walk-in Customer</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Items Table -->
                                <h6 class="text-muted mb-2">PURCHASED ITEMS</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Item</th>
                                                <th scope="col">Code</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Discount</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($receipt->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->product_name }}</td>
                                                <td>{{ $item->product_code }}</td>
                                                <td>Rs.{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>Rs.{{ number_format($item->discount * $item->quantity, 2) }}
                                                </td>
                                                <td>Rs.{{ number_format($item->total, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Payment Details -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">PAYMENT INFORMATION</h6>
                                        @if ($receipt->payments->count() > 0)
                                        @foreach ($receipt->payments as $payment)
                                        <div
                                            class="mb-2 p-2 border-start border-3 {{ $payment->is_completed ? 'border-success' : 'border-warning' }} bg-light">
                                            <p class="mb-1">
                                                <strong>{{ $payment->is_completed ? 'Payment' : 'Scheduled Payment'
                                                    }}:</strong>
                                                Rs.{{ number_format($payment->amount, 2) }}
                                            </p>
                                            <p class="mb-1"><strong>Method:</strong>
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                            </p>
                                            @if ($payment->payment_reference)
                                            <p class="mb-1"><strong>Reference:</strong>
                                                {{ $payment->payment_reference }}
                                            </p>
                                            @endif
                                            @if ($payment->is_completed)
                                            <p class="mb-0"><strong>Date:</strong>
                                                {{ $payment->payment_date->format('d/m/Y') }}
                                            </p>
                                            @else
                                            <p class="mb-0"><strong>Due Date:</strong>
                                                {{ $payment->due_date->format('d/m/Y') }}
                                            </p>
                                            @endif
                                        </div>
                                        @endforeach
                                        @else
                                        <p class="text-muted">No payment information available</p>
                                        @endif

                                        @if ($receipt->notes)
                                        <h6 class="text-muted mt-3 mb-2">NOTES</h6>
                                        <p class="font-italic text-muted">{{ $receipt->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body p-3">
                                                <h6 class="card-title">ORDER SUMMARY</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Subtotal:</span>
                                                    <span>Rs.{{ number_format($receipt->subtotal, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Total Discount:</span>
                                                    <span>Rs.{{ number_format($receipt->discount_amount, 2) }}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold">Grand Total:</span>
                                                    <span class="fw-bold">Rs.{{ number_format($receipt->total_amount, 2)
                                                        }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="text-center mt-4 pt-3 border-top">
                                    <p class="mb-0 text-muted small">Thank you for your purchase!</p>
                                </div>
                            </div>
                            @else
                            <div class="text-center p-5">
                                <p class="text-muted">No receipt data available</p>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeReceiptModal">Close</button>
                        </div>
=======
                    </div>
                    @endif

                    <!-- Customer Notes -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" rows="3"
                                  placeholder="Add any notes about this sale"
                                  wire:model="saleNotes"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Order Summary -->
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-receipt text-primary me-2"></i> Order Summary
                        </h5>
                        <p class="text-muted small mb-0">Review and complete your order</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-medium">Rs.{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Discount:</span>
                        <span class="fw-medium text-danger">- Rs.{{ number_format($totalDiscount, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold text-dark">Grand Total:</span>
                        <span class="fw-bold text-primary fs-5">Rs.{{ number_format($grandTotal, 2) }}</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success" wire:click="completeSale" {{ count($cart) == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check2-circle me-2"></i>Complete Sale
                        </button>
>>>>>>> Stashed changes
                    </div>
                </div>
            </div>
        </div>
    </div>
<<<<<<< Updated upstream
    @push('styles')
    <style>
        .search-results-container {
            z-index: 1050;
        }
=======

    <!-- View Product Modal -->
    <div wire:ignore.self class="modal fade" id="viewDetailModal" tabindex="-1" aria-labelledby="viewDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-box text-primary me-2"></i> Product Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($ProductDetails)
                <div class="modal-body p-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <!-- Image Column -->
                                <div class="col-md-4 border-end">
                                    <div class="position-relative h-100">
                                        @if ($ProductDetails->image)
                                        <img src="{{ asset('storage/' . $ProductDetails->image) }}"
                                             alt="{{ $ProductDetails->name }}"
                                             class="img-fluid rounded-start h-100 w-100 object-fit-cover">
                                        @else
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="bi bi-box text-muted" style="font-size: 5rem;"></i>
                                            <p class="text-muted">No image available</p>
                                        </div>
                                        @endif

                                        <!-- Status badges in corner -->
                                        <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
                                            <span class="badge bg-{{ $ProductDetails->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($ProductDetails->status) }}
                                            </span>

                                            <!-- Stock Status Badge -->
                                            @if ($ProductDetails->available_stock > 0)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle-fill"></i>
                                                In Stock
                                            </span>
                                            @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle-fill"></i>
                                                Out of Stock
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Details Column -->
                                <div class="col-md-8">
                                    <div class="p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h3 class="fw-bold mb-0 text-primary">
                                                {{ $ProductDetails->name }}
                                            </h3>
                                        </div>

                                        <!-- Display code prominently -->
                                        <div class="mb-3">
                                            <span class="badge bg-dark p-2 fs-6">Code: {{ $ProductDetails->code }}</span>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">Brand</p>
                                                <h5 class="fw-bold text-primary">{{ $ProductDetails->brand }}</h5>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">Model</p>
                                                <h5 class="text-primary">{{ $ProductDetails->model }}</h5>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">Category</p>
                                                <h5 class="text-primary">{{ $ProductDetails->category }}</h5>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">Gender</p>
                                                <h5 class="text-primary">{{ ucfirst($ProductDetails->gender) }}</h5>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <p class="text-muted mb-1">Description</p>
                                            <p>{{ $ProductDetails->description }}</p>
                                        </div>

                                        <!-- Pricing with attractive discount display -->
                                        <div class="card bg-light p-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="text-danger fw-bold mb-0">
                                                        Rs. {{ number_format($ProductDetails->selling_price, 2) }}
                                                    </h5>
                                                    @if ($ProductDetails->available_stock > 0)
                                                    <small class="text-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        {{ $ProductDetails->available_stock }} units available
                                                    </small>
                                                    @else
                                                    <small class="text-danger fw-bold">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                                        OUT OF STOCK
                                                    </small>
                                                    @endif
                                                </div>
                                                @if ($ProductDetails->discount_price > 0)
                                                <div class="position-relative">
                                                    <div class="position-absolute top-0 start-50 translate-middle">
                                                        <span class="badge bg-danger p-2 rounded-pill">SPECIAL OFFER</span>
                                                    </div>
                                                    <div class="border border-success rounded-3 p-2 text-center mt-3"
                                                         style="background-color: rgba(25, 135, 84, 0.1);">
                                                        <span class="text-success fw-bold fs-5">
                                                            SAVE Rs. {{ number_format($ProductDetails->discount_price, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Specifications using Accordion instead of Tabs -->
                    <div class="accordion mt-4" id="ProductDetailsAccordion">
                        <!-- Specifications Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#specs-collapse" aria-expanded="true"
                                        aria-controls="specs-collapse">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Specifications
                                </button>
                            </h2>
                            <div id="specs-collapse" class="accordion-collapse collapse show"
                                 data-bs-parent="#ProductDetailsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-muted" width="40%">Color</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->color }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Made By</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->made_by }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Movement</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->movement }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Type</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->type }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Dial Color</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->dial_color }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Water Resistance</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->water_resistance }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-muted" width="40%">Case Diameter</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->case_diameter_mm }} mm</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Case Thickness</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->case_thickness_mm }} mm</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Glass Type</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->glass_type }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Strap Material</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->strap_material }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Strap Color</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->strap_color }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Features</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->features }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#inventory-collapse"
                                        aria-expanded="false" aria-controls="inventory-collapse">
                                    <i class="bi bi-box-seam me-2"></i>
                                    Inventory
                                </button>
                            </h2>
                            <div id="inventory-collapse" class="accordion-collapse collapse"
                                 data-bs-parent="#ProductDetailsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-primary">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Shop Stock</p>
                                                        <h4 class="card-title text-primary">{{ $ProductDetails->shop_stock }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-primary">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Store Stock</p>
                                                        <h4 class="card-title text-primary">{{ $ProductDetails->store_stock }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-danger">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Damage Stock</p>
                                                        <h4 class="card-title text-danger">{{ $ProductDetails->damage_stock }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-3 {{ $ProductDetails->available_stock > 0 ? 'border-success' : 'border-danger' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Available Stock</p>
                                                        <h4 class="card-title {{ $ProductDetails->available_stock > 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ $ProductDetails->available_stock }}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-dark">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Total Stock</p>
                                                        <h4 class="card-title">{{ $ProductDetails->total_stock }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-info">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Store Location</p>
                                                        <h5 class="card-title text-info">{{ $ProductDetails->location }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supplier Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#supplier-collapse"
                                        aria-expanded="false" aria-controls="supplier-collapse">
                                    <i class="bi bi-truck me-2"></i> Supplier Details
                                </button>
                            </h2>
                            <div id="supplier-collapse" class="accordion-collapse collapse"
                                 data-bs-parent="#ProductDetailsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="fw-bold mb-3">Supplier Information</h5>
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-muted" width="40%">Supplier</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->supplier_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Email</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->email ?: 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Phone</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->contact ?: 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Barcode</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->barcode }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-muted">Warranty</th>
                                                        <td class="text-primary fw-medium">{{ $ProductDetails->warranty }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-6">
                                            <h5 class="fw-bold mb-3">Pricing Information</h5>
                                            <div class="card mb-3 border-secondary">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Supplier Price</p>
                                                        <h5 class="card-title">
                                                            Rs. {{ number_format($ProductDetails->supplier_price, 2) }}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-3 border-primary">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="card-text fw-bold">Selling Price</p>
                                                        <h5 class="card-title text-primary">
                                                            Rs. {{ number_format($ProductDetails->selling_price, 2) }}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($ProductDetails->discount_price > 0)
                                            <div class="card border-success bg-success bg-opacity-10">
                                                <div class="card-header bg-success bg-opacity-25 border-success">
                                                    <h6 class="text-success mb-0 fw-bold">SPECIAL DISCOUNT</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <p class="card-text fw-bold text-success">Discount Amount</p>
                                                        <h5 class="card-title text-success">
                                                            Rs. {{ number_format($ProductDetails->discount_price, 2) }}
                                                        </h5>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 10px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                             style="width: {{ min(($ProductDetails->discount_price / $ProductDetails->selling_price) * 100, 100) }}%"
                                                             aria-valuenow="{{ ($ProductDetails->discount_price / $ProductDetails->selling_price) * 100 }}"
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <small class="text-muted">0%</small>
                                                        <small class="text-muted">Save {{ number_format(($ProductDetails->discount_price / $ProductDetails->selling_price) * 100, 0) }}%</small>
                                                        <small class="text-muted">100%</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- View Product Modal End-->

    <!-- Add New Customer Modal -->
    <div wire:ignore.self class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus text-primary me-2"></i>Add New Customer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveCustomer">
                        <div class="row g-3">
                            <!-- Customer Type -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Customer Type</label>
                                <div class="d-flex">
                                    <div class="form-check me-4">
                                        <input class="form-check-input" type="radio" name="newCustomerType"
                                               id="newRetail" value="retail" wire:model="newCustomerType" checked>
                                        <label class="form-check-label" for="newRetail">Retail</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="newCustomerType"
                                               id="newWholesale" value="wholesale" wire:model="newCustomerType">
                                        <label class="form-check-label" for="newWholesale">Wholesale</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Name & Phone -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" placeholder="Enter customer name"
                                           wire:model="newCustomerName" required>
                                </div>
                                @error('newCustomerName')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" placeholder="Enter phone number"
                                           wire:model="newCustomerPhone" required>
                                </div>
                                @error('newCustomerPhone')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Email & Address -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                                    <input type="email" class="form-control" placeholder="Enter email address"
                                           wire:model="newCustomerEmail">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" placeholder="Enter address"
                                           wire:model="newCustomerAddress">
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Additional Information</label>
                                <textarea class="form-control" rows="3"
                                          placeholder="Add any additional information about this customer"
                                          wire:model="newCustomerNotes"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveCustomer">
                        <i class="bi bi-save me-1"></i>Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div wire:ignore.self class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt text-primary me-2"></i>Sales Receipt
                    </h5>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" wire:click="downloadReceipt">
                            <i class="bi bi-download me-1"></i>Download
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" wire:click="printReceipt">
                            <i class="bi bi-printer me-1"></i>Print
                        </button>
                        <button type="button" class="btn-close" wire:click="closeReceiptModal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4" id="receiptContent">
                    @if ($receipt)
                    <div class="receipt-container">
                        <!-- Receipt Header -->
                        <div class="text-center mb-4">
                            <h3 class="mb-0">USN Auto Parts</h3>
                            <p class="mb-0 text-muted small">103 H,Yatiyanthota Road,Seethawaka,avissawella.</p>
                            <p class="mb-0 text-muted small">Phone: (076) 9085252 | Email: autopartsusn@gmail.com</p>
                            <h4 class="mt-3 border-bottom border-2 pb-2">SALES RECEIPT</h4>
                        </div>

                        <!-- Invoice Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">INVOICE DETAILS</h6>
                                <p class="mb-1"><strong>Invoice Number:</strong> {{ $receipt->invoice_number }}</p>
                                <p class="mb-1"><strong>Date:</strong> {{ $receipt->created_at->setTimezone('Asia/Colombo')->format('d/m/Y h:i A') }}</p>
                                <p class="mb-1"><strong>Payment Status:</strong>
                                    <span class="badge bg-{{ $receipt->payment_status == 'paid' ? 'success' : ($receipt->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($receipt->payment_status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">CUSTOMER DETAILS</h6>
                                @if ($receipt->customer)
                                <p class="mb-1"><strong>Name:</strong> {{ $receipt->customer->name }}</p>
                                <p class="mb-1"><strong>Phone:</strong> {{ $receipt->customer->phone }}</p>
                                <p class="mb-1"><strong>Type:</strong> {{ ucfirst($receipt->customer_type) }}</p>
                                @else
                                <p class="text-muted">Walk-in Customer</p>
                                @endif
                            </div>
                        </div>

                        <!-- Items Table -->
                        <h6 class="text-muted mb-2">PURCHASED ITEMS</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Item</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Discount</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($receipt->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>Rs.{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rs.{{ number_format($item->discount * $item->quantity, 2) }}</td>
                                        <td>Rs.{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">PAYMENT INFORMATION</h6>
                                @if ($receipt->payments->count() > 0)
                                @foreach ($receipt->payments as $payment)
                                <div class="mb-2 p-2 border-start border-3 {{ $payment->is_completed ? 'border-success' : 'border-warning' }} bg-light">
                                    <p class="mb-1">
                                        <strong>{{ $payment->is_completed ? 'Payment' : 'Scheduled Payment' }}:</strong>
                                        Rs.{{ number_format($payment->amount, 2) }}
                                    </p>
                                    <p class="mb-1"><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                    @if ($payment->payment_reference)
                                    <p class="mb-1"><strong>Reference:</strong> {{ $payment->payment_reference }}</p>
                                    @endif
                                    @if ($payment->is_completed)
                                    <p class="mb-0"><strong>Date:</strong> {{ $payment->payment_date->format('d/m/Y') }}</p>
                                    @else
                                    <p class="mb-0"><strong>Due Date:</strong> {{ $payment->due_date->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                                @endforeach
                                @else
                                <p class="text-muted">No payment information available</p>
                                @endif

                                @if ($receipt->notes)
                                <h6 class="text-muted mt-3 mb-2">NOTES</h6>
                                <p class="font-italic text-muted">{{ $receipt->notes }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body p-3">
                                        <h6 class="card-title">ORDER SUMMARY</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span>Rs.{{ number_format($receipt->subtotal, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Discount:</span>
                                            <span>Rs.{{ number_format($receipt->discount_amount, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Grand Total:</span>
                                            <span class="fw-bold">Rs.{{ number_format($receipt->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="mb-0 text-muted small">Thank you for your purchase!</p>
                        </div>
                    </div>
                    @else
                    <div class="text-center p-5">
                        <p class="text-muted">No receipt data available</p>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeReceiptModal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .search-results-container {
        z-index: 1050;
    }
>>>>>>> Stashed changes

        .search-result-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        /* Add to your existing styles */
        input[type="number"].is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .input-group .invalid-feedback {
            display: none;
        }

        .input-group .is-invalid~.invalid-feedback {
            display: block;
        }

        /* Add to your existing styles */
        input[type="number"].is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .input-group .invalid-feedback {
            display: none;
        }

<<<<<<< Updated upstream
        .input-group .is-invalid~.invalid-feedback {
            display: block;
        }
    </style>
    @endpush
    @push('scripts')

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for form reset event
            Livewire.on('formReset', () => {
                // Force clear all input fields with wire:model
                setTimeout(() => {
                    // Reset customer dropdown
                    const customerSelect = document.querySelector('select[wire\\:model\\.live="customerId"]');
                    if (customerSelect) {
                        customerSelect.value = '';
                        customerSelect.dispatchEvent(new Event('change'));
                    }

                    // Reset pay amount input
                    const payAmountInput = document.querySelector('input[wire\\:model\\.live="paymentAmount"]');
                    if (payAmountInput) {
                        payAmountInput.value = '';
                        payAmountInput.dispatchEvent(new Event('input'));
                    }

                    // Reset payment method
                    const paymentMethodSelect = document.querySelector('select[wire\\:model\\.live="paymentMethod"]');
                    if (paymentMethodSelect) {
                        paymentMethodSelect.value = '';
                        paymentMethodSelect.dispatchEvent(new Event('change'));
                    }

                    // Reset notes textarea
                    const saleNotesTextarea = document.querySelector('textarea[wire\\:model="saleNotes"]');
                    if (saleNotesTextarea) {
                        saleNotesTextarea.value = '';
                    }

                    const paymentNotesTextarea = document.querySelector('textarea[wire\\:model="paymentNotes"]');
                    if (paymentNotesTextarea) {
                        paymentNotesTextarea.value = '';
                    }

                    // Reset file inputs
                    const fileInputs = document.querySelectorAll('input[type="file"]');
                    fileInputs.forEach(input => {
                        input.value = '';
                    });

                    // Trigger Livewire refresh
                    Livewire.dispatch('$refresh');
                }, 100);
            });
        });

        // Additional script for your existing functionality
        document.addEventListener('printReceipt', function() {
            const printContent = document.getElementById('receiptContent').innerHTML;
            const originalContent = document.body.innerHTML;

            const printStyles = `
=======
    .input-group .is-invalid~.invalid-feedback {
        display: block;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('printReceipt', function() {
        const printContent = document.getElementById('receiptContent').innerHTML;
        const originalContent = document.body.innerHTML;

        const printStyles = `
>>>>>>> Stashed changes
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    font-size: 14px;
                }
                .modal-header, .modal-footer, button {
                    display: none !important;
                }
                .receipt-container {
                    width: 100%;
                    max-width: 800px;
                    margin: 0 auto;
                }
                @media print {
                    .no-print {
                        display: none !important;
                    }
<<<<<<< Updated upstream
=======
                }
            </style>
        `;

        document.body.innerHTML = printStyles + printContent;
        window.print();
        document.body.innerHTML = originalContent;

        // Reinitialize Livewire after printing
        window.Livewire.rescan();
    });

    document.addEventListener('closeReceiptModal', function() {
        const modalElement = document.getElementById('receiptModal');
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            } else {
                // If no instance exists, create one and hide it
                const newModalInstance = new bootstrap.Modal(modalElement);
                newModalInstance.hide();
            }
        }
    });

    document.addEventListener('showReceiptModal', function() {
        const modalElement = document.getElementById('receiptModal');
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
            if (modalInstance) {
                modalInstance.show();
            }
        }
    });

    document.addEventListener('showToast', function(e) {
        const type = e.detail.type;
        const message = e.detail.message;

        // Implement your toast notification system here
        // For example, if you're using Bootstrap 5 toasts:
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            const newToastContainer = document.createElement('div');
            newToastContainer.id = 'toast-container';
            newToastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(newToastContainer);
        }

        const toastElement = document.createElement('div');
        toastElement.innerHTML = toastHtml;
        document.getElementById('toast-container').appendChild(toastElement.firstChild);

        const toastInstance = new bootstrap.Toast(document.getElementById('toast-container').lastChild, {
            delay: 3000
        });
        toastInstance.show()
    });

    document.addEventListener('livewire:initialized', () => {
        // Initialize receipt modal
        const receiptModalElement = document.getElementById('receiptModal');
        if (receiptModalElement) {
            const receiptModal = new bootstrap.Modal(receiptModalElement);

            // Listen for when modal is hidden
            receiptModalElement.addEventListener('hidden.bs.modal', function () {
                call('closeReceiptModal');
            });
        }

        // Handle quantity input validation
        function setupQuantityValidation() {
            document.querySelectorAll('.quantity-input').forEach(input => {
                // Add validation on input
                input.addEventListener('input', function(e) {
                    const max = parseInt(this.getAttribute('max')) || 1;
                    const min = parseInt(this.getAttribute('min')) || 1;
                    const value = parseInt(this.value) || 0;

                    // Show visual warning if exceeds max
                    if (value > max) {
                        this.classList.add('is-invalid');
                        const errorElement = this.closest('.input-group').nextElementSibling;
                        if (errorElement?.classList.contains('invalid-feedback')) {
                            errorElement.classList.add('d-block');
                        }
                    } else {
                        this.classList.remove('is-invalid');
                        const errorElement = this.closest('.input-group').nextElementSibling;
                        if (errorElement?.classList.contains('invalid-feedback')) {
                            errorElement.classList.remove('d-block');
                        }
                    }
                });

                // Force correction on blur
                input.addEventListener('blur', function() {
                    const max = parseInt(this.getAttribute('max')) || 1;
                    const min = parseInt(this.getAttribute('min')) || 1;
                    let value = parseInt(this.value) || 0;

                    // Cap the value between min and max
                    if (value > max) {
                        this.value = max;
                        value = max;

                        // Show toast notification
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'warning',
                                message: `Quantity limited to maximum available (${max})`
                            }
                        }));
                    } else if (value < min) {
                        this.value = min;
                        value = min;
                    }

                    // Update Livewire model
                    const ProductId = this.dataset.ProductId;
                    if (ProductId) {
                        set(`quantities.${ProductId}`, value);
                        call('validateQuantity', ProductId);
                    }

                    // Remove visual warning after correction
                    this.classList.remove('is-invalid');
                    const errorElement = this.closest('.input-group').nextElementSibling;
                    if (errorElement?.classList.contains('invalid-feedback')) {
                        errorElement.classList.remove('d-block');
                    }
                });

                // Check initial state
                const value = parseInt(input.value) || 0;
                const max = parseInt(input.getAttribute('max'));
                if (value > max) {
                    input.classList.add('is-invalid');
                    const errorElement = input.closest('.input-group').nextElementSibling;
                    if (errorElement?.classList.contains('invalid-feedback')) {
                        errorElement.classList.add('d-block');
                    }
>>>>>>> Stashed changes
                }
            </style>
        `;

            document.body.innerHTML = printStyles + printContent;
            window.print();
            document.body.innerHTML = originalContent;

            // Reinitialize Livewire after printing
            window.Livewire.rescan();
        });
<<<<<<< Updated upstream

        document.addEventListener('closeReceiptModal', function() {
            const modalElement = document.getElementById('receiptModal');
            if (modalElement) {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    const newModalInstance = new bootstrap.Modal(modalElement);
                    newModalInstance.hide();
                }
            }
        });

        document.addEventListener('showReceiptModal', function() {
            const modalElement = document.getElementById('receiptModal');
            if (modalElement) {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                if (modalInstance) {
                    modalInstance.show();
                }
            }
        });

        // Rest of your existing scripts...
    </script>
    @endpush
</div>
=======
    });
</script>
@endpush
>>>>>>> Stashed changes
