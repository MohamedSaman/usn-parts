<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daily Purchases Report</h5>
            <p class="text-muted mb-0 small">
                <i class="bi bi-calendar-event me-1"></i>
                {{ \Carbon\Carbon::parse($reportStartDate ?? now())->format('F d, Y') }}
            </p>
        </div>
        <div class="text-end">
            <div class="fs-4 fw-bold text-primary">Rs.{{ number_format($reportTotal, 2) }}</div>
            <small class="text-muted">Total Purchases</small>
        </div>
    </div>

    @if($reportData->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-inbox display-4 text-muted"></i>
        <p class="mt-3 text-muted">No purchase orders found for this date</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Order Code</th>
                    <th>Supplier</th>
                    <th>Order Date</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $order)
                <tr>
                    <td>
                        <span class="badge bg-primary">{{ $order->order_code }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $order->supplier->name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $order->supplier->businessname ?? '' }}</small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</td>
                    <td>{{ $order->items->count() }} items</td>
                    <td class="fw-bold">
                        Rs.{{ number_format($order->items->sum(function($item) {
                            return $item->quantity * $item->unit_price;
                        }), 2) }}
                    </td>
                    <td>
                        @if($order->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($order->status === 'received')
                            <span class="badge bg-success">Received</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4" class="text-end">Grand Total:</td>
                    <td>Rs.{{ number_format($reportTotal, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>