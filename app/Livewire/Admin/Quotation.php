<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Quotation")]
#[Layout('components.layouts.admin')]
class Quotation extends Component
{
    public function render()
    {
        return view('livewire.admin.quotation');
    }
}
