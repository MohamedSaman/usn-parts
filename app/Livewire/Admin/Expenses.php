<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Expenses")]
#[Layout('components.layouts.admin')]
class Expenses extends Component
{
    public function render()
    {
        return view('livewire.admin.expenses');
    }
}
