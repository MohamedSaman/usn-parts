<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title("Expenses")]
#[Layout('components.layouts.admin')]
class Expenses extends Component
{
    public function render()
    {
        return view('livewire.admin.expenses');
    }
}
