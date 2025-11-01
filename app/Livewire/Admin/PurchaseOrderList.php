<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductSupplier;
use App\Models\ProductDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title("Purchase Order")]
#[Layout('components.layouts.admin')]

class PurchaseOrderList extends Component
{
    public $suppliers = [];
    public $supplier_id = '';
    public $Product_id = '';
    public $search = '';
    public $products = [];
    public $selectedProduct = null;
    public $quantity = 1;
    public $orderItems = [];
    public $orders;
    public $selectedOrder;

    // Edit flow
    public $editOrderId = null;
    public $editOrderItems = [];

    // Add these properties
    public $selectedProductPrice = 0;
    public $totalPrice = 0;
    //GRN properties
    public $purchaseOrders = [];
    public $selectedPO = null;
    public $grnItems = [];
    public $searchProduct = '';
    public $searchResults = [];
    public $newItem = ['product_id' => null, 'name' => '', 'qty' => 1, 'unit_price' => 0, 'discount' => 0, 'status' => 'received'];

    protected $listeners = ['deleteGRNItem'];

    public $grandTotal = 0;

    // Add this property to track new products
    public $newProducts = [];
    public function mount()
    {
        $this->suppliers = ProductSupplier::all();
        $this->loadOrders();
        $this->searchResults = []; // Initialize searchResults array
    }

    public function calculateGrandTotal()
    {
        $this->grandTotal = collect($this->orderItems)->sum('total_price');
    }

    public function updatedSearch()
    {
        if ($this->search != '') {
            $this->products = ProductDetail::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%')
                ->with(['stock'])
                ->limit(5)
                ->get();
        } else {
            $this->products = [];
        }
    }

    public function selectProduct($id)
    {
        $this->selectedProduct = ProductDetail::with(['stock'])->find($id);

        if (!$this->selectedProduct) {
            Log::error("Product not found with ID: " . $id);
            return;
        }

        // Get the price
        $price = \App\Models\ProductPrice::where('product_id', $id)->value('supplier_price');

        // If not found in product_prices, try other sources
        if (!$price && isset($this->selectedProduct->price)) {
            $price = $this->selectedProduct->price;
        }

        if (!$price && isset($this->selectedProduct->cost_price)) {
            $price = $this->selectedProduct->cost_price;
        }

        if (!$price && isset($this->selectedProduct->purchase_price)) {
            $price = $this->selectedProduct->purchase_price;
        }

        // If still no price, use a default
        if (!$price) {
            $price = $this->selectedProduct->selling_price ?? 0;
        }

        // Store the price in a separate property instead of on the model
        $this->selectedProductPrice = $price;

        $this->products = [];
        $this->search = '';
        $this->quantity = 1;
        $this->calculateTotalPrice();

        Log::info("Product selected: " . $this->selectedProduct->name . ", Price: " . $this->selectedProductPrice);
    }

    public function calculateTotalPrice()
    {
        $this->totalPrice = $this->quantity * $this->selectedProductPrice;
        Log::info("Total calculated: " . $this->quantity . " * " . $this->selectedProductPrice . " = " . $this->totalPrice);
    }

    public function updatedQuantity($value)
    {
        $this->calculateTotalPrice();
    }

    public function addItem()
    {
        if (!$this->selectedProduct || $this->quantity < 1) return;

        $this->orderItems[] = [
            'product_id' => $this->selectedProduct->id,
            'code' => $this->selectedProduct->code,
            'name' => $this->selectedProduct->name,
            'quantity' => $this->quantity,
            'supplier_price' => $this->selectedProductPrice,
            'total_price' => $this->totalPrice
        ];

        $this->selectedProduct = null;
        $this->selectedProductPrice = 0;
        $this->search = '';
        $this->quantity = 1;
        $this->totalPrice = 0;
        $this->calculateGrandTotal();
    }

    public function removeItem($index)
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
        $this->calculateGrandTotal();
    }

    public function saveOrder()
    {
        if (!$this->supplier_id || count($this->orderItems) == 0) return;

        // Generate unique order code for the year
        $year = date('Y');
        $lastOrder = PurchaseOrder::where('order_code', 'like', 'ORD-' . $year . '-%')
            ->orderByDesc('order_code')
            ->first();
        if ($lastOrder && preg_match('/ORD-' . $year . '-(\d+)/', $lastOrder->order_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $orderCode = 'ORD-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $order = PurchaseOrder::create([
            'order_code' => $orderCode,
            'supplier_id' => $this->supplier_id,
            'order_date' => now(),
            'status' => 'pending',
        ]);

        foreach ($this->orderItems as $item) {
            PurchaseOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['supplier_price'],
                'discount' => 0,
            ]);
        }

        // Refresh table
        $this->loadOrders();

        $this->reset(['supplier_id', 'search', 'selectedProduct', 'selectedProductPrice', 'quantity', 'orderItems', 'totalPrice']);
        $this->js("bootstrap.Modal.getInstance(document.getElementById('addPurchaseOrderModal')).hide();");
        $this->js("Swal.fire('Success', 'Purchase Order created successfully!', 'success');");
        $this->js("location.reload();");
    }

    public function loadOrders()
    {
        $this->orders = PurchaseOrder::whereIn('status', ['pending', 'complete', 'received', 'cancelled'])
            ->with(['supplier', 'items.product'])
            ->latest()
            ->orderBy('id', 'desc')
            ->get();
    }

    public function viewOrder($id)
    {
        $this->selectedOrder = PurchaseOrder::with(['supplier', 'items.product.detail'])->find($id);

        if (!$this->selectedOrder) {
            $this->js("Swal.fire('Error', 'Order not found!', 'error');");
            return;
        }

        // Open modal using JavaScript
        $this->js("new bootstrap.Modal(document.getElementById('viewOrderModal')).show();");
    }

    public function editOrder($id)
    {
        $order = PurchaseOrder::with(['supplier', 'items.product'])->find($id);
        if (!$order) {
            $this->js("Swal.fire('Error', 'Order not found!', 'error');");
            return;
        }

        $this->editOrderId = $order->id;
        $this->editOrderItems = [];
        foreach ($order->items as $item) {
            $this->editOrderItems[] = [
                'item_id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,

            ];
        }

        // Open modal using JavaScript
        $this->js("new bootstrap.Modal(document.getElementById('editOrderModal')).show();");
    }

    public function removeEditItem($index)
    {
        if (isset($this->editOrderItems[$index])) {
            unset($this->editOrderItems[$index]);
            $this->editOrderItems = array_values($this->editOrderItems);
        }
    }

    public function updateOrder()
    {
        if (!$this->editOrderId) return;

        $order = PurchaseOrder::find($this->editOrderId);
        if (!$order) {
            $this->js("Swal.fire('Error', 'Order not found!', 'error');");
            return;
        }

        // Track current item ids to keep
        $keepIds = [];

        foreach ($this->editOrderItems as $item) {
            // Validation: quantity must be >=1
            $qty = isset($item['quantity']) ? intval($item['quantity']) : 0;
            if ($qty < 1) continue; // skip invalid rows

            if (!empty($item['item_id'])) {
                // update existing
                $orderItem = PurchaseOrderItem::find($item['item_id']);
                if ($orderItem) {
                    $orderItem->quantity = $qty;
                    $orderItem->unit_price = $item['unit_price'] ?? 0;
                    $orderItem->discount = $item['discount'] ?? 0;
                    $orderItem->total_amount = $item['total_amount'] ?? 0;
                    $orderItem->due_amount = $item['due_amount'] ?? 0;
                    $orderItem->save();
                    $keepIds[] = $orderItem->id;
                }
            } else {
                // create new
                $new = PurchaseOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,

                ]);
                $keepIds[] = $new->id;
            }
        }

        // Delete any order items not present in keepIds
        PurchaseOrderItem::where('order_id', $order->id)
            ->whereNotIn('id', $keepIds ?: [0])
            ->delete();

        $this->loadOrders();
        $this->editOrderId = null;
        $this->editOrderItems = [];
        $this->js("bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();");
        $this->js("Swal.fire('Success', 'Order updated successfully!', 'success');");
    }

    public function confirmDelete($id)
    {
        $this->js("Swal.fire({
                title: 'Delete order?',
                text: 'This will remove the purchase order and its items. Continue?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    \$wire.deleteOrderConfirmed({$id});
                }
            });
        ");
        //relode js
        $this->js("location.reload();");
    }

    public function deleteOrderConfirmed($id)
    {
        $order = PurchaseOrder::find($id);
        if ($order) {
            $order->status = 'cancelled';
            $order->save();
            $this->loadOrders();
            $this->js("Swal.fire('Cancelled', 'Order status changed to cancelled.', 'success');");
        }
    }

    public function confirmComplete($id)
    {
        $this->js("Swal.fire({
                title: 'Mark order as complete?',
                text: 'Are you sure you want to complete this order?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    \$wire.completeOrderConfirmed({$id});
                }
            });
        ");
    }

    public function completeOrderConfirmed($id)
    {
        $order = PurchaseOrder::find($id);
        if ($order) {
            $order->status = 'complete';
            $order->save();
            $this->loadOrders();
            $this->js("Swal.fire('Success', 'Order marked as completed!', 'success');");
        }
    }

    protected function updateProductStock($productId, $quantity)
    {
        $product = ProductDetail::find($productId);
        if ($product) {
            // If product has stock relationship, update it
            if ($product->stock) {
                $product->stock->available_stock += $quantity;
                $product->stock->total_stock += $quantity;
                $product->stock->save();

                //add discounted price to cost price


            } else {
                // If no stock record exists, create one
                \App\Models\ProductStock::create([
                    'product_id' => $productId,
                    'available_stock' => $quantity,
                    'reserved_stock' => 0,
                ]);
            }
        }
    }

    // Update saveGRN method to handle new products
    public function saveGRN()
    {
        if (!$this->selectedPO || empty($this->grnItems)) return;

        $receivedItemsCount = 0;
        $notReceivedItemsCount = 0;
        $orderTotal = 0; // <-- Track the total amount

        foreach ($this->grnItems as $item) {
            $productId = $item['product_id'];
            $receivedQty = $item['received_qty'] ?? 0;
            $status = strtolower($item['status'] ?? '');
            $isNewProduct = $item['is_new'] ?? false;

            // Count statuses
            if ($status === 'received') {
                $receivedItemsCount++;
            } elseif ($status === 'notreceived') {
                $notReceivedItemsCount++;
            }

            // Skip items without a valid product_id and not new products
            if (!$productId && !$isNewProduct) {
                continue;
            }

            // Handle new product creation
            if ($isNewProduct && !empty($item['name']) && !empty($item['code'])) {
                $existingProduct = ProductDetail::where('code', $item['code'])->first();
                if ($existingProduct) {
                    $productId = $existingProduct->id;
                } else {
                    $newProduct = ProductDetail::create([
                        'code' => $item['code'],
                        'name' => $item['name'],
                        'description' => 'Added via GRN',
                        'category_id' => 1,
                        'brand_id' => 1,
                        'status' => 'active',
                    ]);
                    \App\Models\ProductPrice::create([
                        'product_id' => $newProduct->id,
                        'supplier_price' => $item['unit_price'] ?? 0,
                        'selling_price' => ($item['unit_price'] ?? 0) * 1.2,
                        'wholesale_price' => ($item['unit_price'] ?? 0) * 1.1,
                    ]);
                    \App\Models\ProductStock::create([
                        'product_id' => $newProduct->id,
                        'available_stock' => 0,
                        'reserved_stock' => 0,
                    ]);
                    $productId = $newProduct->id;
                }
            }

            // Calculate total for this item (with discount)
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $discount = floatval($item['discount'] ?? 0);
            $discountType = $item['discount_type'] ?? 'rs';
            $subtotal = $receivedQty * $unitPrice;
            if ($discountType === 'percent') {
                $discountAmount = ($subtotal * $discount) / 100;
                $itemTotal = $subtotal - $discountAmount;
            } else {
                $itemTotal = $subtotal - $discount;
            }
            $orderTotal += max(0, $itemTotal);

            if (isset($item['id'])) {
                // Update existing order item
                $orderItem = PurchaseOrderItem::find($item['id']);
                if ($orderItem) {
                    $totalQty = PurchaseOrderItem::where('order_id', $orderItem->order_id)
                        ->where('product_id', $orderItem->product_id)
                        ->sum('quantity');
                    $previousQty = $totalQty ?? 0;
                    $orderItem->quantity = $receivedQty;
                    $orderItem->unit_price = $item['unit_price'];
                    $orderItem->discount = $item['discount'];
                    $orderItem->status = $status;
                    $orderItem->save();
                    if ($status === 'received' && $receivedQty > 0) {
                        $previousQty += $receivedQty;
                        $this->updateProductStock($productId, $receivedQty);
                    }
                }
            } else {
                // Save new GRN item
                $newOrderItem = PurchaseOrderItem::create([
                    'order_id' => $this->selectedPO->id,
                    'code' => $item['code'] ?? '',
                    'product_id' => $productId,
                    'quantity' => $receivedQty,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'status' => 'received',
                ]);
                $newOrderItem->status = 'received';
                $newOrderItem->save();
                if ($receivedQty > 0) {
                    $this->updateProductStock($productId, $receivedQty);
                }
            }
        }

        // Update order received date
        $this->selectedPO->received_date = now();

        // Determine overall order status based on GRN items
        $totalItems = count($this->grnItems);
        if ($receivedItemsCount === $totalItems) {
            $this->selectedPO->status = 'received';
        } elseif ($receivedItemsCount > 0) {
            $this->selectedPO->status = 'complete';
        } else {
            $this->selectedPO->status = 'pending';
        }

        // Set total_amount and due_amount
        $this->selectedPO->total_amount = $orderTotal;
        $this->selectedPO->due_amount = $orderTotal;
        $this->selectedPO->save();

        // Refresh the orders table
        $this->loadOrders();

        // Close modal and show success message
        $this->js("bootstrap.Modal.getInstance(document.getElementById('grnModal')).hide();");
        $this->js("Swal.fire('Success', 'GRN processed successfully! Stock updated.', 'success');");

        // Reset GRN data
        $this->selectedPO = null;
        $this->grnItems = [];
        $this->searchResults = [];
        $this->js("location.reload();");
    }

    public function deleteGRNItem($index)
    {
        if (isset($this->grnItems[$index])) {
            // Mark as not received instead of deleting
            $this->grnItems[$index]['status'] = 'notreceived';
            $this->grnItems[$index]['received_qty'] = 0;

            // Remove the item completely from GRN items array
            unset($this->grnItems[$index]);
        }
    }

    public function correctGRNItem($index)
    {
        $item = $this->grnItems[$index];
        $productId = $item['product_id'];
        $receivedQty = $item['received_qty'] ?? 0;

        // Mark the item as received in the UI
        $this->grnItems[$index]['status'] = 'received';

        // Update stock immediately if we have a valid product and quantity
        if ($productId && $receivedQty > 0) {
            $this->updateProductStock($productId, $receivedQty);
        }
        if (isset($this->grnItems[$index])) {
            $this->grnItems[$index]['status'] = 'received';
            // Reset to ordered quantity if needed
            if ($this->grnItems[$index]['received_qty'] == 0) {
                $this->grnItems[$index]['received_qty'] = $this->grnItems[$index]['ordered_qty'];
            }
        }
    }

    public function searchGRNProducts($searchTerm, $index)
    {
        if (!empty($searchTerm)) {
            $this->searchResults[$index] = ProductDetail::where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('code', 'like', '%' . $searchTerm . '%')
                ->with(['stock'])
                ->limit(5)
                ->get();
        } else {
            $this->searchResults[$index] = [];
        }
    }

    // Add these methods to your component

    public function calculateGRNTotal($index)
    {
        if (!isset($this->grnItems[$index])) {
            return 0;
        }

        $item = $this->grnItems[$index];

        // Convert to numbers and ensure they are valid
        $receivedQty = floatval($item['received_qty'] ?? 0);
        $unitPrice = floatval($item['unit_price'] ?? 0);
        $discount = floatval($item['discount'] ?? 0);

        // Calculate subtotal
        $subtotal = $receivedQty * $unitPrice;

        // Apply discount (discount is per item, not percentage)
        $total = $subtotal - $discount;
        $discountType = $item['discount_type'] ?? 'rs';
        // Apply discount based on type
        if ($discountType === 'percent') {
            // Calculate percentage discount
            $discountAmount = ($subtotal * $discount) / 100;
            $total = $subtotal - $discountAmount;
        } else {
            // Fixed rupees discount
            $total = $subtotal - $discount;
        }


        // Ensure total is not negative
        return max(0, $total);
    }

    public function updatedGrnItems($value, $index)
    {
        // Handle search when product name is updated
        $parts = explode('.', $index);
        if (count($parts) === 3) {
            $itemIndex = $parts[1];
            $field = $parts[2];

            if ($field === 'name') {
                $this->searchGRNProducts($value, $itemIndex);
            }

            // Auto-calculate when numeric fields change
            if (in_array($field, ['received_qty', 'unit_price', 'discount'])) {
                $this->calculateGRNTotal($itemIndex);
            }
        }
    }

    // Update the selectGRNProduct method to include discount
    // Update selectGRNProduct method to populate code
    public function selectGRNProduct($index, $productId)
    {
        $product = ProductDetail::find($productId);
        if ($product) {
            $this->grnItems[$index]['product_id'] = $product->id;
            $this->grnItems[$index]['code'] = $product->code; // Add this line
            $this->grnItems[$index]['name'] = $product->name;

            // Get product price
            $price = \App\Models\ProductPrice::where('product_id', $productId)->value('supplier_price');
            if (!$price && isset($product->price)) {
                $price = $product->price;
            }
            if (!$price && isset($product->cost_price)) {
                $price = $product->cost_price;
            }

            $this->grnItems[$index]['unit_price'] = $price ?? 0;
            $this->grnItems[$index]['discount'] = 0;
            $this->grnItems[$index]['is_new'] = false; // Not a new product
            $this->searchResults[$index] = [];

            // Trigger calculation
            $this->calculateGRNTotal($index);
        }
    }

    // Update addNewRow to include discount
    public function addNewRow()
    {
        $this->grnItems[] = [
            'product_id' => null,
            'code' => '',
            'name' => '',
            'ordered_qty' => 0,
            'received_qty' => 0,
            'unit_price' => 0,
            'discount' => 0,
            'discount_type' => 'rs',
            'status' => 'received ',
            'is_new' => true // Flag to identify new products
        ];
    }


    // Update convertToGRN to include discount
    public function convertToGRN($orderId)
    {
        $this->selectedPO = PurchaseOrder::with(['supplier', 'items.product.detail'])->find($orderId);

        if (!$this->selectedPO) {
            $this->js("Swal.fire('Error', 'Order not found!', 'error');");
            return;
        }

        // Initialize GRN items from purchase order items
        $this->grnItems = [];
        foreach ($this->selectedPO->items as $item) {
            $this->grnItems[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'code' => $item->product->code ?? 'N/A',
                'name' => $item->product->name ?? 'N/A',
                'ordered_qty' => $item->quantity,
                'received_qty' => $item->quantity, // Default to ordered quantity
                'unit_price' => $item->unit_price,
                'discount' => $item->discount ?? 0, // Include discount
                'discount_type' => 'rs', // Default to rupees
                'status' => 'received'
            ];
        }


        // Open GRN modal using JavaScript
        $this->js("new bootstrap.Modal(document.getElementById('grnModal')).show();");
    }

    // Calculate cost (supplier price - discount amount per unit)
    public function calculateCost($index)
    {
        if (!isset($this->grnItems[$index])) {
            return 0;
        }

        $item = $this->grnItems[$index];
        $unitPrice = floatval($item['unit_price'] ?? 0);
        $discount = floatval($item['discount'] ?? 0);
        $discountType = $item['discount_type'] ?? 'rs';
        $receivedQty = floatval($item['received_qty'] ?? 0);

        if ($receivedQty <= 0) {
            return $unitPrice;
        }

        if ($discountType === 'percent') {
            // Calculate percentage discount per unit
            $discountAmountPerUnit = ($unitPrice * $discount) / 100;
            $costPerUnit = $unitPrice - $discountAmountPerUnit;
        } else {
            // Fixed rupees discount distributed per unit
            $discountAmountPerUnit = $discount / $receivedQty;
            $costPerUnit = $unitPrice - $discountAmountPerUnit;
        }

        // Ensure cost is not negative
        return max(0, $costPerUnit);
    }
    // Set discount type method
    public function setDiscountType($index, $type)
    {
        if (isset($this->grnItems[$index])) {
            $this->grnItems[$index]['discount_type'] = $type;
            // Recalculate total when discount type changes
            $this->calculateGRNTotal($index);
        }
    }
    // Calculate discount amount for percentage display
    public function calculateDiscountAmount($index)
    {
        if (!isset($this->grnItems[$index])) {
            return 0;
        }

        $item = $this->grnItems[$index];
        $receivedQty = floatval($item['received_qty'] ?? 0);
        $unitPrice = floatval($item['unit_price'] ?? 0);
        $discount = floatval($item['discount'] ?? 0);
        $discountType = $item['discount_type'] ?? 'rs';

        if ($discountType === 'percent') {
            $subtotal = $receivedQty * $unitPrice;
            return ($subtotal * $discount) / 100;
        }

        return $discount;
    }


    public function loadPurchaseOrders()
    {
        $this->purchaseOrders = PurchaseOrder::where('status', 'pending')
            ->with(['supplier', 'items.product'])
            ->latest()
            ->get();
    }

    public $orderId;
    public function confirmPermanentDelete($orderId)
    {
        $this->js("
        Swal.fire({
            title: 'Permanently Delete Order?',
            text: 'This action cannot be undone! The purchase order will be permanently removed from the system.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                \$wire.permanentDeleteOrder({$orderId});
            }
        });
    ");
    }

    public function permanentDeleteOrder($orderId)
    {
        try {
            $order = PurchaseOrder::findOrFail($orderId);
            $order->delete(); // Permanent deletion

            $this->loadOrders(); // Refresh the orders list
            session()->flash('message', 'Purchase order permanently deleted.');

            // Show success message
            $this->js("Swal.fire('Success', 'Purchase order permanently deleted.', 'success');");
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting order: ' . $e->getMessage());
            $this->js("Swal.fire('Error', 'Error deleting order.', 'error');");
        }
    }

    // Add these computed properties for totals

    // Calculate grand total for view order modal
    public function getViewOrderTotalProperty()
    {
        if (!$this->selectedOrder) {
            return 0;
        }

        return $this->selectedOrder->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    // Calculate grand total for GRN modal
    public function getGrnGrandTotalProperty()
    {
        $total = 0;
        foreach ($this->grnItems as $index => $item) {
            $total += $this->calculateGRNTotal($index);
        }
        return $total;
    }


    public function downloadPDF($orderId)
    {
        $order = PurchaseOrder::with('supplier', 'items.product')->find($orderId);

        if (!$order) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Order not found!',
                'icon' => 'error'
            ]);
            return;
        }

        // Inline HTML for the PDF
        $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Purchase Order - ' . $order->order_code . '</title>
        <style>
            body { font-family: sans-serif; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            table, th, td { border: 1px solid #333; }
            th, td { padding: 5px; text-align: left; }
            th { background-color: #f0f0f0; }
        </style>
    </head>
    <body>
        <h2>Purchase Order: ' . $order->order_code . '</h2>
        <p><strong>Supplier:</strong> ' . ($order->supplier->name ?? 'N/A') . '</p>
        <p><strong>Order Date:</strong> ' . $order->order_date . '</p>
        <p><strong>Received Date:</strong> ' . ($order->received_date ?? '-') . '</p>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($order->items as $item) {
            $html .= '<tr>
                    <td>' . $item->product->code . '</td>
                    <td>' . $item->product->name . '</td>
                    <td>' . $item->quantity . '</td>
                    <td>' . number_format($item->unit_price, 2) . '</td>
                    <td>' . number_format($item->quantity * $item->unit_price, 2) . '</td>
                  </tr>';
        }

        $totalAmount = $order->items->sum(fn($item) => $item->quantity * $item->unit_price);

        $html .= '</tbody>
        </table>
        <h3 style="text-align:right;">Grand Total: ' . number_format($totalAmount, 2) . '</h3>
    </body>
    </html>';

        $pdf = Pdf::loadHTML($html);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "Purchase_Order_{$order->order_code}.pdf"
        );
    }


    public function render()
    {
        $pendingCount = PurchaseOrder::where('status', 'pending')->count();
        $completedCount = PurchaseOrder::where('status', 'complete')->count();

        // Optional: Count orders that have been fully received (all items received)
        $fullyReceivedCount = PurchaseOrder::whereHas('items', function ($query) {
            $query->where('status', 'received');
        })->whereDoesntHave('items', function ($query) {
            $query->where('status', '!=', 'received');
        })->count();

        return view('livewire.admin.purchase-order-list', compact('pendingCount', 'completedCount', 'fullyReceivedCount'));
    }
}
