<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Product Stock Report</h5>
            <p class="text-muted mb-0 small">
                <i class="bi bi-calendar-event me-1"></i>As of {{ now()->format('F d, Y h:i A') }}
            </p>
        </div>
    </div>

    <!-- Stock Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 small">Total Products</h6>
                            <h3 class="mb-0 fw-bold">{{ $reportStats['total_products'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-box-seam fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 small">Total Stock</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($reportStats['total_stock'] ?? 0) }}</h3>
                        </div>
                        <i class="bi bi-boxes fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 small">Available Stock</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ number_format($reportStats['available_stock'] ?? 0) }}</h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 small">Low Stock Items</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $reportStats['low_stock'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($reportData->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-inbox display-4 text-muted"></i>
        <p class="mt-3 text-muted">No stock data available</p>
    </div>
    @else
    <!-- Low Stock Alert -->
    @if($reportStats['low_stock'] > 0)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            <strong>Low Stock Alert!</strong> {{ $reportStats['low_stock'] }} product(s) are running low on stock.
        </div>
    </div>
    @endif

    <!-- Stock Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Total Stock</th>
                    <th>Available</th>
                    <th>Sold</th>
                    <th>Damaged</th>
                    <th>Stock Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $stock)
                @php
                    $stockPercentage = $stock->total_stock > 0 ? ($stock->available_stock / $stock->total_stock) * 100 : 0;
                    $statusColor = $stockPercentage > 50 ? 'success' : ($stockPercentage > 20 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($stock->product->image)
                            <img src="{{ asset($stock->product->image) }}" 
                                 class="rounded me-2" 
                                 width="40" 
                                 height="40" 
                                 style="object-fit: cover;"
                                 alt="Product">
                            @else
                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $stock->product->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $stock->product->code ?? '' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $stock->product->brand->brand_name ?? '-' }}</td>
                    <td>{{ $stock->product->category->category_name ?? '-' }}</td>
                    <td class="fw-bold">{{ number_format($stock->total_stock) }}</td>
                    <td>
                        <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                            {{ number_format($stock->available_stock) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ number_format($stock->sold_count) }}</span>
                    </td>
                    <td>
                        @if($stock->damage_stock > 0)
                            <span class="badge bg-danger">{{ number_format($stock->damage_stock) }}</span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td style="width: 120px;">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $statusColor }}" 
                                 role="progressbar" 
                                 style="width: {{ $stockPercentage }}%"
                                 aria-valuenow="{{ $stockPercentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">{{ number_format($stockPercentage, 1) }}%</small>
                    </td>
                    <td>
                        @if($stock->available_stock == 0)
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i>Out of Stock
                            </span>
                        @elseif($stock->available_stock < 10)
                            <span class="badge bg-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>Low Stock
                            </span>
                        @else
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>In Stock
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Stock Distribution Chart -->
    <div class="row g-3 mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Stock Status Distribution</h6>
                    @php
                        $inStock = $reportData->where('available_stock', '>', 10)->count();
                        $lowStock = $reportData->where('available_stock', '>', 0)->where('available_stock', '<=', 10)->count();
                        $outOfStock = $reportData->where('available_stock', 0)->count();
                        $total = $reportData->count();
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-success">
                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>In Stock
                            </span>
                            <span class="fw-semibold">{{ $inStock }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $total > 0 ? ($inStock / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-warning">
                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Low Stock
                            </span>
                            <span class="fw-semibold">{{ $lowStock }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-warning" 
                                 style="width: {{ $total > 0 ? ($lowStock / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-danger">
                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Out of Stock
                            </span>
                            <span class="fw-semibold">{{ $outOfStock }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" 
                                 style="width: {{ $total > 0 ? ($outOfStock / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Top 5 Low Stock Products</h6>
                    @foreach($reportData->sortBy('available_stock')->take(5) as $item)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-semibold small">{{ $item->product->name }}</div>
                            <small class="text-muted">{{ $item->product->code }}</small>
                        </div>
                        <span class="badge bg-warning">{{ $item->available_stock }} units</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>