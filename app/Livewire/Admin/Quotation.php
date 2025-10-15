<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductSupplier;
use App\Models\ProductDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title("Purchase Order")]
#[Layout('components.layouts.admin')]
class Quotation extends Component
{
    public $suppliers = [];
    public $supplier_id = '';
    public $search = '';
    public $products = [];
    public $selectedProduct = null;
    public $quantity = 1;
    public $orderItems = [];
    public $orders;
    public $selectedOrder;

    public function mount()
    {
        $this->suppliers = ProductSupplier::all();
        $this->loadOrders();
    }

    public function updatedSearch()
    {
        if ($this->search != '') {
            $this->products = ProductDetail::where('name', 'like', '%' . $this->search . '%')
                ->limit(5)
                ->get();
        } else {
            $this->products = [];
        }
    }

    public function selectProduct($id)
    {
        $this->selectedProduct = ProductDetail::find($id);
        $this->products = [];
        $this->search = $this->selectedProduct->name;
    }

    public function addItem()
    {
        if (!$this->selectedProduct || $this->quantity < 1) return;

        $this->orderItems[] = [
            'product_id' => $this->selectedProduct->id,
            'name' => $this->selectedProduct->name,
            'quantity' => $this->quantity,
        ];

        $this->selectedProduct = null;
        $this->search = '';
        $this->quantity = 1;
    }

    public function removeItem($index)
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
    }

    public function saveOrder()
    {
        if (!$this->supplier_id || count($this->orderItems) == 0) return;

        $orderCode = 'ORD-' . date('Y') . '-' . str_pad(PurchaseOrder::count() + 1, 3, '0', STR_PAD_LEFT);

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
                'unit_price' => 0,
                'discount' => 0,
            ]);
        }

        // Refresh table
        $this->loadOrders();

        $this->reset(['supplier_id', 'search', 'selectedProduct', 'quantity', 'orderItems']);
        $this->js("bootstrap.Modal.getInstance(document.getElementById('addPurchaseOrderModal')).hide();");
        $this->js("Swal.fire('Success', 'Purchase Order created successfully!', 'success');");
    }

    public function loadOrders()
    {
        $this->orders = PurchaseOrder::with(['supplier', 'items.product'])->latest()->get();
    }

    public function viewOrder($id)
    {
        $this->selectedOrder = PurchaseOrder::with(['supplier', 'items.product'])->find($id);
    }

    public function confirmComplete($id)
    {
        $this->js("
            Swal.fire({
                title: 'Mark order as complete?',
                text: 'Are you sure you want to complete this order?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    \$wire.completeOrderConfirmed($id);
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

    public function render()
    {
        $pendingCount = PurchaseOrder::where('status', 'pending')->count();
        $completedCount = PurchaseOrder::where('status', 'complete')->count();

        return view('livewire.admin.quotation', compact('pendingCount', 'completedCount'));
    }
}
