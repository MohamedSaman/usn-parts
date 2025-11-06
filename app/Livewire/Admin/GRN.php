<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ProductDetail;
use App\Models\ProductStock;
use Illuminate\Support\Str;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title("Goods Receive Note")]
#[Layout('components.layouts.admin')]
class GRN extends Component
{
    public $purchaseOrders = [];
    public $selectedPO = null;
    public $grnItems = [];
    public $searchProduct = '';
    public $searchResults = [];
    public $newItem = ['product_id' => null, 'name' => '', 'qty' => 1, 'unit_price' => 0, 'discount' => 0, 'status' => 'received'];

    protected $listeners = ['deleteGRNItem'];

    public function mount()
    {
        $this->loadPurchaseOrders();
        $this->searchResults = ['unplanned' => []];
    }

    public function loadPurchaseOrders()
    {
        // Show both complete and received orders in the table
        $this->purchaseOrders = PurchaseOrder::whereIn('status', ['complete', 'received'])
            ->with(['supplier', 'items.product'])
            ->latest()
            ->get();
    }

    // Add this method to get counts for both statuses
    public function getOrderCounts()
    {
        return [
            'complete' => PurchaseOrder::where('status', 'complete')->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
            'total' => PurchaseOrder::whereIn('status', ['complete', 'received'])->count()
        ];
    }
    public function viewGRN($orderId)
    {
        $this->selectedPO = PurchaseOrder::with(['supplier', 'items' => function ($query) {
            $query->where('status', 'received');
        }, 'items.product'])->find($orderId);

        if (!$this->selectedPO) {
            $this->dispatch('alert', ['message' => 'Order not found!', 'type' => 'error']);
            return;
        }

        $this->grnItems = [];
        $this->searchResults = ['unplanned' => []];

        foreach ($this->selectedPO->items as $item) {
            $this->grnItems[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'ordered_qty' => $item->quantity,
                'received_qty' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'discount_type' => $item->discount_type ?? 'rs',
                'status' => $item->status ?? 'received',
            ];
        }

        // Dispatch event to open modal after data is loaded
        $this->dispatch('open-view-grn-modal');
    }

    public function openGRN($orderId)
    {
        $this->selectedPO = PurchaseOrder::with(['supplier', 'items.product'])->find($orderId);

        if (!$this->selectedPO) {
            $this->dispatch('alert', ['message' => 'Order not found!', 'type' => 'error']);
            return;
        }

        $this->grnItems = [];
        $this->searchResults = ['unplanned' => []];

        foreach ($this->selectedPO->items as $item) {
            $this->grnItems[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'ordered_qty' => $item->quantity,
                'received_qty' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'status' => $item->status,
            ];
        }

        // Dispatch event to open modal after data is loaded
        $this->dispatch('open-grn-modal');
    }

    public function updated($propertyName)
    {
        if (preg_match('/grnItems\.(\d+)\.name/', $propertyName, $matches)) {
            $index = $matches[1];
            $searchTerm = $this->grnItems[$index]['name'];
            if (strlen($searchTerm) > 1) {
                $this->searchResults[$index] = ProductDetail::where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->with(['price', 'stock'])
                    ->limit(5)
                    ->get();
            } else {
                $this->searchResults[$index] = [];
            }
        } elseif ($propertyName === 'searchProduct') {
            if (strlen($this->searchProduct) > 1) {
                $this->searchResults['unplanned'] = ProductDetail::where('name', 'like', "%{$this->searchProduct}%")
                    ->orWhere('code', 'like', "%{$this->searchProduct}%")
                    ->with(['price', 'stock'])
                    ->limit(5)
                    ->get();
            } else {
                $this->searchResults['unplanned'] = [];
            }
        }
    }

    public function selectProduct($index, $productId)
    {
        if (!is_numeric($productId)) return;

        $product = ProductDetail::with('price')->find($productId);
        if ($product) {
            $unitPrice = $product->price ? $product->price->supplier_price : 0;
            if ($index === -1) {
                $this->newItem['product_id'] = $product->id;
                $this->newItem['name'] = $product->name;
                $this->newItem['unit_price'] = $unitPrice;
                $this->newItem['status'] = 'received';
                $this->searchProduct = $product->name;
                $this->searchResults['unplanned'] = [];
            } else {
                $this->grnItems[$index]['product_id'] = $product->id;
                $this->grnItems[$index]['name'] = $product->name;
                $this->grnItems[$index]['unit_price'] = $unitPrice;
                $this->grnItems[$index]['status'] = 'received';
                $this->searchResults[$index] = [];
            }
        }
    }

    public function addUnplannedItem()
    {
        if (!$this->newItem['name'] || $this->newItem['qty'] < 1) return;

        $this->grnItems[] = [
            'product_id' => $this->newItem['product_id'],
            'name' => $this->newItem['name'],
            'ordered_qty' => 0,
            'received_qty' => $this->newItem['qty'],
            'unit_price' => $this->newItem['unit_price'],
            'discount' => $this->newItem['discount'],
            'status' => 'received',
        ];

        $this->newItem = ['product_id' => null, 'name' => '', 'qty' => 1, 'unit_price' => 0, 'discount' => 0, 'status' => 'received'];
        $this->searchProduct = '';
        $this->searchResults['unplanned'] = [];
    }

    public function addNewRow()
    {
        $this->grnItems[] = [
            'product_id' => null,
            'name' => '',
            'ordered_qty' => 0,
            'received_qty' => 1,
            'unit_price' => 0,
            'discount' => 0,
            'status' => 'received',
        ];

        // Initialize search results for the new row
        $newIndex = count($this->grnItems) - 1;
        $this->searchResults[$newIndex] = [];
    }

    public function deleteGRNItem($index)
    {
        if (isset($this->grnItems[$index]['id'])) {
            $orderItem = PurchaseOrderItem::find($this->grnItems[$index]['id']);
            if ($orderItem) {
                $orderItem->status = 'notreceived';
                $orderItem->save();
            }
        }
        $this->grnItems[$index]['status'] = 'notreceived';
        $this->searchResults[$index] = [];
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
    }

    public function saveGRN()
    {
        if (!$this->selectedPO || empty($this->grnItems)) return;

        $receivedItemsCount = 0;
        $totalItemsCount = 0;

        foreach ($this->grnItems as $item) {
            // Skip items that are marked as not received
            if (strtolower($item['status'] ?? '') === 'notreceived') {
                $totalItemsCount++;
                continue;
            }

            $productId = $item['product_id'];
            $receivedQty = $item['received_qty'] ?? 0;

            // Skip items without a valid product_id (empty rows)
            if (!$productId) {
                continue;
            }

            $totalItemsCount++;

            if (isset($item['id'])) {
                // Update existing order item
                $orderItem = PurchaseOrderItem::find($item['id']);
                if ($orderItem) {
                    // Calculate delta: new received qty minus previously recorded qty
                    $previousQty = $orderItem->quantity ?? 0;
                    $orderItem->quantity = $receivedQty;
                    $orderItem->unit_price = $item['unit_price'];
                    $orderItem->discount = $item['discount'];
                    $orderItem->discount_type = $item['discount_type'] ?? 'rs';
                    $orderItem->status = $item['status'];
                    $orderItem->save();

                    // Update stock only with the delta (received now)
                    if (strtolower($item['status'] ?? '') === 'received' && $receivedQty > 0) {
                        $delta = $receivedQty - $previousQty;
                        if ($delta > 0) {
                            $this->updateProductStock($productId, $delta);
                        }
                        $receivedItemsCount++;
                    }
                }
            } else {
                // Always save new GRN items as 'received' status
                $newOrderItem = PurchaseOrderItem::create([
                    'order_id' => $this->selectedPO->id,
                    'product_id' => $productId,
                    'quantity' => $receivedQty,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'discount_type' => $item['discount_type'] ?? 'rs',
                    'status' => 'received',
                ]);

                // Update stock for new received item
                if ($receivedQty > 0) {
                    $this->updateProductStock($productId, $receivedQty);
                    $receivedItemsCount++;
                }
            }
        }

        // Update order received date and status based on received items
        $this->selectedPO->received_date = now();

        // Determine overall order status
        if ($receivedItemsCount > 0 && $receivedItemsCount === $totalItemsCount) {
            // All items received - mark as fully received
            $this->selectedPO->status = 'received';
        } elseif ($receivedItemsCount > 0) {
            // Some items received but not all - keep as complete (partial receipt)
            $this->selectedPO->status = 'complete';
        }
        // If no items received, status remains as it was

        $this->selectedPO->save();

        $this->dispatch('alert', ['message' => 'GRN processed successfully! Stock updated.']);
        $this->selectedPO = null;
        $this->grnItems = [];
        $this->searchResults = ['unplanned' => []];
        $this->loadPurchaseOrders();
    }

    private function updateProductStock($productId, $quantity)
    {
        $stock = ProductStock::where('product_id', $productId)->first();

        if ($stock) {
            // Update existing stock
            $stock->available_stock += $quantity;
            $stock->total_stock += $quantity;
            $stock->restocked_quantity += $quantity;
            $stock->save();
        } else {
            // Create new stock record
            ProductStock::create([
                'product_id' => $productId,
                'available_stock' => $quantity,
                'damage_stock' => 0,
                'total_stock' => $quantity,
                'sold_count' => 0,
                'restocked_quantity' => $quantity,
            ]);
        }
    }

    // Calculate discount amount in rupees
    public function calculateDiscountAmount($item)
    {
        $discountType = $item['discount_type'] ?? 'rs';
        $discount = floatval($item['discount'] ?? 0);
        $unitPrice = floatval($item['unit_price'] ?? 0);
        $receivedQty = floatval($item['received_qty'] ?? 0);

        if ($discountType === 'percent') {
            // Calculate percentage discount
            $subtotal = $receivedQty * $unitPrice;
            return ($subtotal * $discount) / 100;
        }
        
        // Return discount as is (it's already in rupees)
        return $discount;
    }

    // Calculate total for an item
    public function calculateItemTotal($item)
    {
        $receivedQty = floatval($item['received_qty'] ?? 0);
        $unitPrice = floatval($item['unit_price'] ?? 0);
        $subtotal = $receivedQty * $unitPrice;
        $discountAmount = $this->calculateDiscountAmount($item);
        
        return max(0, $subtotal - $discountAmount);
    }

    public function render()
    {
        return view('livewire.admin.g-r-n', [
            'purchaseOrders' => $this->purchaseOrders,
        ]);
    }
}
