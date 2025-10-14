<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Title("Goods Receive Note")]
#[Layout('components.layouts.admin')]
class GRN extends Component
{
    public function render()
    {
        return view('livewire.admin.g-r-n');
    }
}
