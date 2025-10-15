<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Income")]
#[Layout('components.layouts.admin')]
class Income extends Component
{
    public function render()
    {
        return view('livewire.admin.income');
    }
}
