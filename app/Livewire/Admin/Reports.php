<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Sale;
use App\Models\Payment;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use App\Exports\SalaryReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\StaffReportExport;
use App\Exports\PaymentsReportExport;
use App\Exports\AttendanceReportExport;
use Illuminate\Support\Facades\DB;
use App\Livewire\Concerns\WithDynamicLayout;

#[Title('Reports')]
class Reports extends Component
{
    use WithDynamicLayout;

    public $selectedReport = 'sales';
    public $reportStartDate;
    public $reportEndDate;

    // Report data arrays
    public $salesReport = [];
    public $salaryReport = [];
    public $inventoryReport = [];
    public $staffReport = [];
    public $paymentsReport = [];
    public $attendanceReport = [];

    // Report totals
    public $salesReportTotal = 0;
    public $salaryReportTotal = 0;
    public $inventoryReportTotal = 0;
    public $staffReportTotal = 0;
    public $paymentsReportTotal = 0;
    public $attendanceReportTotal = 0;

    public function mount()
    {
        // Generate initial report
        $this->generateReport();
    }

    public function updatedSelectedReport()
    {
        $this->generateReport();
    }

    public function updatedReportStartDate()
    {
        $this->generateReport();
    }

    public function updatedReportEndDate()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        // Validate date range
        if ($this->reportStartDate && $this->reportEndDate && $this->reportStartDate > $this->reportEndDate) {
            $this->addError('reportEndDate', 'End date must be after start date.');
            return;
        }

        // Clear previous errors
        $this->resetErrorBag();

        if ($this->selectedReport === 'sales') {
            $this->salesReport = $this->getSalesReport($this->reportStartDate, $this->reportEndDate);
            $this->salesReportTotal = collect($this->salesReport)->sum('total_amount');
        } elseif ($this->selectedReport === 'salary') {
            $this->salaryReport = $this->getSalaryReport($this->reportStartDate, $this->reportEndDate);
            $this->salaryReportTotal = collect($this->salaryReport)->sum('net_salary');
        } elseif ($this->selectedReport === 'inventory') {
            $this->inventoryReport = $this->getInventoryReport($this->reportStartDate, $this->reportEndDate);
            $this->inventoryReportTotal = collect($this->inventoryReport)->sum('available_stock');
        } elseif ($this->selectedReport === 'staff') {
            $this->staffReport = $this->getStaffReport($this->reportStartDate, $this->reportEndDate);
            $this->staffReportTotal = collect($this->staffReport)->sum('total_sales');
        } elseif ($this->selectedReport === 'payments') {
            $this->paymentsReport = $this->getPaymentsReport($this->reportStartDate, $this->reportEndDate);
            $this->paymentsReportTotal = collect($this->paymentsReport)->sum('amount');
        } elseif ($this->selectedReport === 'attendance') {
            $this->attendanceReport = $this->getAttendanceReport($this->reportStartDate, $this->reportEndDate);
            $this->attendanceReportTotal = collect($this->attendanceReport)->count();
        }
    }

    public function downloadReport()
    {
        $filename = $this->selectedReport . '_report_' . now()->format('Y_m_d') . '.xlsx';
        
        switch ($this->selectedReport) {
            case 'sales':
                $export = new SalesReportExport($this->salesReport, $this->salesReportTotal);
                break;
            case 'salary':
                $export = new SalaryReportExport($this->salaryReport, $this->salaryReportTotal);
                break;
            case 'inventory':
                $export = new InventoryReportExport($this->inventoryReport, $this->inventoryReportTotal);
                break;
            case 'staff':
                $export = new StaffReportExport($this->staffReport, $this->staffReportTotal);
                break;
            case 'payments':
                $export = new PaymentsReportExport($this->paymentsReport, $this->paymentsReportTotal);
                break;
            case 'attendance':
                $export = new AttendanceReportExport($this->attendanceReport, $this->attendanceReportTotal);
                break;
            default:
                return;
        }

        return Excel::download($export, $filename);
    }

    public function printReport()
    {
        $this->dispatch('print-report', reportType: $this->selectedReport);
    }

    // Report data methods
    public function getSalesReport($start = null, $end = null)
    {
        $query = Sale::with('items', 'customer', 'payments')->orderBy('created_at', 'desc');
        
        if ($start) $query->whereDate('created_at', '>=', $start);
        if ($end) $query->whereDate('created_at', '<=', $end);
        
        return $query->limit(100)->get();
    }

    public function getSalaryReport($start = null, $end = null)
    {
        $query = DB::table('salaries')
            ->join('users', 'salaries.user_id', '=', 'users.id')
            ->select('users.name', 'salaries.net_salary', 'salaries.salary_month', 'salaries.payment_status')
            ->orderBy('salaries.salary_month', 'desc');
            
        if ($start) $query->whereDate('salaries.salary_month', '>=', $start);
        if ($end) $query->whereDate('salaries.salary_month', '<=', $end);
        
        return $query->limit(100)->get();
    }

    public function getInventoryReport($start = null, $end = null)
    {
        $query = DB::table('product_details')
            ->join('product_stocks', 'product_details.id', '=', 'product_stocks.product_id')
            ->join('brand_lists', 'product_details.brand_id', '=', 'brand_lists.id')
            ->select(
                'product_details.name', 
                'product_details.model', 
                'brand_lists.brand_name as brand', 
                'product_stocks.total_stock',
                'product_stocks.available_stock',
                'product_stocks.sold_count',
                'product_stocks.damage_stock'
            )
            ->orderBy('product_stocks.available_stock', 'desc');
            
        return $query->get();
    }

    public function getStaffReport($start = null, $end = null)
    {
        $query = DB::table('users')
            ->where('role', 'staff')
            ->leftJoin('staff_sales', 'users.id', '=', 'staff_sales.staff_id')
            ->select(
                'users.name', 
                'users.email', 
                DB::raw('COALESCE(SUM(staff_sales.sold_value), 0) as total_sales'),
                DB::raw('COALESCE(SUM(staff_sales.sold_quantity), 0) as total_quantity')
            )
            ->groupBy('users.id', 'users.name', 'users.email');
            
        return $query->get();
    }

    public function getPaymentsReport($start = null, $end = null)
    {
        $query = Payment::with('sale')->orderBy('payment_date', 'desc');
        
        if ($start) $query->whereDate('payment_date', '>=', $start);
        if ($end) $query->whereDate('payment_date', '<=', $end);
        
        return $query->limit(100)->get();
    }

    public function getAttendanceReport($start = null, $end = null)
    {
        $query = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->select(
                'users.name', 
                'attendances.date', 
                'attendances.check_in', 
                'attendances.check_out', 
                'attendances.status'
            )
            ->orderBy('attendances.date', 'desc');
            
        if ($start) $query->whereDate('attendances.date', '>=', $start);
        if ($end) $query->whereDate('attendances.date', '<=', $end);
        
        return $query->limit(100)->get();
    }

    public function getCurrentReportData()
    {
        return match($this->selectedReport) {
            'sales' => $this->salesReport,
            'salary' => $this->salaryReport,
            'inventory' => $this->inventoryReport,
            'staff' => $this->staffReport,
            'payments' => $this->paymentsReport,
            'attendance' => $this->attendanceReport,
            default => [],
        };
    }

    public function getCurrentReportTotal()
    {
        return match($this->selectedReport) {
            'sales' => $this->salesReportTotal,
            'salary' => $this->salaryReportTotal,
            'inventory' => $this->inventoryReportTotal,
            'staff' => $this->staffReportTotal,
            'payments' => $this->paymentsReportTotal,
            'attendance' => $this->attendanceReportTotal,
            default => 0,
        };
    }

    public function getReportTitle()
    {
        return match($this->selectedReport) {
            'sales' => 'Sales Report',
            'salary' => 'Salary Report',
            'inventory' => 'Inventory Report',
            'staff' => 'Staff Performance Report',
            'payments' => 'Payments Report',
            'attendance' => 'Attendance Report',
            default => 'Report',
        };
    }

    public function clearFilters()
    {
        $this->reportStartDate = null;
        $this->reportEndDate = null;
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.admin.reports', [
            'currentReportData' => $this->getCurrentReportData(),
            'currentReportTotal' => $this->getCurrentReportTotal(),
            'reportTitle' => $this->getReportTitle(),
        ])->layout($this->layout);
    }
}