<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cheque;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
#[Title('Cheque List')]
class ChequeList extends Component
{
    use WithPagination;

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

    public function confirmComplete($id)
    {
        $this->js("
            Swal.fire({
                title: 'Mark as Complete?',
                text: 'Are you sure you want to mark this cheque as complete?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, mark as complete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    \$wire.completeCheque({$id});
                }
            });
        ");
    }

    public function confirmReturn($id)
    {
        $this->js("
            Swal.fire({
                title: 'Return Cheque?',
                text: 'Are you sure you want to return this cheque?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, return cheque!'
            }).then((result) => {
                if (result.isConfirmed) {
                    \$wire.returnCheque({$id});
                }
            });
        ");
    }

    public function completeCheque($id)
    {
        try {
            $cheque = Cheque::find($id);
            
            if (!$cheque) {
                $this->js("Swal.fire('Error', 'Cheque not found!', 'error');");
                return;
            }

            $cheque->status = 'complete';
            $cheque->save();

            // Refresh the data
          

            $this->js("
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Cheque marked as complete successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            ");

        } catch (\Exception $e) {
            Log::error("Error completing cheque: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to mark cheque as complete!', 'error');");
        }
    }

    public function returnCheque($id)
    {
        try {
            $cheque = Cheque::find($id);
            
            if (!$cheque) {
                $this->js("Swal.fire('Error', 'Cheque not found!', 'error');");
                return;
            }

            $cheque->status = 'return';
            $cheque->save();

            // Refresh the data
            

            $this->js("
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Cheque returned successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            ");

        } catch (\Exception $e) {
            Log::error("Error returning cheque: " . $e->getMessage());
            $this->js("Swal.fire('Error', 'Failed to return cheque!', 'error');");
        }
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