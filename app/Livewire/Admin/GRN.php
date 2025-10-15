<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ProductDetail;
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
    public $newItem = ['product_id' => null, 'name' => '', 'qty' => 1, 'unit_price' => 0, 'discount' => 0, 'status' => 'pending'];

    protected $listeners = ['deleteGRNItem'];

    public function mount()
    {
        $this->loadPurchaseOrders();
        $this->searchResults = ['unplanned' => []];
    }

    public function loadPurchaseOrders()
    {
        $this->purchaseOrders = PurchaseOrder::where('status', 'complete')
            ->with(['supplier', 'items.product'])
            ->latest()
            ->get();
    }

    public function openGRN($orderId)
    {
        $this->selectedPO = PurchaseOrder::with(['supplier', 'items.product'])->find($orderId);
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
                'status' => $item->status ?? 'pending',
            ];
        }
    }

    public function updated($propertyName)
    {
        if (preg_match('/grnItems\.(\d+)\.name/', $propertyName, $matches)) {
            $index = $matches[1];
            $searchTerm = $this->grnItems[$index]['name'];
            if (strlen($searchTerm) > 1) {
                $this->searchResults[$index] = ProductDetail::where('name', 'like', "%{$searchTerm}%")
                    ->with('price')
                    ->limit(5)
                    ->get();
            } else {
                $this->searchResults[$index] = [];
            }
        } elseif ($propertyName === 'searchProduct') {
            if (strlen($this->searchProduct) > 1) {
                $this->searchResults['unplanned'] = ProductDetail::where('name', 'like', "%{$this->searchProduct}%")
                    ->with('price')
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
                $this->newItem['status'] = 'pending';
                $this->searchProduct = $product->name;
                $this->searchResults['unplanned'] = [];
            } else {
                $this->grnItems[$index]['product_id'] = $product->id;
                $this->grnItems[$index]['name'] = $product->name;
                $this->grnItems[$index]['unit_price'] = $unitPrice;
                $this->grnItems[$index]['status'] = 'pending';
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
            'status' => 'pending',
        ];

        $this->newItem = ['product_id' => null, 'name' => '', 'qty' => 1, 'unit_price' => 0, 'discount' => 0, 'status' => 'pending'];
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
            'status' => 'pending',
        ];
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
        if (isset($this->grnItems[$index]['id'])) {
            $orderItem = PurchaseOrderItem::find($this->grnItems[$index]['id']);
            if ($orderItem) {
                $orderItem->status = 'received';
                $orderItem->quantity = $this->grnItems[$index]['received_qty'];
                $orderItem->unit_price = $this->grnItems[$index]['unit_price'];
                $orderItem->discount = $this->grnItems[$index]['discount'];
                $orderItem->save();
            }
        }
        $this->grnItems[$index]['status'] = 'received';
    }

    public function saveGRN()
    {
        if (!$this->selectedPO || empty($this->grnItems)) return;

        foreach ($this->grnItems as $item) {
            if (isset($item['id'])) {
                // Update existing order item
                $orderItem = PurchaseOrderItem::find($item['id']);
                if ($orderItem) {
                    $orderItem->quantity = $item['received_qty'];
                    $orderItem->unit_price = $item['unit_price'];
                    $orderItem->discount = $item['discount'];
                    $orderItem->status = $item['status'];
                    $orderItem->save();
                }
            } else {
                // Create new item
                PurchaseOrderItem::create([
                    'order_id' => $this->selectedPO->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['received_qty'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'status' => $item['status'],
                ]);
            }
        }

        // Update order received date and status
        $this->selectedPO->received_date = now();
        $this->selectedPO->status = 'received';
        $this->selectedPO->save();

        $this->dispatch('alert', ['message' => 'GRN processed successfully!']);
        $this->selectedPO = null;
        $this->grnItems = [];
        $this->searchResults = ['unplanned' => []];
        $this->loadPurchaseOrders();
    }

    public function render()
    {
        return view('livewire.admin.g-r-n', [
            'purchaseOrders' => $this->purchaseOrders,
        ]);
    }
}