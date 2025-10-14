<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Supplier Management")]
#[Layout('components.layouts.admin')]
class SupplierManage extends Component
{
    public function render()
    {
        return view('livewire.admin.supplier-manage');
    }
}
