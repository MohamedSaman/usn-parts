<div>
    <div class="container-fluid p-4">
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm stat-card border-warning">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted text-uppercase">Awaiting Receipt</h6>
                            <h2 class="card-title fw-bold">{{ $purchaseOrders->where('status', 'complete')->count() }}</h2>
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
                            <h2 class="card-title fw-bold">{{ $purchaseOrders->whereNotNull('received_date')->count() }}</h2>
                        </div>
                        <div class="fs-1 text-success opacity-50"><i class="bi bi-archive-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Orders Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Goods Received Notes</h4>
                    <p class="text-muted mb-0 d-none d-sm-block">Process incoming orders from suppliers.</p>
                </div>
                <input type="text" class="form-control w-25" placeholder="Search PO # or supplier...">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Order Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $po)
                            <tr>
                                <td>{{ $po->order_code }}</td>
                                <td>{{ $po->supplier->name }}</td>
                                <td>{{ $po->order_date }}</td>
                                <td class="text-center">
                                    @if($po->status === 'complete')
                                    <button class="btn btn-sm btn-outline-primary"
                                        wire:click="openGRN({{ $po->id }})"
                                        data-bs-toggle="modal"
                                        data-bs-target="#grnModal">
                                        <i class="bi bi-check-lg me-1"></i> Process GRN
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
    </div>

    <!-- GRN Modal -->
    <div wire:ignore.self class="modal fade" id="grnModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create GRN for {{ $selectedPO?->order_code }}</h5>
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
                                    <th>Product</th>
                                    <th style="width: 100px;">Ordered Qty</th>
                                    <th style="width: 100px;">Received Qty</th>
                                    <th style="width: 100px;">Unit Price</th>
                                    <th style="width: 100px;">Discount</th>
                                    <th style="width: 100px;">Status</th>
                                    <th>Total</th>
                                    <th style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grnItems as $index => $item)
                                <tr wire:key="item-{{ $index }}"
                                    style="
        @if(strtolower($item['status'] ?? '') === 'received') background-color:#d1e7dd;
        @elseif(strtolower($item['status'] ?? '') === 'notreceived') background-color:#f8d7da;
        @endif
    ">
                                    <td>
                                        <input type="text" class="form-control product-search"
                                            wire:model.debounce.500ms="grnItems.{{ $index }}.name">
                                        @if(isset($searchResults[$index]) && $searchResults[$index])
                                        <ul class="list-group position-absolute z-3 w-100">
                                            @foreach($searchResults[$index] as $product)
                                            <li class="list-group-item list-group-item-action"
                                                wire:click="selectProduct({{ $index }}, {{ $product->id }})">
                                                {{ $product->name }}
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </td>
                                    <td>{{ $item['ordered_qty'] ?? 0 }}</td>
                                    <td><input type="number" class="form-control" wire:model="grnItems.{{ $index }}.received_qty"></td>
                                    <td><input type="text" class="form-control" wire:model="grnItems.{{ $index }}.unit_price"></td>
                                    <td><input type="text" class="form-control" wire:model="grnItems.{{ $index }}.discount"></td>
                                    <td>{{ ucfirst($item['status'] ?? 'pending') }}</td>
                                    <td>{{ ($item['received_qty'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['discount'] ?? 0) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger me-1"
                                            wire:click="deleteGRNItem({{ $index }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success"
                                            wire:click="correctGRNItem({{ $index }})">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-success" wire:click="addNewRow">
                            <i class="bi bi-plus-circle"></i> Add New Item
                        </button>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" wire:click="saveGRN">Save GRN</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.addEventListener('alert', event => {
            Swal.fire('Success', event.detail.message, 'success');
            var modalEl = document.getElementById('grnModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        });
    </script>
    @endpush
</div>