<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductSupplier;
use App\Models\ProductDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ProductStock;
use App\Models\ReturnSupplier as ReturnSupplierModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\WithDynamicLayout;

#[Title("Supplier Return")]
class ReturnSupplier extends Component
{
    use WithDynamicLayout;

    public $searchSupplier = '';
    public $suppliers = [];
    public $selectedSupplier = null;

    public $supplierPurchaseOrders = [];
    public $selectedPurchaseOrder = null;
    public $selectedPurchaseOrders = [];

    public $purchaseOrderProducts = [];
    public $returnItems = [];
    public $totalReturnValue = 0;
    public $overallDiscountPerItem = 0;

    public $showPurchaseOrderModal = false;
    public $purchaseOrderModalData = null;

    public $showReturnSection = false;
    public $searchReturnProduct = '';
    public $availableProducts = [];
    public $purchaseOrderProductsForSearch = [];
    public $selectedProducts = [];

    public $previousReturns = []; // Track previously returned items
    public $returnReason = 'damaged'; // Default return reason

    /** 🔍 Search Supplier or Purchase Order */
    public function updatedSearchSupplier()
    {
        if (strlen($this->searchSupplier) > 2) {
            $this->suppliers = ProductSupplier::query()
                ->where('name', 'like', '%' . $this->searchSupplier . '%')
                ->orWhere('phone', 'like', '%' . $this->searchSupplier . '%')
                ->orWhere('email', 'like', '%' . $this->searchSupplier . '%')
                ->limit(10)
                ->get();

            $this->supplierPurchaseOrders = PurchaseOrder::where('order_code', 'like', '%' . $this->searchSupplier . '%')
                ->latest()
                ->limit(5)
                ->get();
        } else {
            $this->suppliers = [];
            $this->supplierPurchaseOrders = [];
        }
    }

    /** 👤 Select Supplier */
    public function selectSupplier($supplierId)
    {
        $this->selectedSupplier = ProductSupplier::find($supplierId);
        $this->searchSupplier = '';
        $this->suppliers = [];

        $this->resetReturnData();
        $this->loadSupplierPurchaseOrders();
    }

    /** 🧾 Load Selected Supplier's Purchase Orders */
    public function loadSupplierPurchaseOrders()
    {
        if (!$this->selectedSupplier) {
            $this->supplierPurchaseOrders = [];
            return;
        }

        $this->supplierPurchaseOrders = PurchaseOrder::where('supplier_id', $this->selectedSupplier->id)
            ->whereIn('status', ['received', 'complete'])
            ->latest()
            ->limit(5)
            ->get();
    }

    /** 🎯 Simple Purchase Order Selection for Return */
    public function selectPurchaseOrderForReturn($purchaseOrderId)
    {
        $this->resetReturnData();

        $this->selectedPurchaseOrder = PurchaseOrder::with(['items.product', 'supplier'])->find($purchaseOrderId);
        $this->selectedPurchaseOrders = [$purchaseOrderId];
        $this->showReturnSection = true;

        if ($this->selectedPurchaseOrder && $this->selectedPurchaseOrder->supplier) {
            $this->selectedSupplier = $this->selectedPurchaseOrder->supplier;
        }

        if ($this->selectedPurchaseOrder) {
            // Calculate overall discount per item
            $this->calculateOverallDiscountPerItem();

            // Load previous returns for this purchase order
            $this->loadPreviousReturns();

            // Build return items with remaining quantities
            foreach ($this->selectedPurchaseOrder->items as $item) {
                $alreadyReturned = $this->getAlreadyReturnedQuantity($item->product->id);
                $remainingQty = $item->quantity - $alreadyReturned;

                if ($remainingQty > 0) {
                    // Calculate per-unit discount (item discount + proportional overall discount)
                    $itemDiscount = $item->discount ?? 0;
                    $proportionalOverallDiscount = $this->overallDiscountPerItem;
                    $totalDiscountPerUnit = $itemDiscount + $proportionalOverallDiscount;

                    $this->returnItems[] = [
                        'purchase_order_item_id' => $item->id,
                        'product_id' => $item->product->id,
                        'name' => $item->product->name,
                        'code' => $item->product->code,
                        'unit_price' => $item->unit_price,
                        'discount_per_unit' => $itemDiscount,
                        'overall_discount_per_unit' => $proportionalOverallDiscount,
                        'total_discount_per_unit' => $totalDiscountPerUnit,
                        'net_unit_price' => $item->unit_price - $totalDiscountPerUnit,
                        'original_qty' => $item->quantity,
                        'already_returned' => $alreadyReturned,
                        'max_qty' => $remainingQty,
                        'return_qty' => 0,
                        'return_reason' => 'damaged',
                    ];
                }
            }
        }

        $this->loadPurchaseOrderProductsForSearch();
        $this->searchSupplier = '';
    }

    /** 📊 Calculate Overall Discount Per Item */
    private function calculateOverallDiscountPerItem()
    {
        if (!$this->selectedPurchaseOrder) {
            $this->overallDiscountPerItem = 0;
            return;
        }

        $totalQuantity = $this->selectedPurchaseOrder->items->sum('quantity');
        $overallDiscount = $this->selectedPurchaseOrder->discount_amount ?? 0;

        $this->overallDiscountPerItem = $totalQuantity > 0 ? ($overallDiscount / $totalQuantity) : 0;
    }

    /** 📜 Load Previous Returns */
    private function loadPreviousReturns()
    {
        if (!$this->selectedPurchaseOrder) {
            $this->previousReturns = [];
            return;
        }

        $this->previousReturns = ReturnSupplierModel::where('purchase_order_id', $this->selectedPurchaseOrder->id)
            ->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function ($returns) {
                return [
                    'product_name' => $returns->first()->product->name ?? 'Unknown',
                    'total_returned' => $returns->sum('return_quantity'),
                    'total_amount' => $returns->sum('total_amount'),
                    'returns' => $returns->map(function ($return) {
                        return [
                            'quantity' => $return->return_quantity,
                            'amount' => $return->total_amount,
                            'reason' => $return->return_reason,
                            'date' => $return->created_at->format('Y-m-d H:i'),
                        ];
                    })->toArray()
                ];
            })
            ->toArray();
    }

    /** 🔢 Get Already Returned Quantity */
    private function getAlreadyReturnedQuantity($productId)
    {
        if (!$this->selectedPurchaseOrder) return 0;

        return ReturnSupplierModel::where('purchase_order_id', $this->selectedPurchaseOrder->id)
            ->where('product_id', $productId)
            ->sum('return_quantity');
    }

    /** 👁️ View Purchase Order Details in Modal */
    public function viewPurchaseOrder($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::with(['items.product', 'supplier'])->find($purchaseOrderId);

        if ($purchaseOrder) {
            $overallDiscount = $purchaseOrder->discount_amount ?? 0;
            $totalQty = $purchaseOrder->items->sum('quantity');
            $discountPerItem = $totalQty > 0 ? ($overallDiscount / $totalQty) : 0;

            $this->purchaseOrderModalData = [
                'order_code' => $purchaseOrder->order_code,
                'supplier_name' => $purchaseOrder->supplier->name,
                'date' => $purchaseOrder->created_at->format('Y-m-d H:i:s'),
                'total_amount' => $purchaseOrder->total_amount,
                'overall_discount' => $overallDiscount,
                'items' => $purchaseOrder->items->map(function ($item) use ($discountPerItem) {
                    $itemDiscount = $item->discount ?? 0;
                    $totalDiscountPerUnit = $itemDiscount + $discountPerItem;
                    $netPrice = $item->unit_price - $totalDiscountPerUnit;

                    return [
                        'product_name' => $item->product->name,
                        'product_code' => $item->product->code,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'item_discount' => $itemDiscount,
                        'overall_discount' => $discountPerItem,
                        'net_price' => $netPrice,
                        'total' => $item->quantity * $netPrice,
                    ];
                })->toArray()
            ];
            $this->showPurchaseOrderModal = true;
            $this->dispatch('show-purchase-order-modal');
        }
    }

    /** ❌ Close Purchase Order Modal */
    public function closePurchaseOrderModal()
    {
        $this->showPurchaseOrderModal = false;
        $this->purchaseOrderModalData = null;
    }

    /** 📦 Load Products from Selected Purchase Order for Search */
    private function loadPurchaseOrderProductsForSearch()
    {
        if (empty($this->selectedPurchaseOrders)) {
            $this->purchaseOrderProductsForSearch = [];
            return;
        }

        $allProducts = collect();

        foreach ($this->selectedPurchaseOrders as $purchaseOrderId) {
            $purchaseOrder = PurchaseOrder::with(['items.product'])->find($purchaseOrderId);
            if ($purchaseOrder) {
                $products = $purchaseOrder->items->map(function ($item) use ($purchaseOrder) {
                    $alreadyReturned = $this->getAlreadyReturnedQuantity($item->product->id);
                    $remainingQty = $item->quantity - $alreadyReturned;

                    return [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'code' => $item->product->code,
                        'image' => $item->product->image,
                        'unit_price' => $item->unit_price,
                        'purchase_order_id' => $purchaseOrder->id,
                        'order_code' => $purchaseOrder->order_code,
                        'max_qty' => $remainingQty,
                    ];
                });
                $allProducts = $allProducts->merge($products);
            }
        }

        $this->purchaseOrderProductsForSearch = $allProducts->unique('id')->values()->toArray();
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
            fn($item) => $item['return_qty'] * $item['net_unit_price']
        );
    }

    /** ✅ Validate before showing confirmation */
    public function processReturn()
    {
        $this->calculateTotalReturnValue();

        if (empty($this->returnItems) || !$this->selectedPurchaseOrder) {
            $this->js("Swal.fire('Error!', 'Please select items for return.', 'error')");
            return;
        }

        $hasReturnItems = false;
        foreach ($this->returnItems as $item) {
            if ($item['return_qty'] < 0) {
                $this->js("Swal.fire('Error!', 'Return quantity cannot be negative for " . $item['name'] . "', 'error')");
                return;
            }

            if (isset($item['return_qty']) && $item['return_qty'] > 0) {
                if ($item['return_qty'] > $item['max_qty']) {
                    $this->js("Swal.fire('Error!', 'Invalid return quantity for " . $item['name'] . ". Maximum available: " . $item['max_qty'] . "', 'error')");
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
        $this->calculateTotalReturnValue();

        if (empty($this->returnItems) || !$this->selectedSupplier || !$this->selectedPurchaseOrder) return;

        $itemsToReturn = array_filter($this->returnItems, function ($item) {
            return isset($item['return_qty']) && $item['return_qty'] > 0;
        });

        if (empty($itemsToReturn)) {
            $this->dispatch('alert', ['message' => 'No valid return quantities entered.']);
            return;
        }

        DB::transaction(function () use ($itemsToReturn) {
            foreach ($itemsToReturn as $item) {
                ReturnSupplierModel::create([
                    'purchase_order_id' => $this->selectedPurchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'return_quantity' => $item['return_qty'],
                    'unit_price' => $item['net_unit_price'],
                    'total_amount' => $item['return_qty'] * $item['net_unit_price'],
                    'return_reason' => $item['return_reason'] ?? 'damaged',
                    'notes' => 'Supplier return processed via system',
                ]);

                $this->updateProductStock($item['product_id'], $item['return_qty'], $item['return_reason']);
            }
        });

        $this->clearReturnCart();
        $this->dispatch('alert', ['message' => 'Supplier return processed successfully!']);
        $this->dispatch('close-return-modal');
        $this->dispatch('reload-page');
    }

    /** 📈 Update Product Stock (Reduce stock for supplier returns) */
    private function updateProductStock($productId, $quantity, $reason)
    {
        $stock = ProductStock::where('product_id', $productId)->first();

        if ($stock) {
            // For supplier returns, we reduce the stock
            $stock->available_stock = max(0, $stock->available_stock - $quantity);
            $stock->total_stock = max(0, $stock->total_stock - $quantity);
            
            // Track damage stock if reason is damage
            if ($reason === 'damaged') {
                $stock->damage_stock += $quantity;
            }
            
            $stock->save();
        }
    }

    /** 🔄 Reset Return Data */
    private function resetReturnData()
    {
        $this->selectedPurchaseOrder = null;
        $this->selectedPurchaseOrders = [];
        $this->purchaseOrderProducts = [];
        $this->returnItems = [];
        $this->selectedProducts = [];
        $this->showReturnSection = false;
        $this->searchReturnProduct = '';
        $this->availableProducts = [];
        $this->purchaseOrderProductsForSearch = [];
        $this->totalReturnValue = 0;
        $this->overallDiscountPerItem = 0;
        $this->previousReturns = [];
        $this->returnReason = 'damaged';
    }

    public function render()
    {
        return view('livewire.admin.return-supplier')->layout($this->layout);
    }
}