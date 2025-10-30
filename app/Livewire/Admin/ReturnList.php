<?php

namespace App\Livewire\Admin;

use Livewire\Component;

use App\Models\ReturnsProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title("Product Return")]
#[Layout('components.layouts.admin')]
class ReturnList extends Component
{
    public $returns = [];
    public $selectedReturn = null;

    public function mount()
    {
        $this->returns = ReturnsProduct::with(['sale', 'product'])->orderByDesc('created_at')->get();
    }

    public function showReturnDetails($id)
    {
        $this->selectedReturn = ReturnsProduct::with(['sale', 'product'])->find($id);
        $this->dispatch('showModal', 'returnDetailsModal');
    }

    public function closeModal()
    {
        $this->selectedReturn = null;
        $this->dispatch('hideModal', 'returnDetailsModal');
    }

    public function render()
    {
        return view('livewire.admin.return-list', [
            'returns' => $this->returns,
            'selectedReturn' => $this->selectedReturn,
        ]);
    }
}
