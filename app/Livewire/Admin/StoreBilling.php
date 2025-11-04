<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\Customer;
use App\Models\ProductDetail;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Cheque;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title('POS')]
class StoreBilling extends Component
{
    use WithFileUploads;

    // Basic Properties
    public $search = '';
    public $searchResults = [];
    public $customerId = '';

    // Cart Items
    public $cart = [];

    // Customer Properties
    public $customers = [];
    public $selectedCustomer = null;

    // Customer Form (for new customer - only used in modal)
    public $customerName = '';
    public $customerPhone = '';
    public $customerEmail = '';
    public $customerAddress = '';
    public $customerType = 'retail';
    public $businessName = '';

    // Sale Properties
    public $notes = '';

    // Payment Properties
    public $paymentMethod = 'cash'; // 'cash', 'credit', 'cheque', 'bank_transfer'
    public $paidAmount = 0;

    // Cash Payment
    public $cashAmount = 0;

    // Cheque Payment
    public $cheques = [];
    public $tempChequeNumber = '';
    public $tempBankName = '';
    public $tempChequeDate = '';
    public $tempChequeAmount = 0;

    // Bank Transfer Payment
    public $bankTransferFile = null;
    public $bankTransferAmount = 0;

    // Discount Properties
    public $additionalDiscount = 0;
    public $additionalDiscountType = 'fixed'; // 'fixed' or 'percentage'

    // Modals
    public $showSaleModal = false;
    public $showCustomerModal = false;
    public $showPaymentConfirmModal = false;
    public $lastSaleId = null;
    public $createdSale = null;
    public $pendingDueAmount = 0;

    public function mount()
    {
        $this->loadCustomers();
        $this->setDefaultCustomer();
        $this->tempChequeDate = now()->format('Y-m-d');
    }

    // Set default walking customer
    public function setDefaultCustomer()
    {
        // Find or create walking customer (only one)
        $walkingCustomer = Customer::where('name', 'Walking Customer')->first();
        
        if (!$walkingCustomer) {
            $walkingCustomer = Customer::create([
                'name' => 'Walking Customer',
                'phone' => 'xxxxx', // Empty phone number
                'email' => null,
                'address' => 'xxxxx',
                'type' => 'retail',
                'business_name' => null,
            ]);
            
            $this->loadCustomers(); // Reload customers after creating new one
        }
        
        $this->customerId = $walkingCustomer->id;
        $this->selectedCustomer = $walkingCustomer;
    }

    // Load customers for dropdown
    public function loadCustomers()
    {
        $this->customers = Customer::orderBy('name')->get();
    }

    // Computed Properties for Totals
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum('total');
    }

    public function getTotalDiscountProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return ($item['discount'] * $item['quantity']);
        });
    }

    public function getSubtotalAfterItemDiscountsProperty()
    {
        return $this->subtotal;
    }

    public function getAdditionalDiscountAmountProperty()
    {
        if (empty($this->additionalDiscount) || $this->additionalDiscount <= 0) {
            return 0;
        }

        if ($this->additionalDiscountType === 'percentage') {
            return ($this->subtotalAfterItemDiscounts * $this->additionalDiscount) / 100;
        }

        return min($this->additionalDiscount, $this->subtotalAfterItemDiscounts);
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotalAfterItemDiscounts - $this->additionalDiscountAmount;
    }

    public function getTotalPaidAmountProperty()
    {
        $total = 0;

        if ($this->paymentMethod === 'cash') {
            $total = $this->cashAmount;
        } elseif ($this->paymentMethod === 'cheque') {
            $total = collect($this->cheques)->sum('amount');
        } elseif ($this->paymentMethod === 'bank_transfer') {
            $total = $this->bankTransferAmount;
        }

        return $total;
    }

    public function getDueAmountProperty()
    {
        if ($this->paymentMethod === 'credit') {
            return $this->grandTotal;
        }
        return max(0, $this->grandTotal - (int)$this->totalPaidAmount);
    }

    public function getPaymentStatusProperty()
    {
        if ($this->paymentMethod === 'credit' || (int)$this->totalPaidAmount <= 0) {
            return 'pending';
        } elseif ((int)$this->totalPaidAmount >= $this->grandTotal) {
            return 'paid';
        } else {
            return 'partial';
        }
    }

    // Determine payment_type for database (must be 'full' or 'partial')
    public function getDatabasePaymentTypeProperty()
    {
        if ($this->paymentMethod === 'credit') {
            return 'partial';
        }
        if ((int)$this->totalPaidAmount >= $this->grandTotal) {
            return 'full';
        } else {
            return 'partial';
        }
    }

    // When customer is selected from dropdown
    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $this->selectedCustomer = $customer;
            }
        } else {
            // If customer is deselected, set back to walking customer
            $this->setDefaultCustomer();
        }
    }

    // When payment method changes
    public function updatedPaymentMethod($value)
    {
        // Reset all payment fields
        $this->cashAmount = 0;
        $this->cheques = [];
        $this->bankTransferFile = null;
        $this->bankTransferAmount = 0;

        if ($value === 'cash') {
            $this->cashAmount = $this->grandTotal;
        } elseif ($value === 'bank_transfer') {
            $this->bankTransferAmount = $this->grandTotal;
        }
    }

    // Auto-update cash amount when cart changes (if payment method is cash)
    public function updated($propertyName)
    {
        // If cart or discount changes, update payment amounts
        if (
            str_contains($propertyName, 'cart') ||
            str_contains($propertyName, 'additionalDiscount') ||
            str_contains($propertyName, 'additionalDiscountType')
        ) {

            if ($this->paymentMethod === 'cash') {
                $this->cashAmount = $this->grandTotal;
            } elseif ($this->paymentMethod === 'bank_transfer') {
                $this->bankTransferAmount = $this->grandTotal;
            }
        }
    }

    // Add Cheque
    public function addCheque()
    {
        $this->validate([
            'tempChequeNumber' => 'required|string|max:50',
            'tempBankName' => 'required|string|max:100',
            'tempChequeDate' => 'required|date',
            'tempChequeAmount' => 'required|numeric|min:0.01',
        ], [
            'tempChequeNumber.required' => 'Cheque number is required',
            'tempBankName.required' => 'Bank name is required',
            'tempChequeDate.required' => 'Cheque date is required',
            'tempChequeAmount.required' => 'Cheque amount is required',
            'tempChequeAmount.min' => 'Cheque amount must be greater than 0',
        ]);

        // Check if cheque number already exists
        $existingCheque = Cheque::where('cheque_number', $this->tempChequeNumber)->first();
        if ($existingCheque) {
            $this->js("Swal.fire('Error!', 'Cheque number already exists. Please use a different cheque number.', 'error');");
            return;
        }

        $this->cheques[] = [
            'number' => $this->tempChequeNumber,
            'bank_name' => $this->tempBankName,
            'date' => $this->tempChequeDate,
            'amount' => $this->tempChequeAmount,
        ];

        // Reset temporary fields
        $this->tempChequeNumber = '';
        $this->tempBankName = '';
        $this->tempChequeDate = now()->format('Y-m-d');
        $this->tempChequeAmount = 0;

        $this->js("Swal.fire('Success!', 'Cheque added successfully!', 'success')");
    }

    // Remove Cheque
    public function removeCheque($index)
    {
        unset($this->cheques[$index]);
        $this->cheques = array_values($this->cheques);
        $this->js("Swal.fire('success', 'Cheque removed successfully!', 'success')");
    }

    // Reset customer fields
    public function resetCustomerFields()
    {
        $this->customerName = '';
        $this->customerPhone = '';
        $this->customerEmail = '';
        $this->customerAddress = '';
        $this->customerType = 'retail';
        $this->businessName = '';
    }

    // Open customer modal
    public function openCustomerModal()
    {
        $this->resetCustomerFields();
        $this->showCustomerModal = true;
    }

    // Close customer modal
    public function closeCustomerModal()
    {
        $this->showCustomerModal = false;
        $this->resetCustomerFields();
    }

    // Create new customer
    public function createCustomer()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'nullable|string|max:20|unique:customers,phone',
            'customerEmail' => 'nullable|email|unique:customers,email',
            'customerAddress' => 'required|string',
            'customerType' => 'required|in:retail,wholesale,business',
        ]);

        try {
            $customer = Customer::create([
                'name' => $this->customerName,
                'phone' => $this->customerPhone ?: null,
                'email' => $this->customerEmail,
                'address' => $this->customerAddress,
                'type' => $this->customerType,
                'business_name' => $this->businessName,
            ]);

            $this->loadCustomers();
            $this->customerId = $customer->id;
            $this->selectedCustomer = $customer;
            $this->closeCustomerModal();

            $this->js("Swal.fire('success', 'Customer created successfully!', 'success')");
        } catch (\Exception $e) {
            $this->js("Swal.fire('error', 'Failed to create customer: ', 'error')");
        }
    }

    // Search Products
    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->searchResults = ProductDetail::with(['stock', 'price'])
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%')
                ->orWhere('model', 'like', '%' . $this->search . '%')
                ->take(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'code' => $product->code,
                        'model' => $product->model,
                        'price' => $product->price->selling_price ?? 0,
                        'stock' => $product->stock->available_stock ?? 0,
                        'image' => $product->image
                    ];
                });
        } else {
            $this->searchResults = [];
        }
    }

    // Add to Cart
    public function addToCart($product)
    {
        if (($product['stock'] ?? 0) <= 0) {
            $this->js("Swal.fire('error', 'Not enough stock available!', 'error')");
            return;
        }

        $existing = collect($this->cart)->firstWhere('id', $product['id']);

        if ($existing) {
            if (($existing['quantity'] + 1) > $product['stock']) {
                $this->js("Swal.fire('error', 'Not enough stock available!', 'error')");
                return;
            }

            $this->cart = collect($this->cart)->map(function ($item) use ($product) {
                if ($item['id'] == $product['id']) {
                    $item['quantity'] += 1;
                    $item['total'] = ($item['price'] - $item['discount']) * $item['quantity'];
                }
                return $item;
            })->toArray();
        } else {
            $discountPrice = ProductDetail::find($product['id'])->price->discount_price ?? 0;

            $this->cart[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'code' => $product['code'],
                'model' => $product['model'],
                'price' => $product['price'],
                'quantity' => 1,
                'discount' => $discountPrice,
                'total' => $product['price'] - $discountPrice,
                'stock' => $product['stock']
            ];
        }

        $this->search = '';
        $this->searchResults = [];
    }

    // Update Quantity
    public function updateQuantity($index, $quantity)
    {
        if ($quantity < 1) $quantity = 1;

        $productStock = $this->cart[$index]['stock'];
        if ($quantity > $productStock) {
            $this->js("Swal.fire('error', 'Not enough stock available! Maximum: ' . $productStock, 'error')");
            return;
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['total'] = ($this->cart[$index]['price'] - $this->cart[$index]['discount']) * $quantity;
    }

    // Increment Quantity
    public function incrementQuantity($index)
    {
        $currentQuantity = $this->cart[$index]['quantity'];
        $productStock = $this->cart[$index]['stock'];

        if (($currentQuantity + 1) > $productStock) {
            $this->js("Swal.fire('error', 'Not enough stock available! Maximum: ' . $productStock, 'error')");
            return;
        }

        $this->cart[$index]['quantity'] += 1;
        $this->cart[$index]['total'] = ($this->cart[$index]['price'] - $this->cart[$index]['discount']) * $this->cart[$index]['quantity'];
    }

    // Decrement Quantity
    public function decrementQuantity($index)
    {
        if ($this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity'] -= 1;
            $this->cart[$index]['total'] = ($this->cart[$index]['price'] - $this->cart[$index]['discount']) * $this->cart[$index]['quantity'];
        }
    }

    // Update Discount
    public function updateDiscount($index, $discount)
    {
        if ($discount < 0) $discount = 0;
        if ($discount > $this->cart[$index]['price']) {
            $discount = $this->cart[$index]['price'];
        }

        $this->cart[$index]['discount'] = $discount;
        $this->cart[$index]['total'] = ($this->cart[$index]['price'] - $discount) * $this->cart[$index]['quantity'];
    }

    // Remove from Cart
    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->js("Swal.fire('success', 'Product removed from sale!', 'success')");
    }

    // Clear Cart
    public function clearCart()
    {
        $this->cart = [];
        $this->additionalDiscount = 0;
        $this->additionalDiscountType = 'fixed';
        $this->resetPaymentFields();
        $this->js("Swal.fire('success', 'Cart cleared!', 'success')");
    }

    // Reset payment fields
    public function resetPaymentFields()
    {
        $this->cashAmount = 0;
        $this->cheques = [];
        $this->bankTransferFile = null;
        $this->bankTransferAmount = 0;
        $this->paymentMethod = 'cash';
    }

    // Update additional discount
    public function updatedAdditionalDiscount($value)
    {
        if ($value === '') {
            $this->additionalDiscount = 0;
            return;
        }

        if ($value < 0) {
            $this->additionalDiscount = 0;
            return;
        }

        if ($this->additionalDiscountType === 'percentage' && $value > 100) {
            $this->additionalDiscount = 100;
            return;
        }

        if ($this->additionalDiscountType === 'fixed' && $value > $this->subtotalAfterItemDiscounts) {
            $this->additionalDiscount = $this->subtotalAfterItemDiscounts;
            return;
        }
    }

    public function toggleDiscountType()
    {
        $this->additionalDiscountType = $this->additionalDiscountType === 'percentage' ? 'fixed' : 'percentage';
        $this->additionalDiscount = 0;
    }

    public function removeAdditionalDiscount()
    {
        $this->additionalDiscount = 0;
        $this->js("Swal.fire('success', 'Additional discount removed!', 'success')");
    }

    // Validate Payment Before Creating Sale
    public function validateAndCreateSale()
    {
        if (empty($this->cart)) {
            $this->js("Swal.fire('error', 'Please add at least one product to the sale.', 'error')");
            return;
        }

        // If no customer selected, use walking customer
        if (!$this->selectedCustomer && !$this->customerId) {
            $this->js("Swal.fire('error', 'Please select a customer.', 'error')");
            return;
            $this->setDefaultCustomer();
        }

        // Validate payment method specific fields
        if ($this->paymentMethod === 'cash') {
            if ($this->cashAmount <= 0) {
                $this->js("Swal.fire('error', 'Please enter cash amount.', 'error')");
                return;
            }
        } elseif ($this->paymentMethod === 'cheque') {
            if (empty($this->cheques)) {
                $this->js("Swal.fire('error', 'Please add at least one cheque.', 'error')");
                return;
            }
        } elseif ($this->paymentMethod === 'bank_transfer') {
            if ($this->bankTransferAmount <= 0) {
                $this->js("Swal.fire('error', 'Please enter bank transfer amount.', 'error')");
                return;
            }
        }

        // Check if payment amount matches grand total (except for credit)
        if ($this->paymentMethod !== 'credit') {
            if ((int)$this->totalPaidAmount < $this->grandTotal) {
                // Show confirmation modal for due amount
                $this->pendingDueAmount = $this->grandTotal - (int)$this->totalPaidAmount;
                $this->showPaymentConfirmModal = true;
                return;
            }
        }

        // Proceed to create sale
        $this->createSale();
    }

    // Confirm and Create Sale with Due Amount
    public function confirmSaleWithDue()
    {
        $this->showPaymentConfirmModal = false;
        $this->createSale();
    }

    // Cancel Sale Confirmation
    public function cancelSaleConfirmation()
    {
        $this->showPaymentConfirmModal = false;
        $this->pendingDueAmount = 0;
    }

    // Create Sale
    public function createSale()
    {
        try {
            DB::beginTransaction();

            // Get customer data
            $customer = $this->selectedCustomer ?? Customer::find($this->customerId);

            if (!$customer) {
                $this->js("Swal.fire('error', 'Customer not found.', 'error')");
                return;
            }

            // Create sale
            $sale = Sale::create([
                'sale_id' => Sale::generateSaleId(),
                'invoice_number' => Sale::generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'customer_type' => $customer->type,
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->totalDiscount + $this->additionalDiscountAmount,
                'total_amount' => $this->grandTotal,
                'payment_type' => $this->databasePaymentType,
                'payment_status' => $this->paymentStatus,
                'due_amount' => $this->dueAmount,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
                'status' => 'confirm',
                'sale_type' => 'pos'
            ]);

            // Create sale items and update stock
            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'product_code' => $item['code'],
                    'product_name' => $item['name'],
                    'product_model' => $item['model'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount_per_unit' => $item['discount'],
                    'total_discount' => $item['discount'] * $item['quantity'],
                    'total' => $item['total']
                ]);

                // Update product stock
                $product = ProductDetail::find($item['id']);
                if ($product && $product->stock) {
                    $product->stock->available_stock -= $item['quantity'];
                    $product->stock->save();
                }
            }

            // Create Payment Record
            if ($this->paymentMethod !== 'credit' && (int)$this->totalPaidAmount > 0) {
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'amount' => (int)$this->totalPaidAmount,
                    'payment_method' => $this->paymentMethod,
                    'payment_date' => now(),
                    'is_completed' => true,
                    'status' =>  'paid',
                ]);

                // Handle payment method specific data
                if ($this->paymentMethod === 'cash') {
                    $payment->update([
                        'payment_reference' => 'CASH-' . now()->format('YmdHis'),
                    ]);
                } elseif ($this->paymentMethod === 'cheque') {
                    // Create cheque records
                    foreach ($this->cheques as $cheque) {
                        Cheque::create([
                            'cheque_number' => $cheque['number'],
                            'cheque_date' => $cheque['date'],
                            'bank_name' => $cheque['bank_name'],
                            'cheque_amount' => $cheque['amount'],
                            'status' => 'pending',
                            'customer_id' => $customer->id,
                            'payment_id' => $payment->id,
                        ]);
                    }

                    $payment->update([
                        'payment_reference' => 'CHQ-' . collect($this->cheques)->pluck('number')->implode(','),
                        'bank_name' => collect($this->cheques)->pluck('bank_name')->unique()->implode(', '),
                    ]);
                } elseif ($this->paymentMethod === 'bank_transfer') {
                    $attachmentPath = null;
                    if ($this->bankTransferFile) {
                        $attachmentPath = $this->bankTransferFile->store('bank_transfers', 'public');
                    }

                    $payment->update([
                        'payment_reference' => 'BANK-' . now()->format('YmdHis'),
                        'due_payment_attachment' => $attachmentPath,
                    ]);
                }
            }

            DB::commit();

            $this->lastSaleId = $sale->id;
            $this->createdSale = Sale::with(['customer', 'items', 'payments'])->find($sale->id);
            $this->showSaleModal = true;

            $statusMessage = 'Sale created successfully! Payment status: ' . ucfirst($this->paymentStatus);
            if ($this->dueAmount > 0) {
                $statusMessage .= ' | Due Amount: Rs.' . number_format($this->dueAmount, 2);
            }

            $this->js("Swal.fire('success', '$statusMessage', 'success')");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->js("Swal.fire('error', 'Failed to create sale: ' , 'error')");
        }
    }

    // Download Invoice
    public function downloadInvoice()
    {
        if (!$this->lastSaleId) {
            $this->js("Swal.fire('error', 'No sale found to download.', 'error')");
            return;
        }

        $sale = Sale::with(['customer', 'items', 'returns' => function ($q) {
            $q->with('product');
        }])->find($this->lastSaleId);

        if (!$sale) {
            $this->js("Swal.fire('error', 'Sale not found.', 'error')");
            return;
        }

        $pdf = PDF::loadView('admin.sales.invoice', compact('sale'));

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            'invoice-' . $sale->invoice_number . '.pdf'
        );
    }

    // Close Modal
    public function closeModal()
    {
        $this->showSaleModal = false;
        $this->lastSaleId = null;
        $this->createdSale = null;
    }

    // Continue creating new sale
    public function createNewSale()
    {
        $this->resetExcept(['customers']);
        $this->loadCustomers();
        $this->setDefaultCustomer(); // Set walking customer again for new sale
        $this->showSaleModal = false;
    }

    public function render()
    {
        return view('livewire.admin.store-billing', [
            'subtotal' => $this->subtotal,
            'totalDiscount' => $this->totalDiscount,
            'subtotalAfterItemDiscounts' => $this->subtotalAfterItemDiscounts,
            'additionalDiscountAmount' => $this->additionalDiscountAmount,
            'grandTotal' => $this->grandTotal,
            'dueAmount' => $this->dueAmount,
            'paymentStatus' => $this->paymentStatus,
            'databasePaymentType' => $this->databasePaymentType,
            'totalPaidAmount' => (int)$this->totalPaidAmount,
        ]);
    }
}
