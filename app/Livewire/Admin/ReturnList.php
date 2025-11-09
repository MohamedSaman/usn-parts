<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ReturnsProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\Concerns\WithDynamicLayout;

#[Title("Product Return")]
class ReturnList extends Component
{
    use WithDynamicLayout;

    public $returns = [];
    public $selectedReturn = null;
    public $showReceiptModal = false;
    public $currentReturnId = null;

    public function mount()
    {
        $this->returns = ReturnsProduct::with(['sale', 'product'])->orderByDesc('created_at')->get();
    }

    public function showReturnDetails($id)
    {
        $this->selectedReturn = ReturnsProduct::with(['sale', 'product'])->find($id);
        $this->dispatch('showModal', 'returnDetailsModal');
    }

    public function showReceipt($returnId)
    {
        $this->selectedReturn = ReturnsProduct::with(['sale.customer', 'product'])->find($returnId);
        $this->currentReturnId = $returnId;
        $this->showReceiptModal = true;
        $this->dispatch('showModal', 'receiptModal');
    }

    public function downloadReturn($returnId)
    {
        $return = ReturnsProduct::with(['sale.customer', 'product'])->find($returnId);

        if (!$return) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Return record not found.']);
            return;
        }

        try {
            $pdf = PDF::loadView('admin.returns.return-receipt', compact('return'));

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('dpi', 150);
            $pdf->setOption('defaultFont', 'sans-serif');

            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                'return-receipt-' . $return->id . '-' . now()->format('Y-m-d') . '.pdf'
            );
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    public function printReceipt()
    {
        $this->dispatch('printReceipt');
    }

    public function deleteReturn($returnId)
    {
        $this->selectedReturn = ReturnsProduct::find($returnId);
        $this->currentReturnId = $returnId;
        $this->dispatch('showModal', 'deleteReturnModal');
    }

    public function confirmDeleteReturn()
    {
        try {
            if ($this->selectedReturn) {
                // Restore the stock before deleting the return record
                $this->restoreStock($this->selectedReturn);
                
                $this->selectedReturn->delete();
                
                $this->returns = ReturnsProduct::with(['sale', 'product'])->orderByDesc('created_at')->get();
                
                $this->dispatch('hideModal', 'deleteReturnModal');
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Return record deleted successfully!']);
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Error deleting return: ' . $e->getMessage()]);
        }
    }

    private function restoreStock($return)
    {
        // Decrease the available stock since we're deleting a return
        $productStock = \App\Models\ProductStock::where('product_id', $return->product_id)->first();
        
        if ($productStock) {
            $productStock->available_stock -= $return->return_quantity;
            if ($productStock->sold_count >= $return->return_quantity) {
                $productStock->sold_count += $return->return_quantity;
            }
            $productStock->save();
        }
    }

    public function closeModal()
    {
        $this->selectedReturn = null;
        $this->currentReturnId = null;
        $this->showReceiptModal = false;
        $this->dispatch('hideModal', 'returnDetailsModal');
        $this->dispatch('hideModal', 'deleteReturnModal');
        $this->dispatch('hideModal', 'receiptModal');
    }

    public function render()
    {
        return view('livewire.admin.return-list', [
            'returns' => $this->returns,
            'selectedReturn' => $this->selectedReturn,
            'currentReturnId' => $this->currentReturnId,
        ])->layout($this->layout);
    }
}