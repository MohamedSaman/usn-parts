<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Customer;
use App\Models\ProductDetail;
use App\Models\Sale;
use App\Models\ProductStock;
use App\Models\ReturnsProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title("Product Return")]
#[Layout('components.layouts.admin')]
class ReturnProduct extends Component
{
    public $searchCustomer = '';
    public $customers = [];
    public $selectedCustomer = null;

    public $customerInvoices = [];
    public $selectedInvoice = null;
    public $selectedInvoices = []; // For multiple invoice selection

    public $invoiceProducts = [];
    public $returnItems = [];
    public $totalReturnValue = 0;

    public $showInvoiceModal = false;
    public $invoiceModalData = null;

    public $showReturnSection = false;
    public $searchReturnProduct = '';
    public $availableProducts = [];
    public $invoiceProductsForSearch = []; // Products from selected invoice for search
    public $selectedProducts = []; // Products selected for return

    /** 🔍 Search Customer or Invoice */
    public function updatedSearchCustomer()
    {
        if (strlen($this->searchCustomer) > 2) {
            $this->customers = Customer::query()
                ->where('name', 'like', '%' . $this->searchCustomer . '%')
                ->orWhere('phone', 'like', '%' . $this->searchCustomer . '%')
                ->orWhere('email', 'like', '%' . $this->searchCustomer . '%')
                ->limit(10)
                ->get();

            // Search invoices by invoice number
            $this->customerInvoices = Sale::where('invoice_number', 'like', '%' . $this->searchCustomer . '%')
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $this->customers = [];
            $this->customerInvoices = [];
        }
    }

    /** 👤 Select Customer */
    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->searchCustomer = '';
        $this->customers = [];

        // Reset all selections when selecting a new customer
        $this->selectedInvoice = null;
        $this->selectedInvoices = [];
        $this->invoiceProducts = [];
        $this->returnItems = [];
        $this->selectedProducts = [];
        $this->showReturnSection = false;
        $this->searchReturnProduct = '';
        $this->availableProducts = [];
        $this->invoiceProductsForSearch = [];
        $this->totalReturnValue = 0;

        $this->loadCustomerInvoices();
    }

    /** 🧾 Load Selected Customer’s Invoices */
    public function loadCustomerInvoices()
    {
        if (!$this->selectedCustomer) {
            $this->customerInvoices = [];
            return;
        }

        $this->customerInvoices = Sale::where('customer_id', $this->selectedCustomer->id)
            ->latest()
            ->limit(5)
            ->get();
    }

    /** 📦 Load Products from Selected Invoice */
    public function selectInvoice($invoiceId)
    {
        $this->selectedInvoice = Sale::with(['items.product'])->find($invoiceId);

        if ($this->selectedInvoice) {
            $this->invoiceProducts = $this->selectedInvoice->items->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'code' => $item->product->code,
                    'image' => $item->product->image,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray();

            // Build returnItems array with all required keys
            $this->returnItems = [];
            foreach ($this->selectedInvoice->items as $item) {
                $this->returnItems[] = [
                    'product_id' => $item->product->id,
                    'name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'max_qty' => $item->quantity,
                    'return_qty' => 0,
                ];
            }
        }

        // Also update the selected invoices for the new search functionality
        if (!in_array($invoiceId, $this->selectedInvoices)) {
            $this->selectedInvoices[] = $invoiceId;
            $this->showReturnSection = true;
            $this->loadInvoiceProductsForSearch();
        }
    }

    /** 🎯 Simple Invoice Selection for Return */
    public function selectInvoiceForReturn($invoiceId)
    {
        // Clear previous selections
        $this->selectedInvoice = null;
        $this->selectedInvoices = [];
        $this->invoiceProducts = [];
        $this->returnItems = [];
        $this->selectedProducts = [];
        $this->searchReturnProduct = '';
        $this->availableProducts = [];
        $this->invoiceProductsForSearch = [];
        $this->totalReturnValue = 0;

        // Set new selection with customer relationship
        $this->selectedInvoice = Sale::with(['items.product', 'customer'])->find($invoiceId);
        $this->selectedInvoices = [$invoiceId];
        $this->showReturnSection = true;

        // Set the selected customer from the invoice
        if ($this->selectedInvoice && $this->selectedInvoice->customer) {
            $this->selectedCustomer = $this->selectedInvoice->customer;
        }

        // Build returnItems array with all required keys
        if ($this->selectedInvoice) {
            foreach ($this->selectedInvoice->items as $item) {
                $this->returnItems[] = [
                    'product_id' => $item->product->id,
                    'name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'max_qty' => $item->quantity,
                    'return_qty' => 0,
                ];
            }
        }

        // Load products for search
        $this->loadInvoiceProductsForSearch();

        // Clear search field
        $this->searchCustomer = '';
    }

    /** 👁️ View Invoice Details in Modal */
    public function viewInvoice($invoiceId)
    {
        $invoice = Sale::with(['items.product', 'customer'])->find($invoiceId);

        if ($invoice) {
            $this->invoiceModalData = [
                'invoice_number' => $invoice->invoice_number,
                'customer_name' => $invoice->customer->name,
                'date' => $invoice->created_at->format('Y-m-d H:i:s'),
                'total_amount' => $invoice->total_amount,
                'items' => $invoice->items->map(function ($item) {
                    return [
                        'product_name' => $item->product->name,
                        'product_code' => $item->product->code,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->quantity * $item->unit_price,
                    ];
                })->toArray()
            ];
            $this->showInvoiceModal = true;
            $this->dispatch('show-invoice-modal');
        }
    }



    /** 📦 Load Products from Selected Invoice for Search */
    private function loadInvoiceProductsForSearch()
    {
        if (empty($this->selectedInvoices)) {
            $this->invoiceProductsForSearch = [];
            return;
        }

        $allProducts = collect();

        foreach ($this->selectedInvoices as $invoiceId) {
            $invoice = Sale::with(['items.product.price'])->find($invoiceId);
            if ($invoice) {
                $products = $invoice->items->map(function ($item) use ($invoice) {
                    return [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'code' => $item->product->code,
                        'image' => $item->product->image,
                        'selling_price' => $item->unit_price, // Use the price from the sale item
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'max_qty' => $item->quantity,
                    ];
                });
                $allProducts = $allProducts->merge($products);
            }
        }

        // Remove duplicates based on product ID and convert to array
        $this->invoiceProductsForSearch = $allProducts->unique('id')->values()->toArray();
    }

    /** 🔍 Search Products for Return */
    public function updatedSearchReturnProduct()
    {
        if (strlen($this->searchReturnProduct) >= 1 && !empty($this->invoiceProductsForSearch)) {
            $returnProductIds = array_column($this->returnItems, 'product_id');

            $this->availableProducts = collect($this->invoiceProductsForSearch)->filter(function ($product) use ($returnProductIds) {
                $matchesSearch = stripos($product['name'], $this->searchReturnProduct) !== false ||
                    stripos($product['code'], $this->searchReturnProduct) !== false;

                return $matchesSearch && !in_array($product['id'], $returnProductIds);
            })->take(10)->values()->toArray();
        } else {
            $this->availableProducts = [];
        }
    }

    /** ➕ Add Product Directly to Return Cart */
    public function addProductDirectlyToReturn($productId)
    {
        $product = collect($this->invoiceProductsForSearch)->firstWhere('id', $productId);

        if (!$product) {
            $this->dispatch('alert', ['message' => 'Product not found!']);
            return;
        }

        // Check if product is already in return cart
        $existingIndex = collect($this->returnItems)->search(fn($item) => $item['product_id'] == $productId);

        if ($existingIndex !== false) {
            // Increase quantity if already exists
            $currentQty = $this->returnItems[$existingIndex]['return_qty'];
            if ($currentQty < $product['max_qty']) {
                $this->returnItems[$existingIndex]['return_qty'] = $currentQty + 1;
            } else {
                $this->dispatch('alert', ['message' => 'Maximum quantity reached for ' . $product['name']]);
            }
        } else {
            // Add new product to return cart
            $this->returnItems[] = [
                'product_id' => $product['id'],
                'name' => $product['name'],
                'unit_price' => $product['selling_price'],
                'max_qty' => $product['max_qty'],
                'return_qty' => 1,
            ];
        }

        // Clear search and recalculate
        $this->searchReturnProduct = '';
        $this->availableProducts = [];
        $this->calculateTotalReturnValue();
    }

    /** ❌ Close Invoice Modal */
    public function closeInvoiceModal()
    {
        $this->showInvoiceModal = false;
        $this->invoiceModalData = null;
    }



    /** ❌ Remove Product from Return Cart */
    public function removeFromReturn($index)
    {
        unset($this->returnItems[$index]);
        $this->returnItems = array_values($this->returnItems);
        $this->calculateTotalReturnValue();
    }

    /** 🧹 Clear Cart */
    public function clearReturnCart()
    {
        $this->returnItems = [];
        $this->totalReturnValue = 0;
    }

    /** ♻️ Auto-update total when quantities change */
    public function updatedReturnItems()
    {
        $this->calculateTotalReturnValue();
    }

    /** 💰 Calculate Total Return Value */
    private function calculateTotalReturnValue()
    {
        $this->totalReturnValue = collect($this->returnItems)->sum(
            fn($item) =>
            $item['return_qty'] * $item['unit_price']
        );
    }

    /** ✅ Validate before showing confirmation */
    public function processReturn()
    {
        // Force recalculation
        $this->calculateTotalReturnValue();

        if (empty($this->returnItems) || !$this->selectedInvoice) {
            $this->dispatch('alert', ['message' => 'Please select items for return.']);
            return;
        }

        // Check if at least one item has a return quantity > 0
        $hasReturnItems = false;
        foreach ($this->returnItems as $item) {
            if (isset($item['return_qty']) && $item['return_qty'] > 0) {
                if ($item['return_qty'] > $item['max_qty']) {
                    $this->dispatch('alert', ['message' => 'Invalid return quantity for ' . $item['name']]);
                    return;
                }
                $hasReturnItems = true;
            }
        }

        if (!$hasReturnItems) {
            $this->dispatch('alert', ['message' => 'Please enter at least one return quantity.']);
            return;
        }

        $this->dispatch('show-return-modal');
    }

    /** 💾 Confirm Return & Save to Database */
    public function confirmReturn()
    {
        // Force recalculation and syncing
        $this->calculateTotalReturnValue();

        if (empty($this->returnItems) || !$this->selectedCustomer || !$this->selectedInvoice) return;

        // Filter only items with return_qty > 0
        $itemsToReturn = array_filter($this->returnItems, function ($item) {
            return isset($item['return_qty']) && $item['return_qty'] > 0;
        });

        if (empty($itemsToReturn)) {
            $this->dispatch('alert', ['message' => 'No valid return quantities entered.']);
            return;
        }

        // Debug log for troubleshooting
        Log::info('ReturnProduct: Saving items', $itemsToReturn);

        DB::transaction(function () use ($itemsToReturn) {
            foreach ($itemsToReturn as $item) {
                ReturnsProduct::create([
                    'sale_id' => $this->selectedInvoice->id,
                    'product_id' => $item['product_id'],
                    'return_quantity' => $item['return_qty'],
                    'selling_price' => $item['unit_price'],
                    'total_amount' => $item['return_qty'] * $item['unit_price'],
                    'notes' => 'Customer return processed via system',
                ]);

                // ✅ Update stock (increase available stock)
                $this->updateProductStock($item['product_id'], $item['return_qty']);
            }
        });

        $this->clearReturnCart();
        $this->dispatch('alert', ['message' => 'Return processed successfully!']);
        $this->dispatch('close-return-modal');
        $this->dispatch('reload-page');
    }

    /** 📈 Update Product Stock */
    private function updateProductStock($productId, $quantity)
    {
        $stock = ProductStock::where('product_id', $productId)->first();

        if ($stock) {
            $stock->available_stock += $quantity;
            $stock->total_stock += $quantity;
            $stock->save();
        } else {
            ProductStock::create([
                'product_id' => $productId,
                'available_stock' => $quantity,
                'damage_stock' => 0,
                'total_stock' => $quantity,
                'sold_count' => 0,
                'restocked_quantity' => 0,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.return-product');
    }
}
