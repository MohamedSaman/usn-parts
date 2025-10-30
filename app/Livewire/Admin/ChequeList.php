<?php

namespace App\Livewire\Admin;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cheque; // You must have a Cheque model
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Cheque List')]
class ChequeList extends Component
{
    use WithPagination;

    public $showCompleteModal = false;
    public $showReturnModal = false;
    public $selectedChequeId = null;

    protected $listeners = [
        'closeModals' => 'closeModals',
    ];

    public function getChequesProperty()
    {
        // Show pending cheques first, then others by cheque_date desc
        return Cheque::with('customer')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC")
            ->orderByDesc('cheque_date')
            ->paginate(20);
    }

    public function getPendingCountProperty()
    {
        return Cheque::where('status', 'pending')->count();
    }

    public function getCompleteCountProperty()
    {
        return Cheque::where('status', 'complete')->count();
    }

    public function getOverdueCountProperty()
    {
        return Cheque::where('status', 'overdue')->count();
    }

    public function confirmComplete($chequeId)
    {
        $this->selectedChequeId = $chequeId;
        $this->showCompleteModal = true;
        $this->dispatch('showModal', 'confirmCompleteModal');
    }

    public function confirmReturn($chequeId)
    {
        $this->selectedChequeId = $chequeId;
        $this->showReturnModal = true;
        $this->dispatch('showModal', 'confirmReturnModal');
    }

    public function completeCheque()
    {
        if ($this->selectedChequeId) {
            $cheque = Cheque::find($this->selectedChequeId);
            if ($cheque) {
                // Mark as complete
                $cheque->status = 'complete';
                $cheque->save();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Cheque marked as complete.']);
            }
        }
        $this->closeModals();
    }

    public function returnCheque()
    {
        if ($this->selectedChequeId) {
            $cheque = Cheque::find($this->selectedChequeId);
            if ($cheque) {
                $cheque->status = 'return';
                $cheque->save();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Cheque marked as returned.']);
            }
        }
        $this->closeModals();
    }

    public function setSelectedCheque($id)
    {
        $this->selectedChequeId = $id;
    }


    public function closeModals()
    {
        $this->showCompleteModal = false;
        $this->showReturnModal = false;
        $this->selectedChequeId = null;
        $this->dispatch('hideModal', 'confirmCompleteModal');
        $this->dispatch('hideModal', 'confirmReturnModal');
    }

    public function render()
    {
        return view('livewire.admin.cheque-list', [
            'cheques' => $this->cheques,
            'pendingCount' => $this->pendingCount,
            'completeCount' => $this->completeCount,
            'overdueCount' => $this->overdueCount,
        ]);
    }
}
