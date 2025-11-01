<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-cart-check-fill text-primary me-2"></i> Purchase Order Management
            </h3>
            <p class="text-muted mb-0">Create and manage purchase orders from suppliers</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseOrderModal">
                <i class="bi bi-plus-circle me-2"></i> New Purchase Order
            </button>
        </div>
    </div>

    <div class="container-fluid p-4">
        {{-- Summary Cards --}}
        <div class="row mb-2">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card summary-card pending h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-container bg-warning bg-opacity-10 me-3">
                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Pending Orders</p>
                                <h4 class="fw-bold mb-0">{{ $pendingCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card summary-card completed h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-container bg-success bg-opacity-10 me-3">
                                <i class="bi bi-patch-check-fill text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Completed Orders</p>
                                <h4 class="fw-bold mb-0">{{ $completedCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-list-check text-primary me-2"></i> Purchase Orders
                    </h5>
                    <p class="text-muted small mb-0">View and manage all purchase orders</p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order Code</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>GRN Status</th>
                                <th>Quantity</th>
                                <th>Supplier Price</th>
                                <th>Total Amount</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            @php
                            // Calculate totals for this order
                            $totalQuantity = $order->items->sum('quantity');
                            $totalAmount = $order->items->sum(function($item) {
                            return $item->quantity * $item->unit_price;
                            });

                            // Calculate GRN status
                            $totalItems = $order->items->count();
                            $receivedItems = $order->items->where('status', 'received')->count();
                            $notReceivedItems = $order->items->where('status', 'notreceived')->count();
                            $pendingItems = $order->items->whereNotIn('status', ['received', 'notreceived'])->count();

                            $grnStatus = 'Pending';
                            $grnBadge = 'bg-warning';
                            if ($receivedItems > 0 && $notReceivedItems == 0 && $pendingItems == 0) {
                            $grnStatus = 'Completed';
                            $grnBadge = 'bg-success';
                            } elseif ($notReceivedItems > 0) {
                            $grnStatus = 'Partial';
                            $grnBadge = 'bg-danger';
                            } elseif ($receivedItems > 0) {
                            $grnStatus = 'In Progress';
                            $grnBadge = 'bg-info';
                            }
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-medium text-dark">{{ $order->order_code }}</span>
                                </td>
                                <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                <td>
                                    @if($order->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($order->status == 'complete')
                                    <span class="badge bg-success">Completed</span>
                                    @elseif($order->status == 'received')
                                    <span class="badge bg-success">Received</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $grnBadge }}">
                                        {{ $grnStatus }}
                                        @if($grnStatus != 'Pending')
                                        ({{ $receivedItems }}/{{ $totalItems }})
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $totalQuantity }}</td>
                                <td>{{ number_format($order->items->sum('unit_price'), 2) }}</td>
                                <td>{{ number_format($totalAmount, 2) }}</td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-gear-fill"></i> Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <!-- View Order -->
                                            <li>
                                                <button class="dropdown-item" wire:click="viewOrder({{ $order->id }})">
                                                    <i class="bi bi-eye text-primary me-2"></i> View
                                                </button>
                                            </li>



                                            @if($order->status == 'pending')
                                            <!-- Convert to GRN -->
                                            <li>
                                                <button class="dropdown-item" wire:click="convertToGRN({{ $order->id }})">
                                                    <i class="bi bi-arrow-repeat text-info me-2"></i> Process GRN
                                                </button>
                                            </li>

                                            <!-- Mark Complete -->
                                            <!-- <li>
                                                <button class="dropdown-item" wire:click="confirmComplete({{ $order->id }})">
                                                    <i class="bi bi-check-circle text-success me-2"></i> Mark Complete
                                                </button>
                                            </li> -->

                                            <!-- Edit Order -->
                                            <li>
                                                <button class="dropdown-item" wire:click="editOrder({{ $order->id }})">
                                                    <i class="bi bi-pencil-square text-warning me-2"></i> Edit
                                                </button>
                                            </li>

                                            <!-- Cancel Order -->
                                            <li>
                                                <button class="dropdown-item" wire:click="confirmDelete({{ $order->id }})">
                                                    <i class="bi bi-x-circle text-danger me-2"></i> Cancel Order
                                                </button>
                                            </li>
                                            @endif

                                            <!-- Download PDF -->
                                            <li>
                                                <button class="dropdown-item" wire:click="downloadPDF({{ $order->id }})">
                                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Download PDF
                                                </button>
                                            </li>

                                            <!-- Delete Order (for all statuses or only completed) -->
                                            <li>
                                                <button class="dropdown-item" wire:click="confirmPermanentDelete({{ $order->id }})">
                                                    <i class="bi bi-trash text-danger me-2"></i> Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Order Modal --}}
    <div wire:ignore.self class="modal fade" id="addPurchaseOrderModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Create New Purchase Order
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Supplier</label>
                            <select class="form-select" wire:model="supplier_id">
                                <option value="">Choose supplier...</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Search Product</label>
                            <input type="text" class="form-control" wire:model.live="search" placeholder="Search by name or code...">
                            @if(!empty($products) && count($products) > 0)
                            <ul class="list-group mt-1 position-absolute w-100 me-4 z-3 shadow-lg">
                                @foreach($products as $product)
                                <li class="list-group-item list-group-item-action p-2"
                                    wire:click="selectProduct({{ $product->id }})"
                                    style="cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $product->image }}"
                                            alt="{{ $product->name }}"
                                            class="me-2"
                                            style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                            <small class="text-muted">
                                                Code: <span class="badge bg-secondary">{{ $product->code }}</span>
                                                | Stock: <span class="badge {{ ($product->stock->total_stock ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $product->stock->total_stock ?? 0 }} units
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>

                    @if($selectedProduct)
                    <div class="row align-items-end bg-light p-3 rounded border mb-3">
                        <!-- //add prodcut code -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Product Code</label>
                            <input type="text" class="form-control" value="{{ $selectedProduct->code }}" readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Selected Product</label>
                            <input type="text" class="form-control" value="{{ $selectedProduct->name }}" readonly>
                        </div>


                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Quantity</label>
                            <input type="number" class="form-control"
                                wire:model.live="quantity"
                                min="1">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Supplier Price</label>
                            <input type="text" class="form-control" readonly
                                value="{{ number_format($selectedProductPrice, 2) }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Total Price</label>
                            <input type="text" class="form-control" readonly
                                value="{{ number_format($totalPrice, 2) }}">
                        </div>

                        <div class="col-md-12 mt-3">
                            <button class="btn btn-success w-100" wire:click="addItem">
                                <i class="bi bi-plus-circle me-1"></i> Add Item
                            </button>
                        </div>
                    </div>
                    @endif

                    <h5>Order Items</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Code</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Supplier Price</th>
                                <th>Total Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['code'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ number_format($item['supplier_price'], 2) }}</td>
                                <td>{{ number_format($item['total_price'], 2) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $index }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No items added</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Grand Total Section -->
                    @if(count($orderItems) > 0)
                    <div class="row justify-content-end mt-3">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark">Grand Total:</span>
                                        <span class="fw-bold fs-5 text-primary">
                                            {{ number_format($grandTotal, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="saveOrder">
                        <i class="bi bi-save me-1"></i> Save Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- GRN Modal --}}
    <div wire:ignore.self class="modal fade" id="grnModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-clipboard-check text-primary me-2"></i> Create GRN for {{ $selectedPO?->order_code }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedPO)
                    <p><strong>Supplier:</strong> {{ $selectedPO->supplier->name }}</p>

                    <h5>Received Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Code</th>
                                    <th style="width: 120px;">Product</th>
                                    <th style="width: 80px;">Ordered Qty</th>
                                    <th style="width: 80px;">Received Qty</th>
                                    <th style="width: 100px;">Supplier Price</th>
                                    <th style="width: 150px;">Discount</th>
                                    <th style="width: 80px;">Cost</th>
                                    <th style="width: 80px;">Total</th>
                                    <th style="width: 50px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grnItems as $index => $item)
                                @php
                                $statusClass = '';
                                $statusText = 'Pending';
                                $statusBadge = 'bg-warning';
                                if (strtolower($item['status'] ?? '') === 'received') {
                                $statusClass = 'table-success';
                                $statusText = 'Received';
                                $statusBadge = 'bg-success';
                                } elseif (strtolower($item['status'] ?? '') === 'notreceived') {
                                $statusClass = 'table-danger';
                                $statusText = 'Not Received';
                                $statusBadge = 'bg-danger';
                                }
                                @endphp
                                <tr wire:key="item-{{ $index }}" class="{{ $statusClass }}">
                                    <td>
                                        <input type="text"
                                            class="form-control"
                                            wire:model.live="grnItems.{{ $index }}.code"
                                            placeholder="Product Code"
                                            {{ !($item['is_new'] ?? false) ? 'readonly' : '' }}>

                                    </td>
                                    <td class="position-relative">
                                        @if($item['is_new'] ?? false)
                                        <input type="text"
                                            class="form-control"
                                            wire:model.live="grnItems.{{ $index }}.name"
                                            placeholder="New Product Name">
                                        @else
                                        <input type="text"
                                            class="form-control product-search"
                                            wire:model.live="grnItems.{{ $index }}.name"
                                            placeholder="Search by name or code...">
                                        @if(isset($searchResults[$index]) && count($searchResults[$index]) > 0)
                                        <ul class="list-group position-absolute z-10 shadow-lg mt-1" style="min-width: 350px; max-width: 450px; left: 0;">
                                            @foreach($searchResults[$index] as $product)
                                            <li class="list-group-item list-group-item-action p-2"
                                                wire:click="selectGRNProduct({{ $index }}, {{ $product->id }})"
                                                style="cursor: pointer;">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $product->image }}"
                                                        alt="{{ $product->name }}"
                                                        class="me-2"
                                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                                        <small class="text-muted">
                                                            Code: <span class="badge bg-secondary">{{ $product->code }}</span>
                                                            | Stock: <span class="badge {{ ($product->stock->total_stock ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $product->stock->total_stock ?? 0 }} units
                                                            </span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item['ordered_qty'] ?? 0 }}</td>
                                    <td>
                                        <input type="number"
                                            class="form-control text-center"
                                            wire:model.live="grnItems.{{ $index }}.received_qty"
                                            min="0"
                                            wire:change="calculateGRNTotal({{ $index }})">
                                    </td>
                                    <td>
                                        <input type="number"
                                            class="form-control text-end"
                                            wire:model.live="grnItems.{{ $index }}.unit_price"
                                            step="0.01"
                                            min="0"
                                            wire:change="calculateGRNTotal({{ $index }})"
                                            placeholder="0.00">
                                    </td>
                                    <td>
                                        <div class="discount-container">
                                            <div class="input-group input-group-sm">
                                                <input type="number"
                                                    class="form-control"
                                                    wire:model.live="grnItems.{{ $index }}.discount"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ ($grnItems[$index]['discount_type'] ?? 'rs') === 'percent' ? 100 : '' }}"
                                                    wire:change="calculateGRNTotal({{ $index }})"
                                                    placeholder="0.00">
                                                <div class="input-group-append d-flex align-items-center">
                                                    <button type="button"
                                                        class="btn btn-sm {{ ($grnItems[$index]['discount_type'] ?? 'rs') === 'rs' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                        wire:click="setDiscountType({{ $index }}, 'rs')"
                                                        title="Rupees">
                                                        Rs.
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm {{ ($grnItems[$index]['discount_type'] ?? 'rs') === 'percent' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                        wire:click="setDiscountType({{ $index }}, 'percent')"
                                                        title="Percentage">
                                                        %
                                                    </button>
                                                </div>
                                            </div>
                                            @if(($grnItems[$index]['discount_type'] ?? 'rs') === 'percent' && floatval($grnItems[$index]['discount'] ?? 0) > 0)
                                            <small class="text-muted d-block mt-1">
                                                Rs. {{ number_format($this->calculateDiscountAmount($index), 2) }}
                                            </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold text-success">
                                        {{ number_format($this->calculateCost($index), 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format($this->calculateGRNTotal($index), 2) }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                                            <!-- <span class="badge {{ $statusBadge }} mb-2">
                                                {{ $statusText }}
                                            </span> -->
                                            <button class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteGRNItem({{ $index }})"
                                                title="Remove Item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success"
                                                wire:click="correctGRNItem({{ $index }})"
                                                title="Mark as Received">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-success" wire:click="addNewRow">
                                <i class="bi bi-plus-circle"></i> Add New Item
                            </button>
                        </div>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark pr-10">Grand Total:</span>
                                    <span class="fw-bold fs-5 text-primary">
                                        {{ number_format($this->grnGrandTotal, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveGRN">Save GRN</button>
                    </div>

                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- View Order Modal --}}
<div wire:ignore.self class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-eye text-primary me-2"></i> Order Details - {{ $selectedOrder?->order_code }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($selectedOrder)
                <p><strong>Supplier:</strong> {{ $selectedOrder->supplier->name ?? 'N/A' }}</p>
                <p><strong>Order Date:</strong> {{ $selectedOrder->order_date }}</p>
                <p><strong>Received Date:</strong> {{ $selectedOrder->received_date ?? '-' }}</p>

                <h6>Items</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>totalPrice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedOrder->items as $item)
                        <tr>
                            <td>{{ $item->product->code ?? 'N/A' }}</td>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>
                                @if($item->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @elseif($item->status == 'received')
                                <span class="badge bg-success">Received</span>
                                @elseif($item->status == 'notreceived')
                                <span class="badge bg-danger">Not Received</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_price }}</td>
                            <td>{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark pr-10">Grand Total:</span>
                            <span class="fw-bold fs-5 text-primary">
                                {{ number_format($this->viewOrderTotal, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Edit Order Modal --}}
<div wire:ignore.self class="modal fade" id="editOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($editOrderItems as $index => $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" min="1" wire:model.defer="editOrderItems.{{ $index }}.quantity">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" step="0.01" wire:model.defer="editOrderItems.{{ $index }}.unit_price">
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="removeEditItem({{ $index }})">Remove</button>
                                </td>
                            </tr>
                            @endforeach
                            @if(empty($editOrderItems))
                            <tr>
                                <td colspan="4" class="text-center text-muted">No items to edit</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" wire:click="updateOrder">Save Changes</button>
            </div>
        </div>
    </div>
</div>

</div>

@push('styles')
<style>
    .discount-container {
        min-width: 120px;
    }

    .input-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .input-group-sm .form-control {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .input-group-append .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .input-group-append .btn-outline-secondary {
        border-color: #ced4da;
    }

    .text-end {
        text-align: right !important;
    }

    .text-center {
        text-align: center !important;
    }

    .table th {
            border-top: none;
            font-weight: 600;
            color: #ffffff;
            background: #3B5B0C;
            background: linear-gradient(0deg,rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    .table td {
        vertical-align: middle;
        padding: 0.5rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .input-group-append .btn.active {
        background-color: #4361ee;
        color: white;
        border-color: #4361ee;
    }

    .input-group-append .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .input-group-append .btn:hover {
        background-color: #e9ecef;
        border-color: #4361ee;
        color: #4361ee;
    }

    /* Add to your existing styles */
    .table tfoot {
        border-top: 2px solid #dee2e6;
    }

    .table tfoot td {
        font-size: 1.1em;
        background-color: #f8f9fa;
    }

    .text-end {
        text-align: right !important;
    }

    .fw-bold.text-primary {
        color: #4361ee !important;
    }

    .table-success {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .table-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .dropdown-toggle::after {
        margin-left: 0.5em;
    }

    .dropdown-menu {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.1);
        position: absolute;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item:active {
        background-color: #e9ecef;
    }

    .table-responsive {
        overflow: visible;
    }

    .dropdown {
        position: static;
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        z-index: 1000;
    }

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

    .summary-card.pending {
        border-left-color: #ffc107;
    }

    .summary-card.completed {
        border-left-color: #28a745;
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

    

    .table td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
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

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        border-color: #4361ee;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.50rem 0.75rem;
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

    /* Product Search Dropdown Styling */
    .list-group {
        border-radius: 8px;
        overflow: hidden;
        max-height: 400px;
        overflow-y: auto;
    }

    .list-group-item {
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .list-group-item:hover {
        background-color: #e3f2fd;
        transform: translateX(5px);
    }

    .list-group-item-action:hover {
        background-color: #f0f7ff;
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
    // No additional event listeners needed - modals are opened directly via $this->js() in the component
</script>
@endpush