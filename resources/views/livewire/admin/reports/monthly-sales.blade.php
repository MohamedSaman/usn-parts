<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Sales Report</h5>
            <p class="text-muted mb-0 small">
                @if($reportStartDate && $reportEndDate)
                    Period: {{ \Carbon\Carbon::parse($reportStartDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($reportEndDate)->format('M d, Y') }}
                @elseif($reportStartDate)
                    From: {{ \Carbon\Carbon::parse($reportStartDate)->format('M d, Y') }}
                @else
                    All Time
                @endif
            </p>
        </div>
        <div class="text-end">
            <div class="fs-4 fw-bold text-primary">Rs.{{ number_format($totalSales ?? 0, 2) }}</div>
            <small class="text-muted">Total Sales</small>
        </div>
    </div>

    

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-hover custom-table mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th class="text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesReport as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number ?? 'INV-' . $sale->id }}</td>
                        <td>{{ optional($sale->created_at)->format('Y-m-d') }}</td>
                        <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ $sale->items->sum('quantity') }} items</td>
                        <td class="text-end">Rs.{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No sales found for this month.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($salesReport->isNotEmpty())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Grand Total:</td>
                        <td class="text-end">Rs.{{ number_format($totalSales, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Category chart data
            @php
                $catLabels = [];
                $catTotals = [];
                if (!empty($categoryWiseData)) {
                    foreach ($categoryWiseData as $cat => $vals) {
                        $catLabels[] = $cat;
                        $catTotals[] = $vals['total'] ?? ($vals['total_amount'] ?? 0);
                    }
                }
            @endphp

            const catLabels = @json($catLabels);
            const catTotals = @json($catTotals);

            const catCtx = document.getElementById('categorySalesChart');
            if (catCtx) {
                new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            data: catTotals,
                            backgroundColor: [
                                '#667eea','#764ba2','#4fd1c5','#f6ad55','#f56565','#48bb78'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }

            // Payment method chart
            @php
                $pmLabels = $paymentMethodData->pluck('payment_method')->toArray() ?? [];
                $pmTotals = $paymentMethodData->pluck('total')->toArray() ?? [];
            @endphp
            const pmLabels = @json($pmLabels);
            const pmTotals = @json($pmTotals);

            const pmCtx = document.getElementById('paymentMethodChart');
            if (pmCtx) {
                new Chart(pmCtx, {
                    type: 'bar',
                    data: {
                        labels: pmLabels,
                        datasets: [{
                            label: 'Amount',
                            data: pmTotals,
                            backgroundColor: '#63b3ed'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        });
    </script>
    @endpush
</div>
