<div class="container-fluid py-3" style="background-color:#f5fdf1ff;">
    <!-- Enhanced Header -->
    <div class="header-section mb-4">
        <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm border">
            <!-- Logo and Company Info -->
            <div class="d-flex align-items-center">
                <div class="company-logo me-3">
                    <i class="bi bi-shop fs-3 text-success"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold" style="color:#3b5b0c;">USN AUTO PARTS</h4>
                    <small class="text-muted">Point of Sale System</small>
                </div>
            </div>

            <!-- POS Button -->
            <div class="d-flex align-items-center">
                <div class="badge d-flex align-items-center px-3 py-2 rounded-2 shadow-sm"
                    style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); color:white; border:1px solid #3b5b0c; cursor: pointer; transition: all 0.2s ease;"
                    onclick="handlePOSClick()"
                    role="button"
                    onmouseover="this.style.background='linear-gradient(0deg, rgba(40, 70, 5, 1) 0%, rgba(120, 160, 25, 1) 100%)';"
                    onmouseout="this.style.background='linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%)';">
                    <i class="bi bi-cart-plus me-2"></i>
                    <span class="fw-semibold">POS</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Customer Information --}}
        <div class="col-6 mb-4">
            <div class="card border-2 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold" style="color:#3b5b0c;">
                        <i class="bi bi-person me-2" style="color:#8eb922;"></i>Customer Information
                    </h5>
                    <button class="btn btn-sm rounded-1 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); border-color: #3b5b0c;" wire:click="openCustomerModal">
                        <i class="bi bi-plus-circle me-1"></i> Add New Customer
                    </button>
                </div>
                <div class="card-body">
                    {{-- Customer Success Alert --}}
                    @if(session()->has('customer_success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-0 mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Success!</strong> {{ session('customer_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Select Customer *</label>
                            <select class="form-select rounded-0 border" wire:model.live="customerId">
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customer->name === 'Walking Customer' ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                    @if($customer->phone)
                                    - {{ $customer->phone }}
                                    @endif
                                    @if($customer->name === 'Walking Customer') (Default) @endif
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                @if($selectedCustomer && $selectedCustomer->name === 'Walking Customer')
                                <span class="text-info">
                                    <i class="bi bi-info-circle"></i> Using default walking customer
                                </span>
                                @else
                                Select existing customer or add new
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Products Card --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-2 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold" style="color:#3b5b0c;">
                        <i class="bi bi-search me-2" style="color:#8eb922;"></i>Add Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" class="form-control rounded-0 border" wire:model.live="search"
                            placeholder="Search by product name, code or model...">
                    </div>

                    {{-- Search Results --}}
                    @if($search && count($searchResults) > 0)
                    <div class="search-results border rounded-0 bg-white">
                        @foreach($searchResults as $product)
                        <div class="p-3 border-bottom" wire:key="product-{{ $product['id'] }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ $product['name'] }}</h6>
                                    <p class="text-muted small mb-0">
                                        Code: {{ $product['code'] }} |
                                        Model: {{ $product['model'] }}
                                    </p>
                                    <p class="text-success small mb-0">
                                        Rs.{{ number_format($product['price'], 2) }} |
                                        Stock: {{ $product['stock'] }}
                                    </p>
                                </div>
                                <button class="btn btn-sm rounded-0 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); border-color:#3b5b0c;"
                                    wire:click="addToCart({{ json_encode($product) }})"
                                    {{ $product['stock'] <= 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @elseif($search)
                    <div class="text-center text-muted p-3">
                        No products found
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sale Items Table --}}
        <div class="col-md-12 mb-4">
            <div class="card border-2 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold" style="color:#3b5b0c;">
                        <i class="bi bi-cart me-2" style="color:#8eb922;"></i>Sale Items
                    </h5>
                    <span class="badge rounded-1 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);">{{ count($cart) }} items</span>
                </div>
                <div class="card-body p-0">
                    @if(count($cart) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="30">#</th>
                                    <th>Product</th>
                                    <th width="120">Unit Price</th>
                                    <th width="150">Quantity</th>
                                    <th width="120">Discount/Unit</th>
                                    <th width="120">Total</th>
                                    <th width="100" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $item['name'] }}</strong>
                                            <div class="text-muted small">
                                                {{ $item['code'] }} | {{ $item['model'] }}
                                            </div>
                                            <div class="text-info small">
                                                Stock: {{ $item['stock'] }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">
                                        Rs.{{ number_format($item['price'], 2) }}
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary rounded-0" type="button"
                                                wire:click="decrementQuantity({{ $index }})">-</button>
                                            <input type="number" class="form-control text-center rounded-0"
                                                wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}">
                                            <button class="btn btn-outline-secondary rounded-0" type="button"
                                                wire:click="incrementQuantity({{ $index }})">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-danger rounded-0"
                                            wire:change="updateDiscount({{ $index }}, $event.target.value)"
                                            value="{{ $item['discount'] }}" min="0" step="0.01"
                                            placeholder="0.00">
                                    </td>
                                    <td class="fw-bold">
                                        Rs.{{ number_format($item['total'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger rounded-0"
                                            wire:click="removeFromCart({{ $index }})"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                    <td class="fw-bold">Rs.{{ number_format($subtotal, 2) }}</td>
                                    <td></td>
                                </tr>

                                {{-- Additional Discount Section --}}
                                <tr>
                                    <td colspan="3" class="text-end fw-bold align-middle">
                                        Additional Discount:
                                        @if($additionalDiscount > 0)
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1"
                                            wire:click="removeAdditionalDiscount" title="Remove discount">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        @endif
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group input-group-sm">
                                            <input type="number"
                                                class="form-control form-control-sm text-danger rounded-0"
                                                wire:model.live="additionalDiscount"
                                                min="0"
                                                step="{{ $additionalDiscountType === 'percentage' ? '1' : '0.01' }}"
                                                @if($additionalDiscountType==='percentage' ) max="100" @endif
                                                placeholder="0{{ $additionalDiscountType === 'percentage' ? '' : '.00' }}">

                                            <span class="input-group-text rounded-0">
                                                {{ $additionalDiscountType === 'percentage' ? '%' : 'Rs.' }}
                                            </span>

                                            <button type="button"
                                                class="btn btn-outline-secondary rounded-0"
                                                wire:click="toggleDiscountType"
                                                title="Switch Discount Type">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        @if($additionalDiscount > 0)
                                        - Rs.{{ number_format($additionalDiscountAmount, 2) }}
                                        @if($additionalDiscountType === 'percentage')
                                        <div class="text-muted small">({{ $additionalDiscount }}%)</div>
                                        @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>

                                {{-- Grand Total --}}
                                <tr>
                                    <td colspan="5" class="text-end fw-bold fs-5">Grand Total:</td>
                                    <td class="fw-bold fs-5" style="color:#8eb922;">Rs.{{ number_format($grandTotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-cart display-4 d-block mb-2 text-muted"></i>
                        No items added yet
                    </div>
                    @endif
                </div>
                @if(count($cart) > 0)
                <div class="card-footer bg-white">
                    <button class="btn btn-danger rounded-0" wire:click="clearCart">
                        <i class="bi bi-trash me-2"></i>Clear All Items
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment Information Card --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-2 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold" style="color:#3b5b0c;">
                        <i class="bi bi-credit-card me-2" style="color:#8eb922;"></i>Payment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Payment Method *</label>
                            <select class="form-select rounded-0 border" wire:model.live="paymentMethod">
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit">Credit (Pay Later)</option>
                            </select>
                        </div>

                        {{-- Cash Payment Fields --}}
                        @if($paymentMethod === 'cash')
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Cash Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-0">Rs.</span>
                                <input type="number" class="form-control rounded-0"
                                    wire:model.live="cashAmount"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00">
                            </div>
                            <div class="form-text">
                                Enter the cash amount received
                            </div>
                        </div>
                        @endif

                        {{-- Cheque Payment Fields --}}
                        @if($paymentMethod === 'cheque')
                        <div class="col-md-12">
                            <div class="card bg-light border-0">
                                <div class="card-header d-flex justify-content-between align-items-center bg-white py-2">
                                    <h6 class="mb-0 fw-semibold" style="color:#3b5b0c;">Add Cheque Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold" style="color:#3b5b0c;">Cheque Number *</label>
                                            <input type="text" class="form-control form-control-sm rounded-0"
                                                wire:model="tempChequeNumber"
                                                placeholder="Enter cheque number">
                                            @error('tempChequeNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold" style="color:#3b5b0c;">Bank Name *</label>
                                            <input type="text" class="form-control form-control-sm rounded-0"
                                                wire:model="tempBankName"
                                                placeholder="Enter bank name">
                                            @error('tempBankName') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold" style="color:#3b5b0c;">Cheque Date *</label>
                                            <input type="date" class="form-control form-control-sm rounded-0"
                                                wire:model="tempChequeDate">
                                            @error('tempChequeDate') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold" style="color:#3b5b0c;">Cheque Amount *</label>
                                            <input type="number" class="form-control form-control-sm rounded-0"
                                                wire:model="tempChequeAmount"
                                                min="0" step="0.01"
                                                placeholder="0.00">
                                            @error('tempChequeAmount') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm w-100 rounded-0 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); border-color:#3b5b0c;"
                                                wire:click="addCheque">
                                                <i class="bi bi-plus-circle me-1"></i> Add Cheque
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Cheques List --}}
                            @if(count($cheques) > 0)
                            <div class="mt-3">
                                <h6 class="mb-2 fw-semibold" style="color:#3b5b0c;">Added Cheques ({{ count($cheques) }})</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Cheque No</th>
                                                <th>Bank</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th width="50">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cheques as $index => $cheque)
                                            <tr>
                                                <td>{{ $cheque['number'] }}</td>
                                                <td>{{ $cheque['bank_name'] }}</td>
                                                <td>{{ date('d/m/Y', strtotime($cheque['date'])) }}</td>
                                                <td class="fw-bold">Rs.{{ number_format($cheque['amount'], 2) }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-0"
                                                        wire:click="removeCheque({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="3" class="text-end fw-bold">Total:</td>
                                                <td colspan="2" class="fw-bold text-success">
                                                    Rs.{{ number_format(collect($cheques)->sum('amount'), 2) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Bank Transfer Fields --}}
                        @if($paymentMethod === 'bank_transfer')
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Bank Transfer Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-0">Rs.</span>
                                <input type="number" class="form-control rounded-0"
                                    wire:model.live="bankTransferAmount"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Transfer Receipt (Optional)</label>
                            <input type="file" class="form-control rounded-0"
                                wire:model="bankTransferFile"
                                accept="image/*,application/pdf">
                            <div class="form-text">Upload transfer receipt or proof</div>
                            @if($bankTransferFile)
                            <div class="mt-2">
                                <span class="badge rounded-1 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);">
                                    <i class="bi bi-check-circle me-1"></i> File selected: {{ $bankTransferFile->getClientOriginalName() }}
                                </span>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Credit Payment Info --}}
                        @if($paymentMethod === 'credit')
                        <div class="col-md-12">
                            <div class="alert alert-warning mb-0 rounded-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Credit Sale</strong>
                                <p class="mb-0 mt-2">The full amount of Rs.{{ number_format($grandTotal, 2) }} will be marked as due. Customer can pay later.</p>
                            </div>
                        </div>
                        @endif

                        {{-- Payment Summary --}}
                        @if($paymentMethod !== 'credit')
                        <div class="col-md-12">
                            <div class="border rounded-0 p-3 bg-light">
                                <h6 class="mb-3 fw-semibold" style="color:#3b5b0c;">Payment Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Grand Total:</span>
                                    <span class="fw-bold">Rs.{{ number_format($grandTotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Paid Amount:</span>
                                    <span class="fw-bold text-success">Rs.{{ number_format($totalPaidAmount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Due Amount:</span>
                                    <span class="fw-bold {{ $dueAmount > 0 ? 'text-warning' : 'text-success' }}">
                                        Rs.{{ number_format($dueAmount, 2) }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Status:</span>
                                    <span class="badge bg-{{ $paymentStatus === 'paid' ? 'success' : ($paymentStatus === 'partial' ? 'warning' : 'danger') }} rounded-1">
                                        {{ ucfirst($paymentStatus) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($totalPaidAmount < $grandTotal && $totalPaidAmount> 0)
                            <div class="col-md-12">
                                <div class="alert alert-info small mb-0 rounded-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Partial payment. Remaining Rs.{{ number_format($dueAmount, 2) }} will be marked as due.
                                </div>
                            </div>
                            @endif
                            @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes Card --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-2 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold" style="color:#3b5b0c;">
                        <i class="bi bi-chat-text me-2" style="color:#8eb922;"></i>Notes
                    </h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control rounded-0" wire:model="notes" rows="8"
                        placeholder="Add any notes for this sale..."></textarea>
                </div>
            </div>
        </div>

        {{-- Create Sale Button --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center bg-light py-4">
                    <button class="btn btn-lg px-5 rounded-0 fw-bold text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); border-color:#3b5b0c;" wire:click="validateAndCreateSale"
                        {{ count($cart) == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cart-check me-2"></i>Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Customer Modal --}}
    @if($showCustomerModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header text-white rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus me-2"></i>Add New Customer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCustomerModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Name *</label>
                            <input type="text" class="form-control rounded-0" wire:model="customerName" placeholder="Enter customer name">
                            @error('customerName') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Phone *</label>
                            <input type="text" class="form-control rounded-0" wire:model="customerPhone" placeholder="Enter phone number">
                            @error('customerPhone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Email</label>
                            <input type="email" class="form-control rounded-0" wire:model="customerEmail" placeholder="Enter email address">
                            @error('customerEmail') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Customer Type *</label>
                            <select class="form-select rounded-0" wire:model="customerType">
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                            @error('customerType') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Business Name</label>
                            <input type="text" class="form-control rounded-0" wire:model="businessName" placeholder="Enter business name">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="color:#3b5b0c;">Address *</label>
                            <textarea class="form-control rounded-0" wire:model="customerAddress" rows="3" placeholder="Enter address"></textarea>
                            @error('customerAddress') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0">
                    <button type="button" class="btn btn-secondary rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);"wire:click="closeCustomerModal">
                        <i class="bi bi-x-circle me-2"  ></i>Cancel
                    </button>
                    <button type="button" class="btn rounded-0 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); border-color:#3b5b0c;" wire:click="createCustomer">
                        <i class="bi bi-check-circle me-2"></i>Create Customer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Payment Confirmation Modal --}}
    @if($showPaymentConfirmModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.7);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header text-white rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i>Partial Payment Confirmation
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3 rounded-0">
                        <h6 class="alert-heading">Payment Amount Less Than Grand Total</h6>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Grand Total:</strong>
                            <span>Rs.{{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Paid Amount:</strong>
                            <span class="text-success">Rs.{{ number_format($totalPaidAmount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Due Amount:</strong>
                            <span class="text-danger">Rs.{{ number_format($pendingDueAmount, 2) }}</span>
                        </div>
                    </div>
                    <p class="mb-0">
                        The due amount of <strong class="text-danger">Rs.{{ number_format($pendingDueAmount, 2) }}</strong>
                        will be added to the customer's account. Do you want to proceed?
                    </p>
                </div>
                <div class="modal-footer rounded-0">
                    <button type="button" class="btn btn-secondary rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);" wire:click="cancelSaleConfirmation">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn rounded-0 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); border-color:#3b5b0c;" wire:click="confirmSaleWithDue">
                        <i class="bi bi-check-circle me-2"></i>Yes, Proceed with Due
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Sale Preview Modal --}}
    @if($showSaleModal && $createdSale)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header text-white rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cart-check me-2"></i>
                        Sale Completed Successfully! - {{ $createdSale->invoice_number }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="createNewSale"></button>
                </div>

                <div class="modal-body p-0">
                    {{-- Sale Preview --}}
                    <div class="sale-preview p-4">
                        {{-- Header --}}
                        <div class="header text-center mb-4">
                            <h2 class="mb-1 fw-bold" style="color:#3b5b0c;">USN AUTO PARTS</h2>
                            <p class="mb-1">103 H, Yatiyanthota Road, Seethawaka, Avissawella</p>
                            <p class="mb-1">Phone: (076) 9085252 | Email: autopartsusn@gmail.com</p>
                        </div>

                        {{-- Customer & Sale Details --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light rounded-0">
                                        <h6 class="mb-0 fw-bold" style="color:#3b5b0c;">Customer Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>{{ $createdSale->customer->name }}</strong></p>
                                        <p class="mb-1">{{ $createdSale->customer->address }}</p>
                                        <p class="mb-1">Tel: {{ $createdSale->customer->phone }}</p>
                                        @if($createdSale->customer->email)
                                        <p class="mb-0">Email: {{ $createdSale->customer->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light rounded-0">
                                        <h6 class="mb-0 fw-bold" style="color:#3b5b0c;">Sale Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Sale ID:</strong> {{ $createdSale->sale_id }}</p>
                                        <p class="mb-1"><strong>Invoice No:</strong> {{ $createdSale->invoice_number }}</p>
                                        <p class="mb-1"><strong>Date:</strong> {{ $createdSale->created_at->format('d/m/Y') }}</p>
                                        <p class="mb-1"><strong>Payment Status:</strong>
                                            <span class="badge bg-{{ $createdSale->payment_status == 'paid' ? 'success' : ($createdSale->payment_status == 'partial' ? 'warning' : 'danger') }} rounded-1">
                                                {{ ucfirst($createdSale->payment_status) }}
                                            </span>
                                        </p>
                                        <p class="mb-0"><strong>Payment Method:</strong>
                                            {{ ucfirst($paymentMethod) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); color:white;">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Item Code</th>
                                        <th>Description</th>
                                        <th width="80">Qty</th>
                                        <th width="120">Unit Price</th>
                                        <th width="120">Discount</th>
                                        <th width="120">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($createdSale->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->discount_per_unit ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                {{-- Totals Section --}}
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Subtotal:</td>
                                        <td class="text-end fw-bold">{{ number_format($createdSale->subtotal, 2) }}</td>
                                    </tr>

                                    @if($createdSale->discount_amount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold text-danger">Total Discount:</td>
                                        <td class="text-end fw-bold text-danger">- {{ number_format($createdSale->discount_amount, 2) }}</td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td colspan="6" class="text-end fw-bold fs-5">Grand Total:</td>
                                        <td class="text-end fw-bold fs-5" style="color:#8eb922;">
                                            {{ number_format($createdSale->total_amount, 2) }}
                                        </td>
                                    </tr>

                                    @if($createdSale->payments->count() > 0)
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold text-success">Paid Amount:</td>
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format($createdSale->payments->sum('amount'), 2) }}
                                        </td>
                                    </tr>
                                    @endif

                                    @if($createdSale->due_amount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold text-warning">Due Amount:</td>
                                        <td class="text-end fw-bold text-warning">
                                            {{ number_format($createdSale->due_amount, 2) }}
                                        </td>
                                    </tr>
                                    @endif

                                </tfoot>
                            </table>
                        </div>

                        {{-- Payment Details --}}
                        @if($createdSale->payments->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light rounded-0">
                                        <h6 class="mb-0 fw-bold" style="color:#3b5b0c;">Payment Details</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($createdSale->payments as $payment)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</span>
                                            <span><strong>Amount:</strong> Rs.{{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                        @if($payment->payment_reference)
                                        <div class="mb-2">
                                            <strong>Reference:</strong> {{ $payment->payment_reference }}
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Notes --}}
                        @if($createdSale->notes)
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light rounded-0">
                                        <h6 class="mb-0 fw-bold" style="color:#3b5b0c;">Notes</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{!! nl2br(e($createdSale->notes)) !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="modal-footer justify-content-center rounded-0">
                    <button type="button" class="btn btn-outline-secondary me-2 rounded-0" wire:click="createNewSale">
                        <i class="bi bi-plus-circle me-2"></i>Create New Sale
                    </button>
                    <button type="button" class="btn rounded-0 text-white" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); border-color:#3b5b0c;" wire:click="downloadInvoice">
                        <i class="bi bi-download me-2"></i>Download Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .container-fluid{
        background-color: #f5fdf1ff !important;
    }
    /* Header Styles */
    .header-section {
        border-bottom: 1px solid #e9ecef;
    }

    .company-logo {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .admin-avatar {
        font-size: 0.9rem;
    }

    .admin-details {
        line-height: 1.2;
    }

    /* Form Controls with Square Corners */
    .form-control,
    .form-select,
    .input-group-text,
    .btn {
        border-radius: 0 !important;
    }

    /* Search Results */
    .search-results {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0;
    }

    .search-results .p-3 {
        transition: background-color 0.2s ease;
    }

    .search-results .p-3:hover {
        background-color: #f5fdf1ff;
        ;
    }

    .table th {
        font-size: 0.875rem;
        font-weight: 600;
        background-color: #f8f9fa;
    }

    .form-control-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 0;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }

    .input-group-sm>.btn {
        padding: 0.25rem 0.5rem;
    }

    /* Modal Styles */
    .modal.show {
        backdrop-filter: blur(2px);
    }

    /* Sale Preview Styles */
    .sale-preview {
        background: white;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .sale-preview .header {
        border-bottom: 2px solid #3b5b0c;
        padding-bottom: 1rem;
    }

    .sale-preview table td {
        border: 1px solid #dee2e6;
    }

    /* Discount input styling */
    .text-danger.form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Cheque table styling */
    .table-sm td,
    .table-sm th {
        padding: 0.5rem;
        font-size: 0.875rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .input-group-sm {
            flex-wrap: nowrap;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .header-section .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .admin-details {
            text-align: center;
        }
    }

    /* Stock warning */
    .text-info small {
        font-size: 0.75rem;
    }

    /* Loading state for file upload */
    input[type="file"]:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Professional color scheme */
    .btn:hover {
        opacity: 0.9;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-close alerts after 5 seconds
    document.addEventListener('livewire:initialized', () => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });


    // Auto-hide alert after 3 seconds
        setTimeout(() => {
            const alert = document.getElementById('successAlert');
            if (alert) {
                // Add Bootstrap fade-out effect
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 500); // remove from DOM
            }
        }, 3000);
    // Prevent form submission on enter key in search
    document.addEventListener('keydown', function(e) {
        if (e.target.type === 'text' && e.target.getAttribute('wire:model') === 'search') {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        }
    });

    function handlePOSClick() {
        // Handle POS button click
        console.log('POS button clicked');
    }
</script>
@endpush