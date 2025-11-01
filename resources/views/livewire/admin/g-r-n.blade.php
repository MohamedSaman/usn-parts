<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-clipboard-check-fill text-primary me-2"></i> Goods Received Notes
            </h3>
            <p class="text-muted mb-0">Process and track incoming orders from suppliers</p>
        </div>
    </div>

    <div class="container-fluid p-4">
        <!-- Summary Cards -->
        <div class="row mb-2">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card summary-card awaiting h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-container bg-warning bg-opacity-10 me-3">
                                <i class="bi bi-box-seam text-warning fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Awaiting Receipt</p>
                                <h4 class="fw-bold mb-0">{{ $this->getOrderCounts()['complete'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card summary-card received h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-container bg-success bg-opacity-10 me-3">
                                <i class="bi bi-archive-fill text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Fully Received</p>
                                <h4 class="fw-bold mb-0">{{ $this->getOrderCounts()['received'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        
        </div>

        <!-- Purchase Orders Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-list-task text-primary me-2"></i> Purchase Orders
                    </h5>
                    <p class="text-muted small mb-0">Process incoming orders from suppliers</p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">PO Number</th>
                                <th>Supplier</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $po)
                            @php
                            // Calculate GRN progress
                            $totalItems = $po->items->count();
                            $receivedItems = $po->items->where('status', 'received')->count();
                            $notReceivedItems = $po->items->where('status', 'notreceived')->count();

                            $progressPercentage = $totalItems > 0 ? ($receivedItems / $totalItems) * 100 : 0;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-medium text-dark">{{ $po->order_code }}</span>
                                </td>
                                <td>{{ $po->supplier->name }}</td>
                                <td>{{ $po->order_date }}</td>
                                <td>
                                    @if($po->status == 'complete')
                                    <span class="badge bg-warning">Awaiting Receipt</span>
                                    @elseif($po->status == 'received')
                                    <span class="badge bg-success">Fully Received</span>
                                    @elseif($po->status == 'pending')
                                    <span class="badge bg-secondary">Pending</span>
                                    @else
                                    <span class="badge bg-info">{{ ucfirst($po->status) }}</span>
                                    @endif
                                </td>
                                
                                <td class="text-end pe-4">
                                    <button class="btn btn-link text-info p-0"
                                        wire:click="viewGRN({{ $po->id }})"
                                        title="View GRN Details">
                                        <i class="bi bi-eye fs-5"></i>
                                    </button>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <!-- View GRN Details Modal -->
    <div wire:ignore.self class="modal fade" id="viewGrnModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-eye text-info me-2"></i> GRN Details - {{ $selectedPO?->order_code }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedPO)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Supplier:</strong> {{ $selectedPO->supplier->name }}</p>
                            <p><strong>Order Date:</strong> {{ $selectedPO->order_date }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Received Date:</strong> {{ $selectedPO->received_date ?? 'N/A' }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-success">{{ ucfirst($selectedPO->status) }}</span>
                            </p>
                        </div>
                    </div>

                    <h5>Received Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Ordered Qty</th>
                                    <th>Received Qty</th>
                                    <th>Supplier Price</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grnItems as $item)
                                <tr class="
                                    @if(strtolower($item['status'] ?? '') === 'received') table-success
                                    @elseif(strtolower($item['status'] ?? '') === 'notreceived') table-danger
                                    @endif
                                ">
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['ordered_qty'] ?? 0 }}</td>
                                    <td>{{ $item['received_qty'] ?? 0 }}</td>
                                    <td>Rs. {{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                    <td>Rs. {{ number_format($item['discount'] ?? 0, 2) }}</td>
                                    <td>Rs. {{ number_format(($item['received_qty'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['discount'] ?? 0), 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            @if(strtolower($item['status'] ?? '') === 'received') bg-success
                                            @elseif(strtolower($item['status'] ?? '') === 'notreceived') bg-danger
                                            @else bg-warning
                                            @endif
                                        ">
                                            {{ ucfirst($item['status'] ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
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

        .summary-card.awaiting {
            border-left-color: #ffc107;
        }

        .summary-card.received {
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
            padding: 1rem 0.75rem;
        }

        .summary-card.total {
            border-left-color: #4361ee;
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

        /* Product Search Dropdown Styling */
        .product-search {
            position: relative;
        }

        .list-group-item-action:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .list-group {
            border-radius: 8px;
            overflow: hidden;
        }

        .list-group-item {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #e3f2fd;
            transform: translateX(5px);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Handle success alerts
        window.addEventListener('alert', event => {
            Swal.fire('Success', event.detail.message, 'success');
            var modalEl = document.getElementById('grnModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
        });

        // Open GRN modal after data is loaded
        Livewire.on('open-grn-modal', () => {
            var modalEl = document.getElementById('grnModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });

        // Open View GRN modal after data is loaded
        Livewire.on('open-view-grn-modal', () => {
            var modalEl = document.getElementById('viewGrnModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    </script>
    @endpush
</div>