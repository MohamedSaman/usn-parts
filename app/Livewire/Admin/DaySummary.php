<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDynamicLayout;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\POSSession;
use Illuminate\Support\Facades\Auth;

#[Title('Day Summary List')]

class DaySummary extends Component
{
    use WithPagination;
    use WithDynamicLayout;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        // Set default date range to current month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function viewDetails($sessionId)
    {
        return redirect()->route('admin.day-summary-details', ['sessionId' => $sessionId]);
    }

    public function render()
    {
        $sessions = POSSession::query()
            ->with('user')
            ->where('status', 'closed')
            ->when($this->search, function ($query) {
                $query->where('session_date', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('session_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('session_date', '<=', $this->dateTo);
            })
            ->orderBy('session_date', 'desc')
            ->paginate(15);

        return view('livewire.admin.day-summary', [
            'sessions' => $sessions
        ])->layout($this->layout);
    }
}
