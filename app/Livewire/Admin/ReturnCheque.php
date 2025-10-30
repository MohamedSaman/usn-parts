<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cheque;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Return Cheque')]
class ReturnCheque extends Component
{
    use WithPagination;

    public $showRechequeModal = false;
    public $selectedChequeId = null;

    // New cheque fields
    public $newChequeNumber;
    public $newBankName;
    public $newChequeDate;
    public $newChequeAmount;

    protected $rules = [
        'newChequeNumber' => 'required|string|max:255',
        'newBankName' => 'required|string|max:255',
        'newChequeDate' => 'required|date',
        'newChequeAmount' => 'required|numeric|min:0.01',
    ];

    public function getChequesProperty()
    {
        // Only return and cancelled cheques
        return Cheque::with('customer')
            ->whereIn('status', ['return', 'cancelled'])
            ->orderByRaw("CASE WHEN status = 'return' THEN 0 ELSE 1 END ASC")
            ->orderByDesc('cheque_date')
            ->paginate(20);
    }

    public function getReturnCountProperty()
    {
        return Cheque::where('status', 'return')->count();
    }

    public function getCancelledCountProperty()
    {
        return Cheque::where('status', 'cancelled')->count();
    }

    public function getOverdueCountProperty()
    {
        return Cheque::where('status', 'overdue')->count();
    }

    public function setSelectedCheque($id)
    {
        $this->selectedChequeId = $id;
        $this->showRechequeModal = true;
        $this->dispatch('showModal', 'rechequeModal');
    }

    public function rechequeSubmit()
    {
        $this->validate();
        $oldCheque = Cheque::find($this->selectedChequeId);
        if ($oldCheque) {
            // Mark old cheque as cancelled
            $oldCheque->status = 'cancelled';
            $oldCheque->save();

            // Add new cheque (copy customer/payment if needed)
            Cheque::create([
                'cheque_number' => $this->newChequeNumber,
                'bank_name' => $this->newBankName,
                'cheque_date' => $this->newChequeDate,
                'cheque_amount' => $this->newChequeAmount,
                'status' => 'pending',
                'customer_id' => $oldCheque->customer_id,
                'payment_id' => $oldCheque->payment_id,
            ]);

            $this->dispatch('showToast', ['type' => 'success', 'message' => 'New cheque added and old cheque cancelled.']);
        }
        $this->closeModals();
    }

    public function closeModals()
    {
        $this->showRechequeModal = false;
        $this->selectedChequeId = null;
        $this->reset(['newChequeNumber', 'newBankName', 'newChequeDate', 'newChequeAmount']);
        $this->dispatch('hideModal', 'rechequeModal');
    }

    public function render()
    {
        return view('livewire.admin.return-cheque', [
            'cheques' => $this->cheques,
            'returnCount' => $this->returnCount,
            'cancelledCount' => $this->cancelledCount,
            'overdueCount' => $this->overdueCount,
        ]);
    }
}
