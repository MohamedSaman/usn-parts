<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductSupplier;
use App\Models\ProductDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Str;
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
        $this->products = ProductDetail::where('name', 'like', '%' . $this->search . '%')->limit(5)->get();
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
            'status' => 'Pending',
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

        $this->reset(['supplier_id', 'search', 'selectedProduct', 'quantity', 'orderItems']);

        session()->flash('success', 'Purchase Order created successfully!');
        $this->dispatch('close-modal'); // ✅ updated
    }

    public function loadOrders()
    {
        $this->orders = PurchaseOrder::with(['supplier', 'items.product'])->get();
    }

    public function viewOrder($id)
    {
        $this->selectedOrder = PurchaseOrder::with(['supplier', 'items.product'])->find($id);
    }

    public function confirmComplete($id)
    {
        $this->dispatch('confirm-complete', ['id' => $id]); // ✅ updated
    }

    public function completeOrder($id)
    {
        $order = PurchaseOrder::find($id);
        if ($order) {
            $order->status = 'Completed';
            $order->save();
            $this->loadOrders();
            $this->dispatch('alert', ['message' => 'Order marked as completed!']); // ✅ updated
        }
    }

    public function render()
    {
        $pendingCount = PurchaseOrder::where('status', 'Pending')->count();
        $completedCount = PurchaseOrder::where('status', 'Completed')->count();
        $orders = PurchaseOrder::with(['supplier'])->latest()->get();

        return view('livewire.admin.quotation', compact('pendingCount', 'completedCount', 'orders'));
    }
}
