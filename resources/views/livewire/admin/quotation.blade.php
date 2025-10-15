<div>
    <div class="container-fluid p-4">
        {{-- Stats --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-primary">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase">Pending Orders</h6>
                            <h2>{{ $pendingCount }}</h2>
                        </div>
                        <div class="fs-1 text-primary opacity-50"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-success">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase">Completed Orders</h6>
                            <h2>{{ $completedCount }}</h2>
                        </div>
                        <div class="fs-1 text-success opacity-50"><i class="bi bi-patch-check-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Purchase Orders</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseOrderModal">
                    <i class="bi bi-plus-lg me-1"></i> New Purchase Order
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order Code</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $order->status == 'complete' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"
                                    wire:click="viewOrder({{ $order->id }})"
                                    data-bs-toggle="modal" data-bs-target="#viewOrderModal">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if($order->status != 'complete')
                                <button class="btn btn-outline-success btn-sm"
                                    wire:click="confirmComplete({{ $order->id }})">
                                    <i class="bi bi-check2"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Order Modal --}}
    <div wire:ignore.self class="modal fade" id="addPurchaseOrderModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Create New Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Supplier</label>
                            <select class="form-select" wire:model="supplier_id">
                                <option value="">Choose supplier...</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Search Product</label>
                            <input type="text" class="form-control" wire:model.live="search" placeholder="Type product name...">
                            @if(!empty($products))
                            <ul class="list-group mt-1">
                                @foreach($products as $product)
                                <li class="list-group-item list-group-item-action"
                                    wire:click="selectProduct({{ $product->id }})">
                                    {{ $product->name }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>

                    @if($selectedProduct)
                    <div class="row align-items-end bg-light p-3 rounded border mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Selected Product</label>
                            <input type="text" class="form-control" value="{{ $selectedProduct->name }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" wire:model="quantity" min="1">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success w-100" wire:click="addItem">
                                <i class="bi bi-plus-circle me-1"></i> Add to PO
                            </button>
                        </div>
                    </div>
                    @endif

                    <h5>Order Items</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $index }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No items added</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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

    {{-- View Order Modal --}}
    <div wire:ignore.self class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="viewOrderModalLabel">
                        Order Details - {{ $selectedOrder?->order_code }}
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
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedOrder->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit_price }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>