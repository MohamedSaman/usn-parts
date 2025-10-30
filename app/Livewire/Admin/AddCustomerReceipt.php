<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title("Add Customer Receipt")]
#[Layout('components.layouts.admin')]
class AddCustomerReceipt extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCustomer = null;
    public $customerSales = [];
    public $paymentData = [
        'payment_date' => '',
        'payment_method' => 'cash',
        'reference_number' => '',
        'notes' => ''
    ];
    public $allocations = [];
    public $totalDueAmount = 0;
    public $totalPaymentAmount = 0;
    public $remainingAmount = 0;
    public $showPaymentModal = false;
    public $showViewModal = false;
    public $showReceiptModal = false;
    public $selectedSale = null;
    public $latestPayment = null;
    public $paymentSuccess = false;

    protected $rules = [
        'paymentData.payment_date' => 'required|date',
        'paymentData.payment_method' => 'required|in:cash,card,bank_transfer,cheque',
        'paymentData.reference_number' => 'nullable|string|max:100',
        'paymentData.notes' => 'nullable|string|max:500',
        'allocations.*.payment_amount' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->paymentData['payment_date'] = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedCustomer = null;
        $this->customerSales = [];
        $this->resetPaymentData();
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->loadCustomerSales();
        $this->initializeAllocations();
    }

    public function clearSelectedCustomer()
    {
        $this->selectedCustomer = null;
        $this->customerSales = [];
        $this->allocations = [];
        $this->totalDueAmount = 0;
        $this->totalPaymentAmount = 0;
        $this->remainingAmount = 0;
    }

    private function loadCustomerSales()
    {
        if (!$this->selectedCustomer) return;

        $this->customerSales = Sale::with(['items', 'payments'])
            ->where('customer_id', $this->selectedCustomer->id)
            ->where(function($query) {
                $query->where('payment_status', 'pending')
                      ->orWhere('payment_status', 'partial');
            })
            ->where('due_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($sale) {
                $paidAmount = $sale->total_amount - $sale->due_amount;
                $remainingDue = $sale->due_amount;
                
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'sale_id' => $sale->sale_id,
                    'sale_date' => $sale->created_at->format('M d, Y'),
                    'total_amount' => $sale->total_amount,
                    'due_amount' => $remainingDue,
                    'paid_amount' => $paidAmount,
                    'payment_status' => $sale->payment_status,
                    'items_count' => $sale->items->count(),
                    'can_allocate' => $remainingDue > 0,
                    'sale_object' => $sale // Store the sale object for viewing
                ];
            })
            ->toArray();

        $this->calculateTotalDue();
    }

    private function calculateTotalDue()
    {
        $this->totalDueAmount = collect($this->customerSales)->sum('due_amount');
        $this->remainingAmount = $this->totalDueAmount;
    }

    private function initializeAllocations()
    {
        $this->allocations = [];
        
        foreach ($this->customerSales as $sale) {
            $this->allocations[$sale['id']] = [
                'sale_id' => $sale['id'],
                'invoice_number' => $sale['invoice_number'],
                'due_amount' => $sale['due_amount'],
                'payment_amount' => 0,
                'is_fully_paid' => false
            ];
        }
        
        $this->calculatePaymentTotals();
    }

    public function updatedAllocations()
    {
        $this->calculatePaymentTotals();
    }

    private function calculatePaymentTotals()
    {
        $this->totalPaymentAmount = 0;
        
        foreach ($this->allocations as $saleId => $allocation) {
            $paymentAmount = floatval($allocation['payment_amount'] ?? 0);
            $dueAmount = floatval($allocation['due_amount'] ?? 0);
            
            // Validate payment amount doesn't exceed due amount
            if ($paymentAmount > $dueAmount) {
                $paymentAmount = $dueAmount;
                $this->allocations[$saleId]['payment_amount'] = $dueAmount;
            }
            
            $this->totalPaymentAmount += $paymentAmount;
            
            // Update fully paid status
            $this->allocations[$saleId]['is_fully_paid'] = 
                abs($paymentAmount - $dueAmount) < 0.01;
        }

        $this->remainingAmount = $this->totalDueAmount - $this->totalPaymentAmount;
    }

    public function allocateFullPayment($saleId)
    {
        if (isset($this->allocations[$saleId])) {
            $this->allocations[$saleId]['payment_amount'] = $this->allocations[$saleId]['due_amount'];
            $this->calculatePaymentTotals();
        }
    }

    public function clearAllocation($saleId)
    {
        if (isset($this->allocations[$saleId])) {
            $this->allocations[$saleId]['payment_amount'] = 0;
            $this->calculatePaymentTotals();
        }
    }

    public function allocateRemainingAmount()
    {
        if ($this->remainingAmount <= 0) return;

        // Find the first sale that still has due amount
        foreach ($this->allocations as $saleId => $allocation) {
            $currentPayment = floatval($allocation['payment_amount']);
            $dueAmount = floatval($allocation['due_amount']);
            
            if ($currentPayment < $dueAmount) {
                $remainingForThisSale = $dueAmount - $currentPayment;
                $amountToAllocate = min($this->remainingAmount, $remainingForThisSale);
                
                $this->allocations[$saleId]['payment_amount'] = $currentPayment + $amountToAllocate;
                $this->calculatePaymentTotals();
                break;
            }
        }
    }

    public function openPaymentModal()
    {
        if ($this->totalPaymentAmount <= 0) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please allocate payment amounts to at least one sale.'
            ]);
            return;
        }

        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->paymentSuccess = false;
        $this->resetPaymentData();
    }

    private function resetPaymentData()
    {
        $this->paymentData = [
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'reference_number' => '',
            'notes' => ''
        ];
    }

    public function viewSale($saleId)
    {
        $this->selectedSale = Sale::with(['customer', 'items', 'payments'])->find($saleId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedSale = null;
    }

    public function openReceiptModal()
    {
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->latestPayment = null;
        $this->paymentSuccess = false;
    }

    public function processPayment()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $totalPaid = 0;
                $paymentRecords = [];

                foreach ($this->allocations as $allocation) {
                    $paymentAmount = floatval($allocation['payment_amount']);
                    
                    if ($paymentAmount > 0) {
                        $sale = Sale::find($allocation['sale_id']);
                        
                        if ($sale) {
                            // Create payment record
                            $payment = Payment::create([
                                'sale_id' => $sale->id,
                                'customer_id' => $this->selectedCustomer->id,
                                'amount' => $paymentAmount,
                                'payment_date' => $this->paymentData['payment_date'],
                                'payment_method' => $this->paymentData['payment_method'],
                                'reference_number' => $this->paymentData['reference_number'],
                                'notes' => $this->paymentData['notes'],
                                'received_by' => Auth::id(),
                                'status' => 'completed'
                            ]);

                            $paymentRecords[] = $payment;
                            $this->latestPayment = $payment; // Store the latest payment for receipt

                            // Update sale payment status
                            $newDueAmount = $sale->due_amount - $paymentAmount;
                            
                            $paymentStatus = 'partial';
                            if ($newDueAmount <= 0) {
                                $paymentStatus = 'paid';
                                $newDueAmount = 0;
                            }

                            $sale->update([
                                'due_amount' => $newDueAmount,
                                'payment_status' => $paymentStatus
                            ]);

                            $totalPaid += $paymentAmount;
                        }
                    }
                }

                // Log the transaction
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($this->selectedCustomer)
                    ->withProperties([
                        'total_paid' => $totalPaid,
                        'payment_method' => $this->paymentData['payment_method'],
                        'payments_count' => count($paymentRecords)
                    ])
                    ->log('Customer payment received');

                // Show success and open receipt
                $this->paymentSuccess = true;
                $this->showPaymentModal = false;
                $this->openReceiptModal();

                // Reload customer sales
                $this->loadCustomerSales();
                $this->initializeAllocations();

                // Dispatch toast notification
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'message' => "Payment of Rs." . number_format($totalPaid, 2) . " processed successfully!"
                ]);
            });

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadReceipt()
    {
        if (!$this->latestPayment) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'No payment receipt available to download.'
            ]);
            return;
        }

        try {
            $payment = $this->latestPayment;
            $sale = Sale::with(['customer', 'items'])->find($payment->sale_id);
            
            $receiptData = [
                'payment' => $payment,
                'sale' => $sale,
                'customer' => $this->selectedCustomer,
                'received_by' => Auth::user()->name,
                'payment_date' => $payment->payment_date,
            ];

            $pdf = PDF::loadView('admin.receipts.payment-receipt', $receiptData);
            
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('dpi', 150);
            $pdf->setOption('defaultFont', 'sans-serif');
            
            $filename = 'payment-receipt-' . $payment->id . '-' . date('Y-m-d') . '.pdf';
            
            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                $filename
            );
            
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to generate receipt: ' . $e->getMessage()
            ]);
        }
    }

    public function getCustomersProperty()
    {
        return Customer::with(['sales' => function($query) {
                $query->where('due_amount', '>', 0)
                      ->where(function($q) {
                          $q->where('payment_status', 'pending')
                            ->orWhere('payment_status', 'partial');
                      });
            }])
            ->whereHas('sales', function($query) {
                $query->where('due_amount', '>', 0)
                      ->where(function($q) {
                          $q->where('payment_status', 'pending')
                            ->orWhere('payment_status', 'partial');
                      });
            })
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.add-customer-receipt', [
            'customers' => $this->customers
        ]);
    }
}