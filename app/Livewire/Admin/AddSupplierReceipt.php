<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductSupplier;
use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use App\Models\PurchasePaymentAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title("Add Supplier Receipt")]
#[Layout('components.layouts.admin')]
class AddSupplierReceipt extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedSupplier = null;
    public $supplierOrders = [];
    public $selectedOrders = [];
    public $totalDueAmount = 0;
    public $totalPaymentAmount = 0;
    public $remainingAmount = 0;
    public $showPaymentModal = false;

    // Payment modal fields
    public $paymentData = [
        'payment_date' => '',
        'payment_method' => 'cash',
        'reference_number' => '',
        'notes' => ''
    ];

    public $cheque = [
        'cheque_number' => '',
        'bank_name' => '',
        'cheque_date' => '',
        'amount' => 0
    ];

    public $bankTransfer = [
        'bank_name' => '',
        'transfer_date' => '',
        'reference_number' => ''
    ];

    public $allocations = [];

    public function mount()
    {
        $this->paymentData['payment_date'] = now()->format('Y-m-d');
        $this->cheque['cheque_date'] = now()->format('Y-m-d');
        $this->bankTransfer['transfer_date'] = now()->format('Y-m-d');
    }

    public function updatedSearch($value)
    {
        $this->resetPage();
        $this->selectedSupplier = null;
        $this->supplierOrders = [];
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

        // Sync cheque amount
        if ($this->paymentData['payment_method'] === 'cheque') {
            $this->cheque['amount'] = $this->totalPaymentAmount;
        }
    }

    public function updatedPaymentDataPaymentMethod($value)
    {
        // Reset method-specific fields
        if ($value === 'cheque') {
            $this->cheque['cheque_date'] = now()->format('Y-m-d');
            $this->cheque['amount'] = $this->totalPaymentAmount;
        } else {
            $this->cheque = [
                'cheque_number' => '',
                'bank_name' => '',
                'cheque_date' => now()->format('Y-m-d'),
                'amount' => 0
            ];
        }

        if ($value === 'bank_transfer') {
            $this->bankTransfer['transfer_date'] = now()->format('Y-m-d');
        } else {
            $this->bankTransfer = [
                'bank_name' => '',
                'transfer_date' => now()->format('Y-m-d'),
                'reference_number' => ''
            ];
        }
    }

    public function selectSupplier($supplierId)
    {
        $this->selectedSupplier = ProductSupplier::find($supplierId);
        $this->loadSupplierOrders();
        $this->selectedOrders = [];
        $this->totalPaymentAmount = 0;
        $this->totalDueAmount = 0;
        $this->initializeAllocations();
    }

    public function clearSelectedSupplier()
    {
        $this->selectedSupplier = null;
        $this->supplierOrders = [];
        $this->selectedOrders = [];
        $this->allocations = [];
        $this->totalDueAmount = 0;
        $this->totalPaymentAmount = 0;
        $this->remainingAmount = 0;
        $this->resetPaymentData();
    }

    private function loadSupplierOrders()
    {
        if (!$this->selectedSupplier) return;

        $orders = PurchaseOrder::where('supplier_id', $this->selectedSupplier->id)
            ->where('due_amount', '>', 0)
            ->orderBy('order_date', 'asc')
            ->get();

        $this->supplierOrders = $orders;
    }

    public function toggleOrderSelection($orderId)
    {
        if (in_array($orderId, $this->selectedOrders)) {
            $this->selectedOrders = array_values(array_diff($this->selectedOrders, [$orderId]));
        } else {
            $this->selectedOrders[] = $orderId;
        }
        
        $this->calculateTotalDue();
        $this->totalPaymentAmount = 0;
        $this->remainingAmount = $this->totalDueAmount;
        $this->initializeAllocations();
    }

    public function selectAllOrders()
    {
        $this->selectedOrders = $this->supplierOrders->pluck('id')->toArray();
        $this->calculateTotalDue();
        $this->totalPaymentAmount = 0;
        $this->remainingAmount = $this->totalDueAmount;
        $this->initializeAllocations();
    }

    public function clearOrderSelection()
    {
        $this->selectedOrders = [];
        $this->totalDueAmount = 0;
        $this->totalPaymentAmount = 0;
        $this->remainingAmount = 0;
        $this->allocations = [];
    }

    private function calculateTotalDue()
    {
        $this->totalDueAmount = collect($this->supplierOrders)
            ->whereIn('id', $this->selectedOrders)
            ->sum('due_amount');
        $this->remainingAmount = $this->totalDueAmount;
    }

    private function calculateRemainingAmount()
    {
        $this->remainingAmount = $this->totalDueAmount - $this->totalPaymentAmount;
    }

    private function initializeAllocations()
    {
        $this->allocations = [];
        foreach ($this->supplierOrders as $order) {
            if (in_array($order->id, $this->selectedOrders)) {
                $this->allocations[$order->id] = [
                    'order_code' => $order->order_code,
                    'due_amount' => $order->due_amount,
                    'payment_amount' => 0,
                    'is_fully_paid' => false
                ];
            }
        }
    }

    private function autoAllocatePayment()
    {
        $remainingPayment = $this->totalPaymentAmount;

        foreach ($this->supplierOrders as $order) {
            $orderId = $order->id;
            
            // Only allocate to selected orders
            if (!in_array($orderId, $this->selectedOrders)) {
                continue;
            }

            $dueAmount = $order->due_amount;

            if ($remainingPayment <= 0) {
                $this->allocations[$orderId]['payment_amount'] = 0;
                $this->allocations[$orderId]['is_fully_paid'] = false;
            } elseif ($remainingPayment >= $dueAmount) {
                $this->allocations[$orderId]['payment_amount'] = $dueAmount;
                $this->allocations[$orderId]['is_fully_paid'] = true;
                $remainingPayment -= $dueAmount;
            } else {
                $this->allocations[$orderId]['payment_amount'] = $remainingPayment;
                $this->allocations[$orderId]['is_fully_paid'] = false;
                $remainingPayment = 0;
            }
        }
    }

    public function openPaymentModal()
    {
        if (empty($this->selectedOrders)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please select at least one order to make a payment.'
            ]);
            return;
        }

        if ($this->totalPaymentAmount <= 0) {
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

        $this->autoAllocatePayment();

        if ($this->paymentData['payment_method'] === 'cheque') {
            $this->cheque['amount'] = $this->totalPaymentAmount;
        }

        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->resetErrorBag();
        $this->resetValidation();
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
        $this->cheque = [
            'cheque_number' => '',
            'bank_name' => '',
            'cheque_date' => now()->format('Y-m-d'),
            'amount' => 0
        ];
        $this->bankTransfer = [
            'bank_name' => '',
            'transfer_date' => now()->format('Y-m-d'),
            'reference_number' => ''
        ];
    }

    public function processPayment()
    {
        // Base validation
        $this->validate([
            'paymentData.payment_date' => 'required|date',
            'paymentData.payment_method' => 'required|in:cash,cheque,bank_transfer',
            'totalPaymentAmount' => 'required|numeric|min:0.01',
        ]);

        if ($this->totalPaymentAmount > $this->totalDueAmount) {
            $this->addError('totalPaymentAmount', 'Payment amount cannot exceed total due.');
            return;
        }

        $hasAllocation = collect($this->allocations)->sum('payment_amount') > 0;
        if (!$hasAllocation) {
            $this->dispatch('show-toast', ['type' => 'error', 'message' => 'No payment allocated to any order.']);
            return;
        }

        // Conditional validation
        if ($this->paymentData['payment_method'] === 'cheque') {
            $this->validate([
                'cheque.cheque_number' => 'required|string|max:255',
                'cheque.bank_name' => 'required|string|max:255',
                'cheque.cheque_date' => 'required|date',
                'cheque.amount' => 'required|numeric|min:0.01',
            ]);

            if (abs($this->cheque['amount'] - $this->totalPaymentAmount) > 0.01) {
                $this->addError('cheque.amount', 'Cheque amount must match payment amount.');
                return;
            }
        }

        if ($this->paymentData['payment_method'] === 'bank_transfer') {
            $this->validate([
                'bankTransfer.bank_name' => 'required|string|max:255',
                'bankTransfer.transfer_date' => 'required|date',
                'bankTransfer.reference_number' => 'required|string|max:255',
            ]);
        }

        DB::beginTransaction();
        try {
            $paymentRecord = [
                'supplier_id' => $this->selectedSupplier->id,
                'amount' => $this->totalPaymentAmount,
                'payment_method' => $this->paymentData['payment_method'],
                'payment_reference' => $this->paymentData['reference_number'] ?? null,
                'payment_date' => $this->paymentData['payment_date'],
                'notes' => $this->paymentData['notes'],
                'status' => $this->paymentData['payment_method'] === 'cash' ? 'paid' : 'pending',
                'is_completed' => $this->paymentData['payment_method'] === 'cash' ? 1 : 0,
            ];

            if ($this->paymentData['payment_method'] === 'cheque') {
                $paymentRecord = array_merge($paymentRecord, [
                    'cheque_number' => $this->cheque['cheque_number'],
                    'bank_name' => $this->cheque['bank_name'],
                    'cheque_date' => $this->cheque['cheque_date'],
                    'cheque_status' => 'pending',
                ]);
            }

            if ($this->paymentData['payment_method'] === 'bank_transfer') {
                $paymentRecord = array_merge($paymentRecord, [
                    'bank_name' => $this->bankTransfer['bank_name'],
                    'bank_transaction' => $this->bankTransfer['reference_number'],
                ]);
            }

            $payment = PurchasePayment::create($paymentRecord);

            foreach ($this->allocations as $orderId => $allocation) {
                if ($allocation['payment_amount'] > 0) {
                    PurchasePaymentAllocation::create([
                        'purchase_payment_id' => $payment->id,
                        'purchase_order_id' => $orderId,
                        'allocated_amount' => $allocation['payment_amount'],
                    ]);

                    $order = PurchaseOrder::find($orderId);
                    if ($order) {
                        $order->due_amount -= $allocation['payment_amount'];
                        $order->due_amount = max(0, round($order->due_amount, 2));

                        $order->save();
                    }
                }
            }

            DB::commit();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Payment recorded successfully!'
            ]);

            $this->showPaymentModal = false;
            $this->clearSelectedSupplier();
            $this->resetPage();
            $this->resetErrorBag();
            $this->resetValidation();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to save payment. Please try again.'
            ]);
        }
    }

    public function getSuppliersProperty()
    {
        return ProductSupplier::with(['orders' => function ($query) {
            $query->where('due_amount', '>', 0);
        }])
            ->whereHas('orders', function ($query) {
                $query->where('due_amount', '>', 0);
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('mobile', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.add-supplier-receipt', [
            'suppliers' => $this->suppliers
        ]);
    }
}
