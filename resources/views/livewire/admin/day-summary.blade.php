<div class="container-fluid py-4" style="background-color:#f5fdf1ff;">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold" style="color:#3b5b0c;">
                                <i class="bi bi-calendar-check me-2" style="color:#8eb922;"></i>Day Summary List
                            </h4>
                            <small class="text-muted">View all closed POS sessions</small>
                        </div>
                        <div>
                            <button class="btn btn-sm rounded-0" style="background: linear-gradient(0deg, rgba(59, 91, 12, 1) 0%, rgba(142, 185, 34, 1) 100%); color:white;" wire:click="resetFilters">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-semibold" style="color:#3b5b0c;">
                        <i class="bi bi-search me-1"></i>Search
                    </label>
                    <input type="text" class="form-control rounded-0" wire:model.live="search" placeholder="Search by date or user...">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-semibold" style="color:#3b5b0c;">
                        <i class="bi bi-calendar-event me-1"></i>From Date
                    </label>
                    <input type="date" class="form-control rounded-0" wire:model.live="dateFrom">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="form-label fw-semibold" style="color:#3b5b0c;">
                        <i class="bi bi-calendar-event me-1"></i>To Date
                    </label>
                    <input type="date" class="form-control rounded-0" wire:model.live="dateTo">
                </div>
            </div>
        </div>
    </div>

    {{-- Sessions List --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold" style="color:#3b5b0c;">
                        <i class="bi bi-list-ul me-2" style="color:#8eb922;"></i>POS Sessions ({{ $sessions->total() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($sessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color:#f8f9fa;">
                                <tr>
                                    <th class="border-0 py-3">#</th>
                                    <th class="border-0 py-3">Session Date</th>
                                    <th class="border-0 py-3">Cashier</th>
                                    <th class="border-0 py-3">Opening Cash</th>
                                    <th class="border-0 py-3">Closing Cash</th>
                                    <th class="border-0 py-3">Total Sales</th>
                                    <th class="border-0 py-3">Cash Sales</th>
                                    <th class="border-0 py-3">Expenses</th>
                                    <th class="border-0 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $index => $session)
                                <tr wire:click="viewDetails({{ $session->id }})"
                                    style="cursor: pointer; transition: background-color 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f0f8f0';"
                                    onmouseout="this.style.backgroundColor='';">
                                    <td>{{ $sessions->firstItem() + $index }}</td>
                                    <td>
                                        <strong style="color:#3b5b0c;">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($session->session_date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <i class="bi bi-person-circle me-1" style="color:#8eb922;"></i>
                                        {{ $session->user->name ?? 'N/A' }}
                                    </td>
                                    <td>Rs.{{ number_format($session->opening_cash, 2) }}</td>
                                    <td>Rs.{{ number_format($session->closing_cash, 2) }}</td>
                                    <td>
                                        <strong style="color:#3b5b0c;">Rs.{{ number_format($session->total_sales, 2) }}</strong>
                                    </td>
                                    <td>Rs.{{ number_format($session->cash_sales, 2) }}</td>
                                    <td>Rs.{{ number_format($session->expenses, 2) }}</td>
                                    <td>
                                        <span class="badge bg-success rounded-0">
                                            <i class="bi bi-check-circle me-1"></i>{{ ucfirst($session->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="card-footer bg-white">
                        {{ $sessions->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="text-muted mt-3">No closed sessions found</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control,
    .btn {
        border-radius: 0 !important;
    }

    .table tbody tr {
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }
</style>
@endpush