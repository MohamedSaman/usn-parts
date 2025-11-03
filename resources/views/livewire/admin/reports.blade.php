<div>
    @push('styles')
    <style>
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .btn-outline-primary,
        .btn-outline-secondary {
            font-size: 0.8rem;
            font-weight: 500;
            border-radius: 6px;
            padding: 0.3rem 0.7rem;
            transition: all 0.15s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* MOBILE RESPONSIVE STYLES */
        @media (max-width: 768px) {
            .stat-card {
                padding: 12px;
                margin-bottom: 15px;
            }

            .stat-value {
                font-size: 20px !important;
            }

            .content-tab {
                padding: 8px 12px !important;
                white-space: nowrap;
            }

            .widget-container {
                padding: 15px;
            }

            .item-row {
                flex-wrap: wrap;
            }

            .item-details {
                width: 100%;
                margin-bottom: 5px;
            }

            .avatar {
                width: 32px;
                height: 32px;
                margin-right: 10px;
            }

            .amount {
                font-size: 13px;
            }
        }

        @media (max-width: 576px) {
            .content-tabs {
                margin-bottom: 15px;
            }

            .stat-card {
                padding: 10px;
            }

            .stat-value {
                font-size: 18px !important;
            }

            .status-badge {
                padding: 0.15rem 0.35rem;
                font-size: 0.7rem;
            }

            .widget-header h6 {
                font-size: 1rem;
            }

            .widget-header p {
                font-size: 0.75rem;
            }

            .item-row {
                align-items: flex-start;
            }

            .item-details h6 {
                font-size: 0.9rem;
            }

            .d-flex-mobile-column {
                flex-direction: column !important;
            }

            .justify-content-mobile-between {
                justify-content: space-between !important;
            }

            .mb-mobile-2 {
                margin-bottom: 0.5rem !important;
            }

            .w-mobile-100 {
                width: 100% !important;
            }

            .text-truncate-mobile {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
            }
        }
    </style>
    @endpush

    <!-- Reports Content -->
    <div class="container-fluid p-0">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="bi bi-file-earmark-bar-graph text-success me-2"></i>Reports Dashboard</h4>
                        <p class="text-muted mb-0">Generate and export various business reports</p>
                    </div>
                    <!-- Download Excel Button - Only show after report is generated -->
                    @if($selectedReport && count($currentReportData) > 0)
                    <div class="d-flex gap-2">
                        <button wire:click="downloadReport" class="btn btn-success">
                            <i class="bi bi-download"></i> Download Excel
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Report Filters -->
        <div class="mb-3 card p-3 shadow-sm border-0">
            <div class="row g-2 align-items-end">
                <div class="col-md-3 col-12">
                    <label class="form-label fw-semibold">Report Type</label>
                    <select class="form-select" wire:model="selectedReport">
                        <option value="">-- Select Report --</option>
                        <option value="sales">Sales Report</option>
                        <option value="salary">Salary Report</option>
                        <option value="inventory">Inventory Report</option>
                        <option value="staff">Staff Report</option>
                        <option value="payments">Payments Report</option>
                        <option value="attendance">Attendance Report</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" class="form-control" wire:model="reportStartDate">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" class="form-control" wire:model="reportEndDate">
                    @error('reportEndDate')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3 col-12 d-grid">
                    <button class="btn btn-primary mt-md-4 mt-2" wire:click="generateReport">
                        <i class="bi bi-file-earmark-bar-graph"></i> Generate Report
                    </button>
                    <button class="btn btn-outline-secondary mt-2" wire:click="clearFilters">
                        <i class="bi bi-arrow-clockwise"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Summary -->
        @if($selectedReport && count($currentReportData) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="stat-card">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-label">Report Type</div>
                            <div class="stat-value text-primary">{{ $reportTitle }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-label">Total Records</div>
                            <div class="stat-value">{{ number_format(count($currentReportData)) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-label">
                                @if(in_array($selectedReport, ['sales', 'salary', 'payments', 'staff']))
                                    Total Amount
                                @elseif($selectedReport === 'inventory')
                                    Total Stock
                                @else
                                    Total Records
                                @endif
                            </div>
                            <div class="stat-value text-success">
                                @if(in_array($selectedReport, ['sales', 'salary', 'payments', 'staff']))
                                    Rs.{{ number_format($currentReportTotal, 2) }}
                                @else
                                    {{ number_format($currentReportTotal) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">
                        @if($reportStartDate && $reportEndDate)
                            Period: {{ \Carbon\Carbon::parse($reportStartDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($reportEndDate)->format('M d, Y') }}
                        @else
                            All Time Data
                        @endif
                        • Generated: {{ now()->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Report Content -->
        <div>
            @if($selectedReport === 'sales')
                @include('livewire.admin.reports.sales-report', ['data' => $currentReportData, 'salesReportTotal' => $currentReportTotal])
            @elseif($selectedReport === 'salary')
                @include('livewire.admin.reports.salary-report', ['data' => $currentReportData, 'salaryReportTotal' => $currentReportTotal])
            @elseif($selectedReport === 'inventory')
                @include('livewire.admin.reports.inventory-report', ['data' => $currentReportData, 'inventoryReportTotal' => $currentReportTotal])
            @elseif($selectedReport === 'staff')
                @include('livewire.admin.reports.staff-report', ['data' => $currentReportData, 'staffReportTotal' => $currentReportTotal])
            @elseif($selectedReport === 'payments')
                @include('livewire.admin.reports.payments-report', ['data' => $currentReportData, 'paymentsReportTotal' => $currentReportTotal])
            @elseif($selectedReport === 'attendance')
                @include('livewire.admin.reports.attendance-report', ['data' => $currentReportData, 'attendanceReportTotal' => $currentReportTotal])
            @else
                <div class="alert alert-info text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="bi bi-file-earmark-text display-4"></i>
                    </div>
                    <h4 class="text-muted">Welcome to Reports</h4>
                    <p class="text-muted">Select a report type and date range to generate detailed reports.</p>
                </div>
            @endif
        </div>

        <!-- Empty State -->
        @if($selectedReport && count($currentReportData) === 0)
        <div class="alert alert-warning text-center py-5">
            <div class="text-warning mb-3">
                <i class="bi bi-search display-4"></i>
            </div>
            <h4 class="text-warning">No Data Found</h4>
            <p class="text-warning">No records found for the selected criteria. Try adjusting your filters.</p>
            <button wire:click="clearFilters" class="btn btn-outline-warning">
                <i class="bi bi-arrow-clockwise"></i> Clear Filters
            </button>
        </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <i class="bi bi-arrow-repeat animate-spin text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Generating Report</h3>
                <p class="text-sm text-gray-500 mt-1">Please wait while we process your request...</p>
            </div>
        </div>
    </div>
</div>