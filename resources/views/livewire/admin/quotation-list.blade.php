<div class="container-fluid p-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-file-earmark-text text-primary me-2"></i> Quotation Management
            </h3>
            <p class="text-muted mb-0">Review, manage and track all quotations efficiently</p>
        </div>
    </div>

    <!-- Message -->
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Search and Actions -->
    <div class="inventory-header w-100 mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 gap-3">
            <!-- 🔍 Search Bar -->
            <div class="search-bar flex-grow-1">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" wire:model.live="search"
                        placeholder="Search by quotation number, customer name or phone...">
                </div>
            </div>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-journal-text text-primary me-2"></i> Quotation List
                        </h5>
                        <p class="text-muted small mb-0">View and manage all quotations in your system</p>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>

                                    <th class="ps-4">No</th>
                                    <th>Quotation No</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-5">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($quotations->count() > 0)
                                @foreach($quotations as $quotation)
                                <tr>
                                    <td class="ps-4" wire:click="viewQuotation({{ $quotation->id }})">
                                        <span class="fw-medium text-dark">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="fw-bold text-primary" wire:click="viewQuotation({{ $quotation->id }})">{{ $quotation->quotation_number }}</td>
                                    <td wire:click="viewQuotation({{ $quotation->id }})">
                                        <strong class="fw-medium text-dark">{{ $quotation->customer_name }}</strong>
                                        <div class="text-muted small">{{ $quotation->customer_phone }}</div>
                                    </td>
                                    <td wire:click="viewQuotation({{ $quotation->id }})">
                                        <span class="fw-medium text-dark">{{ $quotation->quotation_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td wire:click="viewQuotation({{ $quotation->id }})">
                                        <span class="fw-bold text-success">Rs. {{ number_format($quotation->total_amount, 2) }}</span>
                                    </td>
                                    <td wire:click="viewQuotation({{ $quotation->id }})">
                                        <span class="badge bg-{{ $quotation->status === 'converted' ? 'success' : ($quotation->status === 'draft' ? 'secondary' : 'primary') }}">
                                            {{ ucfirst($quotation->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="action-btns">


                                            @if($quotation->status !== 'converted')
                                            <button class="btn action-btn sale" title="Create Sale"
                                                wire:click="openCreateSaleModal({{ $quotation->id }})"
                                                wire:loading.attr="disabled">
                                                <i class="bi bi-cart-check" wire:loading.class="d-none"
                                                    wire:target="openCreateSaleModal({{ $quotation->id }})"></i>
                                                <span wire:loading wire:target="openCreateSaleModal({{ $quotation->id }})">
                                                    <i class="spinner-border spinner-border-sm"></i>
                                                </span>
                                            </button>

                                            @endif

                                            <button class="btn action-btn delete" title="Delete Quotation"
                                                wire:click="confirmDeleteQuotation({{ $quotation->id }})"
                                                wire:loading.attr="disabled">
                                                <i class="bi bi-trash" wire:loading.class="d-none"
                                                    wire:target="confirmDeleteQuotation({{ $quotation->id }})"></i>
                                                <span wire:loading wire:target="confirmDeleteQuotation({{ $quotation->id }})">
                                                    <i class="spinner-border spinner-border-sm"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="alert alert-primary bg-opacity-10">
                                            <i class="bi bi-info-circle me-2"></i> No quotations found.
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

    <!-- Create Sale Modal -->
    <div wire:ignore.self class="modal fade" id="createSaleModal" tabindex="-1"
        aria-labelledby="createSaleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cart-check text-success me-2"></i> Create Sale from Quotation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeCreateSaleModal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedQuotation)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-primary">Quotation Information</h6>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>Quotation #:</strong> {{ $selectedQuotation->quotation_number }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>Customer:</strong> {{ $selectedQuotation->customer_name }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Quotation Total:</strong> Rs. {{ number_format($selectedQuotation->total_amount, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Items -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Sale Items</h6>
                            <button type="button" class="btn btn-sm btn-primary" wire:click="addNewItem">
                                <i class="bi bi-plus-circle me-1"></i> Add New Product
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30%">Product</th>
                                            <th width="10%">Qty</th>
                                            <th width="15%">Unit Price (Rs.)</th>
                                            <th width="15%">Discount/Unit (Rs.)</th>
                                            <th width="15%">Total Discount (Rs.)</th>
                                            <th width="15%">Total (Rs.)</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($editableItems as $index => $item)
                                        <tr>
                                            <td>
                                                <div class="position-relative">
                                                    @if($item['product_name'])
                                                    <div class="mb-2">
                                                        <small class="text-success fw-bold d-block">{{ $item['product_name'] }}</small>
                                                        <small class="text-muted">Code: {{ $item['product_code'] }} | Model: {{ $item['product_model'] }} | Stock: {{ $item['current_stock'] }}</small>
                                                    </div>
                                                    @else
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        placeholder="Search and select product..."
                                                        wire:model="searchTerms.{{ $index }}"
                                                        wire:key="search-{{ $index }}"
                                                        wire:keydown.debounce.500ms="showSearchResult({{ $index }}, $event.target.value)">

                                                    <!-- Search Results -->
                                                    @if($showSearchResults[$index] ?? false)
                                                    <div class="search-results border rounded p-2 mt-1 bg-white shadow"
                                                        style="max-height: 200px; overflow-y: auto; position: absolute; z-index: 1050; width: calc(100% - 30px);">
                                                        @php
                                                        $searchResults = $this->searchProducts($searchTerms[$index]);
                                                        @endphp
                                                        @if(count($searchResults) > 0)
                                                        @foreach($searchResults as $product)
                                                        <div class="search-result-item p-2 border-bottom cursor-pointer"
                                                            wire:click="selectProduct({{ $index }}, {{ $product['id'] }})"
                                                            wire:key="product-{{ $product['id'] }}-{{ $index }}">
                                                            <strong>{{ $product['name'] }}</strong>
                                                            <br>
                                                            <small>Code: {{ $product['code'] }} | Model: {{ $product['model'] }}</small>
                                                            <br>
                                                            <small>Price: Rs.{{ number_format($product['price'], 2) }} | Discount: Rs.{{ number_format($product['discount_price'], 2) }} | Stock: {{ $product['stock'] }}</small>
                                                        </div>
                                                        @endforeach
                                                        @else
                                                        <div class="text-muted p-2">No products found for "{{ $searchTerms[$index] }}"</div>
                                                        @endif
                                                    </div>
                                                    @endif
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm"
                                                    wire:model="editableItems.{{ $index }}.quantity"
                                                    wire:change="updateItemQuantity({{ $index }}, $event.target.value)"
                                                    min="1"
                                                    max="{{ $item['current_stock'] }}">
                                                <small class="text-muted">Max: {{ $item['current_stock'] }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold">Rs. {{ number_format($item['unit_price'], 2) }}</span>
                                            </td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm"
                                                    wire:model="editableItems.{{ $index }}.discount_per_unit"
                                                    wire:change="updateItemDiscount({{ $index }}, $event.target.value)"
                                                    step="0.01" min="0" max="{{ $item['unit_price'] }}">
                                            </td>
                                            <td class="text-end fw-bold text-danger">
                                                Rs. {{ number_format($item['total_discount'], 2) }}
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                Rs. {{ number_format($item['total'], 2) }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="removeItem({{ $index }})"
                                                    {{ count($editableItems) <= 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Discount and Totals -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <!-- Combined Discount Field -->
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3">Additional Discount</h6>
                                    <div class="row align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-label">Discount Type & Value</label>
                                            <div class="input-group mb-2">
                                                <select class="form-select" wire:model.live="saleData.additional_discount_type" style="max-width: 100px;">
                                                    <option value="fixed">Rs.</option>
                                                    <option value="percentage">%</option>
                                                </select>
                                                <input type="number"
                                                    class="form-control"
                                                    wire:model.live="saleData.additional_discount"
                                                    wire:change="calculateTotals"
                                                    step="0.01"
                                                    min="0"
                                                    @if($saleData['additional_discount_type']==='percentage' ) max="100" @endif
                                                    placeholder="Enter discount">
                                            </div>
                                            <small class="text-muted">
                                                @if($saleData['additional_discount_type'] === 'percentage')
                                                Percentage discount applied to subtotal
                                                @else
                                                Fixed amount discount
                                                @endif
                                            </small>
                                        </div>

                                    </div>

                                    <!-- Display current discount info from quotation -->
                                    @if($selectedQuotation && ($selectedQuotation->additional_discount > 0 || $selectedQuotation->additional_discount_value > 0))
                                    <div class="mt-2 p-2 bg-info bg-opacity-10 rounded">
                                        <small class="text-info">
                                            <i class="bi bi-info-circle"></i>
                                            Quotation had additional discount:
                                            @if($selectedQuotation->additional_discount_type === 'percentage')
                                            {{ $selectedQuotation->additional_discount_value ?? $selectedQuotation->additional_discount }}%
                                            (Rs. {{ number_format($selectedQuotation->additional_discount ?? 0, 2) }})
                                            @else
                                            Rs. {{ number_format($selectedQuotation->additional_discount_value ?? $selectedQuotation->additional_discount ?? 0, 2) }}
                                            @endif
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mt-3">
                                <label class="form-label">Sale Notes</label>
                                <textarea class="form-control" wire:model="saleData.notes" rows="3"
                                    placeholder="Any additional notes for this sale..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Pricing Summary -->
                            <div class="card border-0 bg-light">
                                <div class="card-header bg-transparent">
                                    <h6 class="fw-bold mb-0">Pricing Summary</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">Subtotal:</td>
                                                <td class="text-end">Rs. {{ number_format($subtotal, 2) }}</td>
                                            </tr>

                                            <!-- Item Discount -->
                                            @if($totalDiscount > 0)
                                            <tr>
                                                <td class="fw-bold text-danger">Item Discount:</td>
                                                <td class="text-end text-danger">- Rs. {{ number_format($totalDiscount, 2) }}</td>
                                            </tr>
                                            @endif

                                            <!-- Additional Discount -->
                                            @if($additionalDiscountAmount > 0)
                                            <tr>
                                                <td class="fw-bold text-danger">Additional Discount:</td>
                                                <td class="text-end text-danger">- Rs. {{ number_format($additionalDiscountAmount, 2) }}</td>
                                            </tr>
                                            @endif

                                            <tr class="border-top">
                                                <td class="fw-bold fs-5">Grand Total:</td>
                                                <td class="text-end fw-bold fs-5 text-success">Rs. {{ number_format($grandTotal, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCreateSaleModal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" wire:click="createSale"
                        wire:loading.attr="disabled">
                        <i class="bi bi-cart-check me-1" wire:loading.class="d-none"></i>
                        <span wire:loading wire:target="createSale">
                            <i class="spinner-border spinner-border-sm me-1"></i>
                        </span>
                        Create Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Quotation View Modal -->
    <div wire:ignore.self class="modal fade" id="viewQuotationModal" tabindex="-1"
        aria-labelledby="viewQuotationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                <div class="modal-body p-0">

                    @if($selectedQuotation)
                    <!-- Professional Quotation Template -->
                    <div class="quotation-template" id="quotationPrint">

                        <!-- Quotation Header -->
                        <div class="quotation-header bg-primary text-white p-4 rounded-top">
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h1 class="h3 fw-bold mb-1">QUOTATION</h1>
                                    <p class="mb-0 opacity-75">Official Price Quotation</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="quotation-meta">
                                        <h2 class="h4 fw-bold mb-1">{{ $selectedQuotation->quotation_number }}</h2>
                                        <p class="mb-0 opacity-75">Date: {{ $selectedQuotation->quotation_date->format('F d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company & Client Info -->
                        <div class="quotation-body p-4">
                            <div class="row mb-4">
                                <!-- Company Info -->
                                <div class="col-md-6">
                                    <div class="company-info">
                                        <h5 class="fw-bold text-primary mb-3">FROM</h5>
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-2">USN Auto Parts</h6>
                                                <p class="mb-1 small">103 H,Yatiyanthota Road,Seethawaka,avissawella.</p>
                                                <p class="mb-1 small">Phone: ( 076) 9085252</p>
                                                <p class="mb-0 small">Email: autopartsusn@gmail.com</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Client Info -->
                                <div class="col-md-6">
                                    <div class="client-info">
                                        <h5 class="fw-bold text-primary mb-3">BILL TO</h5>
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-2">{{ $selectedQuotation->customer_name }}</h6>
                                                <p class="mb-1 small">{{ $selectedQuotation->customer_phone }}</p>
                                                @if($selectedQuotation->customer_email)
                                                <p class="mb-1 small">{{ $selectedQuotation->customer_email }}</p>
                                                @endif
                                                @if($selectedQuotation->customer_address)
                                                <p class="mb-0 small">{{ $selectedQuotation->customer_address }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quotation Items Table -->
                            <div class="quotation-items mb-4">
                                <h5 class="fw-bold text-primary mb-3">QUOTATION ITEMS</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" width="5%">#</th>
                                                <th width="45%">Item Description</th>
                                                <th class="text-center" width="10%">Qty</th>
                                                <th class="text-end" width="20%">Unit Price (Rs.)</th>
                                                <th class="text-end" width="20%">Amount (Rs.)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($selectedQuotation->items && count($selectedQuotation->items) > 0)
                                            @foreach($selectedQuotation->items as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $item['product_name'] ?? $item['name'] ?? 'N/A' }}</strong>
                                                    @if(isset($item['product_model']) && $item['product_model'])
                                                    <br><small class="text-muted">Model: {{ $item['product_model'] }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                                                <td class="text-end">{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                                <td class="text-end">{{ number_format($item['total'] ?? (($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)), 2) }}</td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    No items found in this quotation
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pricing Summary -->
                            <div class="pricing-summary">
                                <div class="row justify-content-end">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold">Subtotal</td>
                                                    <td class="text-end">Rs. {{ number_format($selectedQuotation->subtotal, 2) }}</td>
                                                </tr>

                                                <!-- Total Discount (Item + Additional) -->
                                                @php
                                                $totalDiscount = $selectedQuotation->discount_amount + $selectedQuotation->additional_discount;
                                                @endphp

                                                @if($totalDiscount > 0)
                                                <tr>
                                                    <td class="fw-bold text-danger">
                                                        Total Discount
                                                        @if($selectedQuotation->discount_amount > 0 && $selectedQuotation->additional_discount > 0)
                                                        <br><small class="text-muted fw-normal">(Item: Rs.{{ number_format($selectedQuotation->discount_amount, 2) }} + Additional: Rs.{{ number_format($selectedQuotation->additional_discount, 2) }})</small>
                                                        @elseif($selectedQuotation->discount_amount > 0)
                                                        @elseif($selectedQuotation->additional_discount > 0)
                                                        <br><small class="text-muted fw-normal">(Additional Discount)</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-danger">- Rs. {{ number_format($totalDiscount, 2) }}</td>
                                                </tr>
                                                @endif

                                                <tr class="table-active">
                                                    <td class="fw-bold fs-5">TOTAL AMOUNT</td>
                                                    <td class="text-end fw-bold fs-5 text-success">Rs. {{ number_format($selectedQuotation->total_amount, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions -->
                            @if($selectedQuotation->terms_conditions)
                            <div class="terms-conditions mt-4">
                                <h5 class="fw-bold text-primary mb-3">TERMS & CONDITIONS</h5>
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <p class="mb-0 small">{!! nl2br(e($selectedQuotation->terms_conditions)) !!}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Validity Period -->
                            @if($selectedQuotation->valid_until)
                            <div class="validity-period mt-3">
                                <div class="alert alert-info">
                                    <i class="bi bi-clock me-2"></i>
                                    <strong>Valid Until:</strong> {{ \Carbon\Carbon::parse($selectedQuotation->valid_until)->format('F d, Y') }}
                                </div>
                            </div>
                            @endif

                            <!-- Footer Notes -->
                            <div class="quotation-footer mt-5 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="small text-muted mb-1">Thank you for your business!</p>
                                        <p class="small text-muted mb-0">We look forward to serving you.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-circle text-muted fs-1"></i>
                        <p class="text-muted mt-2">Quotation details not found.</p>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="deleteConfirmationModal" tabindex="-1"
        aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i> Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this quotation? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="deleteQuotation" data-bs-dismiss="modal">
                        <i class="bi bi-trash me-1"></i> Delete Quotation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .action-btn.view {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
    }

    .action-btn.view:hover {
        background-color: rgba(13, 110, 253, 0.2);
    }

    .action-btn.sale {
        color: #198754;
        background-color: rgba(25, 135, 84, 0.1);
    }

    .action-btn.sale:hover {
        background-color: rgba(25, 135, 84, 0.2);
    }

    .action-btn.success {
        color: #6c757d;
        background-color: rgba(108, 117, 125, 0.1);
    }

    .action-btn.delete {
        color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
    }

    .action-btn.delete:hover {
        background-color: rgba(220, 53, 69, 0.2);
    }

    .search-results {
        background: white;
        z-index: 1050;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .search-result-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .discount-toggle-btn {
        min-width: 60px;
    }

    /* Disable number input arrows */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    /* Quotation View Modal Styles */
    .quotation-template {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .quotation-header {
        background: linear-gradient(135deg, #0d6404ff 0%, #027f44ff 100%);
    }

    .company-info h6,
    .client-info h6 {
        color: #0d6efd;
    }

    .table-light th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table-active {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
        }

        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle view modal
        Livewire.on('showViewModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('viewQuotationModal'));
            modal.show();
        });

        // Handle create sale modal
        Livewire.on('showCreateSaleModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('createSaleModal'));
            modal.show();
        });

        Livewire.on('closeCreateSaleModal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('createSaleModal'));
            if (modal) {
                modal.hide();
            }
        });

        // Handle delete confirmation modal
        Livewire.on('show-delete-confirmation', () => {
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            modal.show();
        });

        // Handle success messages
        Livewire.on('show-success', (message) => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                confirmButtonColor: '#198754',
                timer: 3000
            });
        });

        // Handle error messages
        Livewire.on('show-error', (message) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        });

        // Handle product selection
        Livewire.on('product-selected', () => {
            // Close any open search results
            const searchResults = document.querySelectorAll('.search-results');
            searchResults.forEach(result => {
                result.style.display = 'none';
            });
        });

        // Auto-calculate when modal opens
        document.getElementById('createSaleModal')?.addEventListener('shown.bs.modal', function() {
            Livewire.dispatch('calculateTotals');
        });
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-results') && !e.target.matches('input[wire\\:model^="searchTerms"]')) {
            const searchResults = document.querySelectorAll('.search-results');
            searchResults.forEach(result => {
                result.style.display = 'none';
            });
        }
    });

    // Show search results when focusing on input
    document.addEventListener('focusin', function(e) {
        if (e.target.matches('input[wire\\:model^="searchTerms"]')) {
            const index = e.target.getAttribute('wire:model').split('.')[1];
            const searchTerm = e.target.value;
            if (searchTerm.length >= 2) {
                Livewire.dispatch('showSearchResult', {
                    index: index,
                    searchTerm: searchTerm
                });
            }
        }
    });

    // Real-time calculation triggers
    document.addEventListener('input', function(e) {
        // Trigger calculations when quantity or discount inputs change
        if (e.target.matches('input[wire\\:model^="editableItems"][type="number"]')) {
            setTimeout(() => {
                Livewire.dispatch('calculateTotals');
            }, 300);
        }
    });
</script>
@endpush