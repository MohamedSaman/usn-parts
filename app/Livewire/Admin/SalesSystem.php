<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Customer;
use App\Models\ProductDetail;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('components.layouts.admin')]
#[Title('Create Sale')]
class SalesSystem extends Component
{
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
    
    // Discount Properties
    public $additionalDiscount = 0;
    public $additionalDiscountType = 'fixed'; // 'fixed' or 'percentage'
    
    // Modals
    public $showSaleModal = false;
    public $showCustomerModal = false;
    public $lastSaleId = null;
    public $createdSale = null;

    public function mount()
    {
        $this->loadCustomers();
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
        return collect($this->cart)->sum(function($item) {
            return ($item['discount'] * $item['quantity']);
        });
    }

    public function getSubtotalAfterItemDiscountsProperty()
    {
        return $this->subtotal - $this->totalDiscount;
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

    // When customer is selected from dropdown
    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $this->selectedCustomer = $customer;
            }
        } else {
            $this->selectedCustomer = null;
        }
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
            'customerPhone' => 'required|string|max:20|unique:customers,phone',
            'customerEmail' => 'nullable|email|unique:customers,email',
            'customerAddress' => 'required|string',
            'customerType' => 'required|in:retail,wholesale,business',
        ]);

        try {
            $customer = Customer::create([
                'name' => $this->customerName,
                'phone' => $this->customerPhone,
                'email' => $this->customerEmail,
                'address' => $this->customerAddress,
                'type' => $this->customerType,
                'business_name' => $this->businessName,
            ]);

            $this->loadCustomers();
            $this->customerId = $customer->id;
            $this->selectedCustomer = $customer;
            $this->closeCustomerModal();
            
            session()->flash('message', 'Customer created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create customer: ' . $e->getMessage());
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
                ->map(function($product) {
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
        // Check stock availability
        if (($product['stock'] ?? 0) <= 0) {
            session()->flash('error', 'Product is out of stock!');
            return;
        }

        $existing = collect($this->cart)->firstWhere('id', $product['id']);
        
        if ($existing) {
            // Check if adding more exceeds stock
            if (($existing['quantity'] + 1) > $product['stock']) {
                session()->flash('error', 'Not enough stock available!');
                return;
            }

            $this->cart = collect($this->cart)->map(function($item) use ($product) {
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
        session()->flash('message', 'Product added to sale!');
    }

    // Update Quantity
    public function updateQuantity($index, $quantity)
    {
        if ($quantity < 1) $quantity = 1;
        
        // Check stock availability
        $productStock = $this->cart[$index]['stock'];
        if ($quantity > $productStock) {
            session()->flash('error', 'Not enough stock available! Maximum: ' . $productStock);
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
            session()->flash('error', 'Not enough stock available! Maximum: ' . $productStock);
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
        session()->flash('message', 'Product removed from sale!');
    }

    // Clear Cart
    public function clearCart()
    {
        $this->cart = [];
        $this->additionalDiscount = 0;
        $this->additionalDiscountType = 'fixed';
        session()->flash('message', 'Cart cleared!');
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
        session()->flash('message', 'Additional discount removed!');
    }

    // Create Sale
    public function createSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Please add at least one product to the sale.');
            return;
        }

        if (!$this->selectedCustomer && !$this->customerId) {
            session()->flash('error', 'Please select a customer.');
            return;
        }

        try {
            DB::beginTransaction();

            // Get customer data
            if ($this->selectedCustomer) {
                $customer = $this->selectedCustomer;
            } else {
                $customer = Customer::find($this->customerId);
            }

            if (!$customer) {
                session()->flash('error', 'Customer not found.');
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
                'payment_type' => 'full',
                'payment_status' => 'pending',
                'due_amount' => $this->grandTotal,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
                'status' => 'confirm',
                'sale_type' => 'admin'
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

            DB::commit();

            $this->lastSaleId = $sale->id;
            $this->createdSale = Sale::with(['customer', 'items'])->find($sale->id);
            $this->showSaleModal = true;
            
            session()->flash('success', 'Sale created successfully! Payment status: Pending');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create sale: ' . $e->getMessage());
        }
    }

    // Download Invoice
    public function downloadInvoice()
    {
        if (!$this->lastSaleId) {
            session()->flash('error', 'No sale found to download.');
            return;
        }

        $sale = Sale::with(['customer', 'items'])->find($this->lastSaleId);
        
        if (!$sale) {
            session()->flash('error', 'Sale not found.');
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
        $this->showSaleModal = false;
        session()->flash('message', 'Ready to create new sale!');
    }

    public function render()
    {
        return view('livewire.admin.sales-system', [
            'subtotal' => $this->subtotal,
            'totalDiscount' => $this->totalDiscount,
            'subtotalAfterItemDiscounts' => $this->subtotalAfterItemDiscounts,
            'additionalDiscountAmount' => $this->additionalDiscountAmount,
            'grandTotal' => $this->grandTotal
        ]);
    }
}