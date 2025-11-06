<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductSupplier;
use App\Models\ProductDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
        if (strlen($this->search) >= 2) {
            $this->products = ProductDetail::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%')
                ->with(['stock', 'price'])
                ->limit(10)
                ->get();
        } else {
            $this->products = [];
        }
    }

    public function selectProduct($id)
    {
        $product = ProductDetail::with(['stock', 'price'])->find($id);

        if (!$product) {
            Log::error("Product not found with ID: " . $id);
            $this->js("Swal.fire('Error', 'Product not found!', 'error');");
            return;
        }

        // Check if product already exists in order items
        $existingIndex = null;
        foreach ($this->orderItems as $index => $item) {
            if ($item['product_id'] == $id) {
                $existingIndex = $index;
                break;
            }
        }

        // Get the price
        $price = \App\Models\ProductPrice::where('product_id', $id)->value('supplier_price');

        // If not found in product_prices, try other sources
        if (!$price && isset($product->price)) {
            $price = $product->price;
        }

        if (!$price && isset($product->cost_price)) {
            $price = $product->cost_price;
        }

        if (!$price && isset($product->purchase_price)) {
            $price = $product->purchase_price;
        }

        // If still no price, use a default
        if (!$price) {
            $price = $product->selling_price ?? 0;
        }

        if ($existingIndex !== null) {
            // Product already exists, increment quantity
            $this->orderItems[$existingIndex]['quantity'] += 1;
            $this->orderItems[$existingIndex]['total_price'] =
                $this->orderItems[$existingIndex]['quantity'] * $this->orderItems[$existingIndex]['supplier_price'];
        } else {
            // Add new product to order items directly
            $this->orderItems[] = [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'quantity' => 1,
                'supplier_price' => $price,
                'total_price' => $price
            ];
        }

        // Clear search
        $this->products = [];
        $this->search = '';
        $this->calculateGrandTotal();

        Log::info("Product added: " . $product->name . ", Price: " . $price);
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

    // Update order item quantity
    public function updateOrderItemQuantity($index, $quantity)
    {
        if (isset($this->orderItems[$index]) && $quantity >= 1) {
            $this->orderItems[$index]['quantity'] = $quantity;
            $this->orderItems[$index]['total_price'] =
                $quantity * $this->orderItems[$index]['supplier_price'];
            $this->calculateGrandTotal();
        }
    }

    // Update order item price
    public function updateOrderItemPrice($index, $price)
    {
        if (isset($this->orderItems[$index]) && $price >= 0) {
            $this->orderItems[$index]['supplier_price'] = $price;
            $this->orderItems[$index]['total_price'] =
                $this->orderItems[$index]['quantity'] * $price;
            $this->calculateGrandTotal();
        }
    }

    public function removeItem($index)
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
        $this->calculateGrandTotal();
    }

    public function saveOrder()
    {
        try {
            // Validation
            if (!$this->supplier_id) {
                $this->js("Swal.fire('Error', 'Please select a supplier!', 'error');");
                return;
            }

            if (empty($this->orderItems) || count($this->orderItems) == 0) {
                $this->js("Swal.fire('Error', 'Please add at least one product to the order!', 'error');");
                return;
            }

            // Validate each order item
            foreach ($this->orderItems as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['supplier_price'])) {
                    $this->js("Swal.fire('Error', 'Invalid order item data!', 'error');");
                    return;
                }
                
                if ($item['quantity'] < 1) {
                    $this->js("Swal.fire('Error', 'Product quantity must be at least 1!', 'error');");
                    return;
                }
            }

            // Generate unique order code for the year
            $year = date('Ymd');
            $lastOrder = PurchaseOrder::where('order_code', 'like', 'ORD-' . $year . '-%')
                ->orderByDesc('order_code')
                ->first();
            
            if ($lastOrder && preg_match('/ORD-' . $year . '-(\d+)/', $lastOrder->order_code, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            
            $orderCode = 'ORD-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Create order with transaction
            DB::beginTransaction();

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
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            // Refresh table
            $this->loadOrders();

            // Reset form
            $this->reset(['supplier_id', 'search', 'selectedProduct', 'selectedProductPrice', 'quantity', 'orderItems', 'totalPrice', 'grandTotal', 'products']);

            // Close modal and show success
            $this->js("
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPurchaseOrderModal'));
                if (modal) modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Purchase Order {$orderCode} created successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            ");

            Log::info("Purchase Order created successfully: " . $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating purchase order: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to create purchase order: " . addslashes($e->getMessage()) . "', 'error');");
        }
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
                'id' => $item->id,
                'product_id' => $item->product_id,
                'code' => $item->product->code ?? 'N/A',
                'name' => $item->product->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];
        }

        // Clear search to avoid conflicts
        $this->search = '';
        $this->products = [];

        // Open modal using JavaScript
        $this->js("new bootstrap.Modal(document.getElementById('editOrderModal')).show();");
    }

    // Add product to edit order items
    public function addProductToEdit($id)
    {
        $product = ProductDetail::with(['stock', 'price'])->find($id);

        if (!$product) {
            Log::error("Product not found with ID: " . $id);
            $this->js("Swal.fire('Error', 'Product not found!', 'error');");
            return;
        }

        // Check if product already exists in edit items
        $existingIndex = null;
        foreach ($this->editOrderItems as $index => $item) {
            if ($item['product_id'] == $id) {
                $existingIndex = $index;
                break;
            }
        }

        // Get the price
        $price = \App\Models\ProductPrice::where('product_id', $id)->value('supplier_price');

        if (!$price && isset($product->price)) {
            $price = $product->price;
        }

        if (!$price && isset($product->cost_price)) {
            $price = $product->cost_price;
        }

        if (!$price && isset($product->purchase_price)) {
            $price = $product->purchase_price;
        }

        if (!$price) {
            $price = $product->selling_price ?? 0;
        }

        if ($existingIndex !== null) {
            // Product already exists, increment quantity
            $this->editOrderItems[$existingIndex]['quantity'] += 1;
        } else {
            // Add new product to edit items
            $this->editOrderItems[] = [
                'id' => null, // New item, no database ID yet
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'quantity' => 1,
                'unit_price' => $price,
            ];
        }

        // Clear search
        $this->products = [];
        $this->search = '';

        Log::info("Product added to edit order: " . $product->name);
    }

    // Update total when quantity or price changes in edit modal
    public function updateEditItemTotal($index)
    {
        // This method is called automatically when wire:model.live triggers
        // No calculation needed here as it's done in the blade template
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
        try {
            if (!$this->editOrderId) {
                $this->js("Swal.fire('Error', 'No order selected for editing!', 'error');");
                return;
            }

            $order = PurchaseOrder::find($this->editOrderId);
            if (!$order) {
                $this->js("Swal.fire('Error', 'Order not found!', 'error');");
                return;
            }

            if (empty($this->editOrderItems)) {
                $this->js("Swal.fire('Error', 'Order must have at least one item!', 'error');");
                return;
            }

            DB::beginTransaction();

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
                        'status' => 'pending',
                    ]);
                    $keepIds[] = $new->id;
                }
            }

            // Delete any order items not present in keepIds
            PurchaseOrderItem::where('order_id', $order->id)
                ->whereNotIn('id', $keepIds ?: [0])
                ->delete();

            DB::commit();

            $this->loadOrders();
            $this->editOrderId = null;
            $this->editOrderItems = [];
            
            $this->js("
                const modal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal'));
                if (modal) modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Order updated successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            ");

            Log::info("Purchase Order updated: " . $order->order_code);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating purchase order: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to update order: " . addslashes($e->getMessage()) . "', 'error');");
        }
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
    }

    public function deleteOrderConfirmed($id)
    {
        try {
            $order = PurchaseOrder::find($id);
            
            if (!$order) {
                $this->js("Swal.fire('Error', 'Order not found!', 'error');");
                return;
            }

            DB::beginTransaction();

            $order->status = 'cancelled';
            $order->save();

            DB::commit();

            $this->loadOrders();
            
            $this->js("Swal.fire({
                icon: 'success',
                title: 'Cancelled!',
                text: 'Order has been cancelled.',
                timer: 2000,
                showConfirmButton: false
            });");

            Log::info("Purchase Order cancelled: " . $order->order_code);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error cancelling order: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to cancel order: " . addslashes($e->getMessage()) . "', 'error');");
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
        try {
            $order = PurchaseOrder::find($id);
            
            if (!$order) {
                $this->js("Swal.fire('Error', 'Order not found!', 'error');");
                return;
            }

            DB::beginTransaction();

            $order->status = 'complete';
            $order->save();

            DB::commit();

            $this->loadOrders();
            
            $this->js("Swal.fire({
                icon: 'success',
                title: 'Completed!',
                text: 'Order has been marked as completed.',
                timer: 2000,
                showConfirmButton: false
            });");

            Log::info("Purchase Order completed: " . $order->order_code);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error completing order: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to complete order: " . addslashes($e->getMessage()) . "', 'error');");
        }
    }

    // Force complete partial receipt order and mark pending items as not received
    public function confirmForceComplete($id)
    {
        $this->js("Swal.fire({
                title: 'Force Complete Order?',
                html: 'This will:<br>• Mark the order as <b>Complete</b><br>• Mark all pending items as <b>Not Received</b><br><br>Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, force complete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    \$wire.forceCompleteOrder({$id});
                }
            });
        ");
    }

    public function forceCompleteOrder($id)
    {
        try {
            $order = PurchaseOrder::find($id);
            
            if (!$order) {
                $this->js("Swal.fire('Error', 'Order not found!', 'error');");
                return;
            }

            DB::beginTransaction();

            // Get all pending items and mark them as not received
            $pendingItems = PurchaseOrderItem::where('order_id', $order->id)
                ->where('status', 'pending')
                ->get();

            $pendingCount = $pendingItems->count();

            foreach ($pendingItems as $item) {
                $item->status = 'notreceived';
                $item->save();
            }

            // Mark order as complete
            $order->status = 'complete';
            $order->save();

            DB::commit();

            $this->loadOrders();
            
            $this->js("Swal.fire({
                icon: 'success',
                title: 'Order Completed!',
                text: 'Order completed. {$pendingCount} pending item(s) marked as not received.',
                timer: 3000,
                showConfirmButton: false
            });");

            Log::info("Purchase Order force completed: " . $order->order_code . " with {$pendingCount} items marked as not received");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error force completing order: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to complete order: " . addslashes($e->getMessage()) . "', 'error');");
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
        try {
            if (!$this->selectedPO) {
                $this->js("Swal.fire('Error', 'No purchase order selected!', 'error');");
                return;
            }

            if (empty($this->grnItems)) {
                $this->js("Swal.fire('Error', 'No items to process!', 'error');");
                return;
            }

            DB::beginTransaction();

            $receivedItemsCount = 0;
            $notReceivedItemsCount = 0;
            $orderTotal = 0;

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
                            'total_stock' => 0,
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
                        $orderItem->quantity = $receivedQty;
                        $orderItem->unit_price = $item['unit_price'];
                        $orderItem->discount = $item['discount'];
                        $orderItem->discount_type = $item['discount_type'] ?? 'rs';
                        $orderItem->status = $status;
                        $orderItem->save();
                        
                        if ($status === 'received' && $receivedQty > 0) {
                            $this->updateProductStock($productId, $receivedQty);
                        }
                    }
                } else {
                    // Save new GRN item
                    $newOrderItem = PurchaseOrderItem::create([
                        'order_id' => $this->selectedPO->id,
                        'product_id' => $productId,
                        'quantity' => $receivedQty,
                        'unit_price' => $item['unit_price'] ?? 0,
                        'discount' => $item['discount'] ?? 0,
                        'discount_type' => $item['discount_type'] ?? 'rs',
                        'status' => 'received',
                    ]);
                    
                    if ($receivedQty > 0) {
                        $this->updateProductStock($productId, $receivedQty);
                    }
                }
            }

            // Update order received date
            $this->selectedPO->received_date = now();

            // Determine overall order status based on ALL order items
            $allOrderItems = PurchaseOrderItem::where('order_id', $this->selectedPO->id)->get();
            $totalItemsCount = $allOrderItems->count();
            $receivedItemsTotal = $allOrderItems->where('status', 'received')->count();
            $notReceivedItemsTotal = $allOrderItems->where('status', 'notreceived')->count();
            $pendingItemsTotal = $allOrderItems->whereNotIn('status', ['received', 'notreceived'])->count();

            // If all items are received, mark order as complete
            if ($receivedItemsTotal === $totalItemsCount && $totalItemsCount > 0) {
                $this->selectedPO->status = 'complete';
            }
            // If some items are received but others are pending/not received
            elseif ($receivedItemsTotal > 0 && ($pendingItemsTotal > 0 || $notReceivedItemsTotal > 0)) {
                $this->selectedPO->status = 'received'; // Partial receipt
            }
            // If no items received yet
            else {
                $this->selectedPO->status = 'pending';
            }

            // Set total_amount and due_amount
            $this->selectedPO->total_amount = $orderTotal;
            $this->selectedPO->due_amount = $orderTotal;
            $this->selectedPO->save();

            DB::commit();

            // Refresh the orders table
            $this->loadOrders();

            // Reset GRN data
            $orderCode = $this->selectedPO->order_code;
            $this->selectedPO = null;
            $this->grnItems = [];
            $this->searchResults = [];

            // Close modal and show success message
            $this->js("
                const modal = bootstrap.Modal.getInstance(document.getElementById('grnModal'));
                if (modal) modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'GRN processed successfully! Stock updated for order {$orderCode}.',
                    timer: 2000,
                    showConfirmButton: false
                });
            ");

            Log::info("GRN processed successfully for order: " . $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing GRN: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to process GRN: " . addslashes($e->getMessage()) . "', 'error');");
        }
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

    // Re-Process GRN - Only load pending items
    public function reProcessGRN($orderId)
    {
        $this->selectedPO = PurchaseOrder::with(['supplier', 'items.product.detail'])->find($orderId);

        if (!$this->selectedPO) {
            $this->js("Swal.fire('Error', 'Order not found!', 'error');");
            return;
        }

        // Initialize GRN items from ONLY pending purchase order items
        $this->grnItems = [];
        foreach ($this->selectedPO->items as $item) {
            // Only include items that are pending or not received
            if (in_array(strtolower($item->status ?? 'pending'), ['pending', 'notreceived', ''])) {
                $this->grnItems[] = [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'code' => $item->product->code ?? 'N/A',
                    'name' => $item->product->name ?? 'N/A',
                    'ordered_qty' => $item->quantity,
                    'received_qty' => $item->quantity, // Default to ordered quantity
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount ?? 0,
                    'discount_type' => 'rs',
                    'status' => 'pending'
                ];
            }
        }

        if (empty($this->grnItems)) {
            $this->js("Swal.fire('Info', 'No pending items to process!', 'info');");
            return;
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
            
            DB::beginTransaction();
            
            // Delete all order items first
            PurchaseOrderItem::where('order_id', $order->id)->delete();
            
            // Delete the order
            $orderCode = $order->order_code;
            $order->delete();
            
            DB::commit();

            $this->loadOrders();

            $this->js("Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Purchase order {$orderCode} has been permanently deleted.',
                timer: 2000,
                showConfirmButton: false
            });");

            Log::info("Purchase order permanently deleted: " . $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error permanently deleting order: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to delete order: " . addslashes($e->getMessage()) . "', 'error');");
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
