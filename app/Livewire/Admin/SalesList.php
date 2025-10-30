<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('components.layouts.admin')]
#[Title('Admin Sales Management')]
class SalesList extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedSale = null;
    public $paymentStatusFilter = 'all';
    public $dateFilter = '';
    public $showViewModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    // Edit form properties
    public $editSaleId;
    public $editCustomerId;
    public $editPaymentStatus;
    public $editNotes;
    public $editDueAmount;
    public $editPaidAmount;
    public $editPayBalanceAmount = 0;

    public function mount()
    {
        // Initialize component
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function viewSale($saleId)
    {
        $this->selectedSale = Sale::with([
            'customer',
            'items',
            'user',
            'returns' => function ($q) {
                $q->with('product');
            }
        ])
            ->where('sale_type', 'admin')
            ->find($saleId);

        $this->showViewModal = true;
        $this->dispatch('showModal', 'viewModal');
    }

    public function editSale($saleId)
    {
        $sale = Sale::with(['customer'])->where('sale_type', 'admin')->find($saleId);

        if ($sale) {
            $this->editSaleId = $sale->id;
            $this->editCustomerId = $sale->customer_id;
            $this->editPaymentStatus = $sale->payment_status;
            $this->editNotes = $sale->notes;
            $this->editDueAmount = $sale->due_amount;
            $this->editPaidAmount = $sale->total_amount - $sale->due_amount;
            $this->editPayBalanceAmount = 0;

            $this->showEditModal = true;
            $this->dispatch('showModal', 'editModal');
        }
    }

    public function updatedEditPaymentStatus($value)
    {
        if ($this->editSaleId) {
            $sale = Sale::find($this->editSaleId);
            if ($sale) {
                if ($value === 'paid') {
                    $this->editPaidAmount = $sale->total_amount;
                    $this->editDueAmount = 0;
                    $this->editPayBalanceAmount = $sale->due_amount;
                } elseif ($value === 'pending') {
                    $this->editPaidAmount = 0;
                    $this->editDueAmount = $sale->total_amount;
                    $this->editPayBalanceAmount = 0;
                } else {
                    $this->editPayBalanceAmount = 0;
                }
            }
        }
    }

    public function updatedEditPayBalanceAmount($value)
    {
        if ($this->editSaleId) {
            $sale = Sale::find($this->editSaleId);
            if ($sale) {
                $value = floatval($value);
                $maxPayable = $sale->due_amount;

                if ($value > $maxPayable) {
                    $this->editPayBalanceAmount = $maxPayable;
                    $value = $maxPayable;
                }

                if ($value < 0) {
                    $this->editPayBalanceAmount = 0;
                    $value = 0;
                }

                $this->editPaidAmount = $sale->total_amount - $sale->due_amount + $value;
                $this->editDueAmount = $sale->due_amount - $value;

                if ($this->editDueAmount <= 0) {
                    $this->editPaymentStatus = 'paid';
                } elseif ($value > 0) {
                    $this->editPaymentStatus = 'partial';
                } else {
                    $this->editPaymentStatus = 'pending';
                }
            }
        }
    }

    public function updateSale()
    {
        $this->validate([
            'editPaymentStatus' => 'required|in:paid,partial,pending',
            'editDueAmount' => 'required|numeric|min:0',
            'editPaidAmount' => 'required|numeric|min:0',
            'editPayBalanceAmount' => 'required|numeric|min:0',
        ]);

        try {
            $sale = Sale::find($this->editSaleId);

            if ($sale) {
                $totalAmount = $sale->total_amount;
                $paidAmount = $this->editPaidAmount;
                $dueAmount = $this->editDueAmount;

                if (($paidAmount + $dueAmount) != $totalAmount) {
                    $dueAmount = $totalAmount - $paidAmount;
                }

                $sale->update([
                    'customer_id' => $this->editCustomerId,
                    'payment_status' => $this->editPaymentStatus,
                    'notes' => $this->editNotes,
                    'due_amount' => $dueAmount,
                    'payment_type' => $this->editPaymentStatus === 'paid' ? 'full' : 'partial',
                ]);

                $this->showEditModal = false;
                $this->resetEditForm();
                $this->dispatch('hideModal', 'editModal');
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Sale updated successfully!']);
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Error updating sale: ' . $e->getMessage()]);
        }
    }

    public function payFullBalance()
    {
        if ($this->editSaleId) {
            $sale = Sale::find($this->editSaleId);
            if ($sale) {
                $this->editPayBalanceAmount = $sale->due_amount;
                $this->updatedEditPayBalanceAmount($sale->due_amount);
            }
        }
    }

    public function resetPayBalance()
    {
        $this->editPayBalanceAmount = 0;
        $this->updatedEditPayBalanceAmount(0);
    }

    public function deleteSale($saleId)
    {
        $this->selectedSale = Sale::where('sale_type', 'admin')->find($saleId);
        $this->showDeleteModal = true;
        $this->dispatch('showModal', 'deleteModal');
    }

    public function confirmDelete()
    {
        try {
            DB::transaction(function () {
                $saleItems = SaleItem::where('sale_id', $this->selectedSale->id)->get();

                foreach ($saleItems as $item) {
                    $productStock = ProductStock::where('product_id', $item->product_id)->first();
                    if ($productStock) {
                        $productStock->available_stock += $item->quantity;
                        if ($productStock->sold_count >= $item->quantity) {
                            $productStock->sold_count -= $item->quantity;
                        }
                        $productStock->save();
                    }
                }

                \App\Models\Payment::where('sale_id', $this->selectedSale->id)->delete();
                SaleItem::where('sale_id', $this->selectedSale->id)->delete();

                $this->selectedSale->delete();
            });

            $this->showDeleteModal = false;
            $this->selectedSale = null;
            $this->dispatch('hideModal', 'deleteModal');
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Sale deleted successfully!']);
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Error deleting sale: ' . $e->getMessage()]);
        }
    }

    public function downloadInvoice($saleId)
    {
        $sale = Sale::with(['customer', 'items'])->where('sale_type', 'admin')->find($saleId);

        if (!$sale) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Sale not found.']);
            return;
        }

        try {
            $sale->paid_amount = $sale->total_amount - $sale->due_amount;
            $sale->balance_amount = $sale->due_amount;

            $pdf = PDF::loadView('admin.sales.invoice', compact('sale'));

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('dpi', 150);
            $pdf->setOption('defaultFont', 'sans-serif');

            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                'invoice-' . $sale->invoice_number . '.pdf'
            );
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    public function closeModals()
    {
        $this->showViewModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->selectedSale = null;
        $this->resetEditForm();

        $this->dispatch('hideModal', 'viewModal');
        $this->dispatch('hideModal', 'editModal');
        $this->dispatch('hideModal', 'deleteModal');
    }

    private function resetEditForm()
    {
        $this->editSaleId = null;
        $this->editCustomerId = null;
        $this->editPaymentStatus = '';
        $this->editNotes = '';
        $this->editDueAmount = 0;
        $this->editPaidAmount = 0;
        $this->editPayBalanceAmount = 0;
    }

    public function getSalesProperty()
    {
        return Sale::with(['customer', 'user', 'items'])
            ->where('sale_type', 'admin') // Only admin sales
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhere('sale_id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', function ($customerQuery) {
                            $customerQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->paymentStatusFilter !== 'all', function ($query) {
                $query->where('payment_status', $this->paymentStatusFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('created_at', $this->dateFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function getSalesStatsProperty()
    {
        $adminSales = Sale::where('sale_type', 'admin');
        $todaySales = Sale::where('sale_type', 'admin')->whereDate('created_at', today());

        return [
            'total_sales' => $adminSales->count(),
            'total_amount' => $adminSales->sum('total_amount'),
            'pending_payments' => $adminSales->where('payment_status', 'pending')->sum('due_amount'),
            'partial_payments' => $adminSales->where('payment_status', 'partial')->sum('due_amount'),
            'paid_amount' => $adminSales->where('payment_status', 'paid')->sum('total_amount'),
            'today_sales' => $todaySales->count(),
            'today_amount' => $todaySales->sum('total_amount'),
        ];
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function markAsPaid($saleId)
    {
        try {
            $sale = Sale::where('sale_type', 'admin')->find($saleId);

            if ($sale) {
                $sale->update([
                    'payment_status' => 'paid',
                    'due_amount' => 0,
                    'payment_type' => 'full'
                ]);

                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Sale marked as paid successfully!']);
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Error updating sale: ' . $e->getMessage()]);
        }
    }

    public function markAsPending($saleId)
    {
        try {
            $sale = Sale::where('sale_type', 'admin')->find($saleId);

            if ($sale) {
                $sale->update([
                    'payment_status' => 'pending',
                    'due_amount' => $sale->total_amount,
                    'payment_type' => 'partial'
                ]);

                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Sale marked as pending successfully!']);
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Error updating sale: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.admin.sales-list', [
            'sales' => $this->sales,
            'stats' => $this->salesStats,
            'customers' => $this->customers,
        ]);
    }
}
