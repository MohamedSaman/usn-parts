<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Cheque;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    
    // Cheque related properties
    public $cheques = [];
    public $bankTransfer = [
        'bank_name' => '',
        'transfer_date' => '',
        'reference_number' => ''
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
        'paymentData.payment_method' => 'required|in:cash,cheque,bank_transfer',
        'paymentData.reference_number' => 'nullable|string|max:100',
        'paymentData.notes' => 'nullable|string|max:500',
        'totalPaymentAmount' => 'required|numeric|min:0.01',
        'cheques.*.cheque_number' => 'required_if:paymentData.payment_method,cheque|string|max:50',
        'cheques.*.bank_name' => 'required_if:paymentData.payment_method,cheque|string|max:100',
        'cheques.*.cheque_date' => 'required_if:paymentData.payment_method,cheque|date',
        'cheques.*.amount' => 'required_if:paymentData.payment_method,cheque|numeric|min:0.01',
        'bankTransfer.bank_name' => 'required_if:paymentData.payment_method,bank_transfer|string|max:100',
        'bankTransfer.transfer_date' => 'required_if:paymentData.payment_method,bank_transfer|date',
        'bankTransfer.reference_number' => 'required_if:paymentData.payment_method,bank_transfer|string|max:100',
    ];

    protected $messages = [
        'paymentData.payment_date.required' => 'Payment date is required.',
        'paymentData.payment_method.required' => 'Payment method is required.',
        'totalPaymentAmount.required' => 'Payment amount is required.',
        'totalPaymentAmount.min' => 'Payment amount must be at least Rs. 0.01',
        'cheques.*.cheque_number.required_if' => 'Cheque number is required for cheque payments.',
        'cheques.*.bank_name.required_if' => 'Bank name is required for cheque payments.',
        'cheques.*.cheque_date.required_if' => 'Cheque date is required for cheque payments.',
        'cheques.*.amount.required_if' => 'Cheque amount is required for cheque payments.',
        'bankTransfer.bank_name.required_if' => 'Bank name is required for bank transfer.',
        'bankTransfer.transfer_date.required_if' => 'Transfer date is required for bank transfer.',
        'bankTransfer.reference_number.required_if' => 'Reference number is required for bank transfer.',
    ];

    public function mount()
    {
        $this->paymentData['payment_date'] = now()->format('Y-m-d');
        $this->totalPaymentAmount = 0;
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedCustomer = null;
        $this->customerSales = [];
        $this->resetPaymentData();
    }

    public function updatedTotalPaymentAmount()
    {
        if ($this->totalPaymentAmount > $this->totalDueAmount) {
            $this->totalPaymentAmount = $this->totalDueAmount;
        }
        
        if ($this->totalPaymentAmount < 0) {
            $this->totalPaymentAmount = 0;
        }
        
        $this->calculateRemainingAmount();
        $this->autoAllocatePayment();
        
        // Update cheque amounts if payment method is cheque
        if ($this->paymentData['payment_method'] === 'cheque' && !empty($this->cheques)) {
            $this->updateChequeAmounts();
        }
    }

    public function updatedPaymentDataPaymentMethod($value)
    {
        // Reset all payment method specific fields first
        $this->cheques = [];
        $this->bankTransfer = [
            'bank_name' => '',
            'transfer_date' => '',
            'reference_number' => ''
        ];
        
        // Initialize based on selected method
        if ($value === 'cheque') {
            $this->initializeCheque();
        } elseif ($value === 'bank_transfer') {
            $this->bankTransfer = [
                'bank_name' => '',
                'transfer_date' => now()->format('Y-m-d'),
                'reference_number' => ''
            ];
        }
    }

    private function initializeCheque()
    {
        $this->cheques = [
            [
                'cheque_number' => '',
                'bank_name' => '',
                'cheque_date' => now()->format('Y-m-d'),
                'amount' => $this->totalPaymentAmount > 0 ? $this->totalPaymentAmount : 0
            ]
        ];
    }

    public function addCheque()
    {
        $this->cheques[] = [
            'cheque_number' => '',
            'bank_name' => '',
            'cheque_date' => now()->format('Y-m-d'),
            'amount' => 0
        ];
    }

    public function removeCheque($index)
    {
        if (count($this->cheques) > 1) {
            unset($this->cheques[$index]);
            $this->cheques = array_values($this->cheques);
        }
    }

    private function updateChequeAmounts()
    {
        $totalChequeAmount = collect($this->cheques)->sum('amount');
        $difference = $this->totalPaymentAmount - $totalChequeAmount;

        if ($difference != 0 && !empty($this->cheques)) {
            $this->cheques[0]['amount'] = max(0, $this->cheques[0]['amount'] + $difference);
        }
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->loadCustomerSales();
        $this->totalPaymentAmount = 0;
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
        $this->cheques = [];
        $this->resetPaymentData();
    }

    private function loadCustomerSales()
    {
        if (!$this->selectedCustomer) return;

        $sales = Sale::with(['items', 'payments'])
            ->where('customer_id', $this->selectedCustomer->id)
            ->where(function ($query) {
                $query->where('payment_status', 'pending')
                    ->orWhere('payment_status', 'partial');
            })
            ->where('due_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->customerSales = $sales->map(function ($sale) {
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
            ];
        })->toArray();

        $this->calculateTotalDue();
    }

    private function calculateTotalDue()
    {
        $this->totalDueAmount = collect($this->customerSales)->sum('due_amount');
        $this->remainingAmount = $this->totalDueAmount;
    }

    private function calculateRemainingAmount()
    {
        $this->remainingAmount = $this->totalDueAmount - $this->totalPaymentAmount;
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
    }

    private function autoAllocatePayment()
    {
        $remainingPayment = $this->totalPaymentAmount;

        foreach ($this->customerSales as $sale) {
            $saleId = $sale['id'];
            $dueAmount = $sale['due_amount'];

            if ($remainingPayment <= 0) {
                $this->allocations[$saleId]['payment_amount'] = 0;
                $this->allocations[$saleId]['is_fully_paid'] = false;
            } elseif ($remainingPayment >= $dueAmount) {
                $this->allocations[$saleId]['payment_amount'] = $dueAmount;
                $this->allocations[$saleId]['is_fully_paid'] = true;
                $remainingPayment -= $dueAmount;
            } else {
                $this->allocations[$saleId]['payment_amount'] = $remainingPayment;
                $this->allocations[$saleId]['is_fully_paid'] = false;
                $remainingPayment = 0;
            }
        }
    }

    public function openPaymentModal()
    {
        // Validate payment amount
        if (!$this->totalPaymentAmount || $this->totalPaymentAmount <= 0) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please enter a payment amount greater than zero.'
            ]);
            return;
        }

        if ($this->totalPaymentAmount > $this->totalDueAmount) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Payment amount cannot exceed total due amount.'
            ]);
            return;
        }

        // Validate cheque amounts if payment method is cheque
        if ($this->paymentData['payment_method'] === 'cheque') {
            $totalChequeAmount = collect($this->cheques)->sum('amount');
            if (abs($totalChequeAmount - $this->totalPaymentAmount) > 0.01) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Total cheque amount must equal the payment amount.'
                ]);
                return;
            }
        }

        // Allocate payment
        $this->autoAllocatePayment();
        
        // Show modal
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->paymentSuccess = false;
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
        
        // Reset everything
        $this->selectedCustomer = null;
        $this->customerSales = [];
        $this->allocations = [];
        $this->totalDueAmount = 0;
        $this->totalPaymentAmount = 0;
        $this->remainingAmount = 0;
        $this->cheques = [];
        $this->search = '';
        $this->resetPaymentData();
        
        // Reset page
        $this->resetPage();
        
        // Dispatch event to refresh the page
        $this->dispatch('payment-completed');
    }

    private function resetPaymentData()
    {
        $this->paymentData = [
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'reference_number' => '',
            'notes' => ''
        ];
        $this->totalPaymentAmount = 0;
        $this->cheques = [];
        $this->bankTransfer = [
            'bank_name' => '',
            'transfer_date' => '',
            'reference_number' => ''
        ];
    }

    public function viewSale($saleId)
    {
        $this->selectedSale = Sale::with(['customer', 'items', 'payments'])->find($saleId);
        $this->showViewModal = true;
    }

    public function processPayment()
    {
        Log::info('Payment processing started', [
            'customer_id' => $this->selectedCustomer->id,
            'amount' => $this->totalPaymentAmount,
            'method' => $this->paymentData['payment_method']
        ]);

        // Validate inputs
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please fill all required fields correctly.'
            ]);
            return;
        }

        // Check payment amount
        if ($this->totalPaymentAmount > $this->totalDueAmount) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Payment amount cannot exceed total due amount.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $totalProcessed = 0;
            $processedInvoices = [];

            // Create a single payment record for customer (not tied to specific sale)
            $paymentData = [
                'customer_id' => $this->selectedCustomer->id,
                'amount' => $this->totalPaymentAmount,
                'payment_method' => $this->paymentData['payment_method'],
                'payment_reference' => $this->paymentData['reference_number'] ?? null,
                'payment_date' => $this->paymentData['payment_date'],
                'status' => 'paid',
                'is_completed' => 1,
                'notes' => $this->paymentData['notes'] ?? null,
                'created_by' => Auth::id(),
            ];

            // Add bank transfer details if payment method is bank transfer
            if ($this->paymentData['payment_method'] === 'bank_transfer') {
                $paymentData['bank_name'] = $this->bankTransfer['bank_name'];
                $paymentData['transfer_date'] = $this->bankTransfer['transfer_date'];
                $paymentData['transfer_reference'] = $this->bankTransfer['reference_number'];
            }

            $payment = Payment::create($paymentData);

            Log::info('Main payment created', ['payment_id' => $payment->id]);

            // Process cheques if payment method is cheque
            if ($this->paymentData['payment_method'] === 'cheque') {
                foreach ($this->cheques as $chequeData) {
                    Cheque::create([
                        'payment_id' => $payment->id,
                        'cheque_number' => $chequeData['cheque_number'],
                        'bank_name' => $chequeData['bank_name'],
                        'cheque_date' => $chequeData['cheque_date'],
                        'cheque_amount' => $chequeData['amount'],
                        'status' => 'pending',
                        'customer_id' => $this->selectedCustomer->id,
                    ]);
                }
                Log::info('Cheques created', ['count' => count($this->cheques)]);
            }

            // Process each sale allocation
            foreach ($this->customerSales as $sale) {
                $saleId = $sale['id'];
                $allocation = $this->allocations[$saleId];
                $paymentAmount = $allocation['payment_amount'];

                if ($paymentAmount <= 0) continue;

                // Update sale
                $saleModel = Sale::find($saleId);
                if ($saleModel) {
                    $newDueAmount = $saleModel->due_amount - $paymentAmount;
                    $saleModel->due_amount = max(0, $newDueAmount);

                    if ($saleModel->due_amount <= 0.01) {
                        $saleModel->payment_status = 'paid';
                        $saleModel->due_amount = 0;
                    } else {
                        $saleModel->payment_status = 'partial';
                    }
                    
                    $saleModel->save();
                    
                    // Create payment allocation record (to track which payment paid which invoice)
                    DB::table('payment_allocations')->insert([
                        'payment_id' => $payment->id,
                        'sale_id' => $saleId,
                        'allocated_amount' => $paymentAmount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    Log::info('Sale updated and allocation created', [
                        'sale_id' => $saleId,
                        'payment_amount' => $paymentAmount,
                        'new_due' => $saleModel->due_amount
                    ]);
                }

                $totalProcessed += $paymentAmount;
                $processedInvoices[] = $sale['invoice_number'];
            }

            DB::commit();

            Log::info('Payment processed successfully', [
                'total_processed' => $totalProcessed,
                'invoices' => $processedInvoices,
                'payment_id' => $payment->id
            ]);

            $this->paymentSuccess = true;
            $this->showPaymentModal = false;
            $this->latestPayment = $payment;
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => "Payment of Rs." . number_format($totalProcessed, 2) . " processed successfully!"
            ]);

            // Open receipt modal
            $this->openReceiptModal();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
            // Load payment with relationships
            $payment = Payment::with(['cheques'])
                ->find($this->latestPayment->id);

            // Get allocations from payment_allocations table
            $allocations = DB::table('payment_allocations')
                ->join('sales', 'payment_allocations.sale_id', '=', 'sales.id')
                ->where('payment_allocations.payment_id', $payment->id)
                ->select(
                    'sales.invoice_number',
                    'sales.total_amount',
                    'payment_allocations.allocated_amount'
                )
                ->get();

            $receiptData = [
                'payment' => $payment,
                'customer' => $this->selectedCustomer,
                'received_by' => Auth::user()->name,
                'payment_date' => $payment->payment_date,
                'allocations' => $allocations,
            ];

            $pdf = PDF::loadView('admin.receipts.payment-receipt', $receiptData);
            $pdf->setPaper('a4', 'portrait');

            $filename = 'payment-receipt-' . $payment->id . '-' . date('Y-m-d') . '.pdf';

            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Receipt download failed', [
                'error' => $e->getMessage(),
                'payment_id' => $this->latestPayment->id
            ]);
            
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to generate receipt: ' . $e->getMessage()
            ]);
        }
    }

    public function getCustomersProperty()
    {
        return Customer::with(['sales' => function ($query) {
            $query->where('due_amount', '>', 0)
                ->where(function ($q) {
                    $q->where('payment_status', 'pending')
                        ->orWhere('payment_status', 'partial');
                });
        }])
            ->whereHas('sales', function ($query) {
                $query->where('due_amount', '>', 0)
                    ->where(function ($q) {
                        $q->where('payment_status', 'pending')
                            ->orWhere('payment_status', 'partial');
                    });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
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