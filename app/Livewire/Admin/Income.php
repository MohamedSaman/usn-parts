<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\CashInHand;
use App\Models\Deposit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Livewire\Concerns\WithDynamicLayout;

#[Title("Deposit By Cash")]
class Income extends Component
{
    use WithDynamicLayout;

    public $todayIncome;
    public $cashIncome;
    public $chequeIncome;
    public $creditIncome;
    public $cashInHand;

    public $newCashInHand;

    public $depositDate;
    public $depositAmount;
    public $depositDescription;

    public $totalDeposits;
    public $thisMonthDeposit = 0;
    public $previousMonthDeposit = 0;
    public $todayDeposits = 0;

    public function mount()
    {
        $today = now()->toDateString();

        // Today's total income
        $this->todayIncome = Payment::whereDate('payment_date', $today)->sum('amount');

        // Breakdown by payment method
        $this->cashIncome = Payment::whereDate('payment_date', $today)
            ->where('payment_method', 'cash')
            ->sum('amount');

        $this->chequeIncome = Payment::whereDate('payment_date', $today)
            ->where('payment_method', 'cheque')
            ->sum('amount');

        $this->creditIncome = Payment::whereDate('payment_date', $today)
            ->where('payment_method', 'credit')
            ->sum('amount');

        // Get or create Cash in Hand
        $cashRecord = CashInHand::firstOrCreate(
            ['key' => 'cash in hand'],
            ['value' => 0]
        );

        $this->cashInHand = (int) $cashRecord->value;
        $this->newCashInHand = $this->cashInHand;

        // Calculate deposits
        $this->calculateDeposits();
        $this->calculateTodayDeposits();



        $this->depositDate = $today;
    }

    protected function calculateDeposits()
    {
        $this->thisMonthDeposit = Deposit::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $this->previousMonthDeposit = Deposit::whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->sum('amount');

        $this->totalDeposits = Deposit::sum('amount');
    }

    protected function calculateTodayDeposits()
    {
        $this->todayDeposits = Deposit::whereDate('date', now()->toDateString())
            ->sum('amount');
    }

    // Updates CashInHand in DB considering today's deposits
    protected function updateCashInHandFromDeposits()
    {
        $cashRecord = CashInHand::where('key', 'cash in hand')->first();

        if ($cashRecord) {
            $extra = max(0, $this->todayDeposits - $this->cashIncome);
            $newCash = max(0, $cashRecord->value - $extra);

            $cashRecord->update(['value' => $newCash]);
            $this->cashInHand = $newCash;

        }
    }

    public function addDeposit()
    {
        $this->validate([
            'depositDate' => 'required|date',
            'depositAmount' => 'required|numeric|min:0.01',
            'depositDescription' => 'nullable|string|max:255',
        ]);

        Deposit::create([
            'date' => $this->depositDate,
            'amount' => $this->depositAmount,
            'description' => $this->depositDescription,
        ]);

        $this->calculateDeposits();
        $this->calculateTodayDeposits();
        $this->updateCashInHandFromDeposits();

        $this->reset(['depositAmount', 'depositDescription']);

        $this->js("Swal.fire('Success!', 'Deposit added and Cash in Hand updated.', 'success')");
        $this->dispatch('close-modal', modalId: 'addIncomeModal');
                    $this->dispatch('refreshPage');

    }

    public function deleteDeposit($id)
    {
        Deposit::find($id)?->delete();

        $this->calculateDeposits();
        $this->calculateTodayDeposits();
        $this->updateCashInHandFromDeposits();

        $this->js("Swal.fire('Deleted!', 'Deposit has been deleted and Cash in Hand updated.', 'success')");
                            $this->dispatch('refreshPage');

    }

    public function updateCashInHand()
    {
        $this->validate([
            'newCashInHand' => 'required|integer|min:0',
        ]);

        $record = CashInHand::where('key', 'cash in hand')->first();

        if ($record) {
            $record->update(['value' => $this->newCashInHand]);
            $this->cashInHand = $this->newCashInHand;
        } else {
            // Create new record if missing
            CashInHand::create([
                'key' => 'cash in hand',
                'value' => $this->newCashInHand,
            ]);
            $this->cashInHand = $this->newCashInHand;
        }

        // ✅ SweetAlert confirmation
        $this->js("Swal.fire('Success!', 'Cash in Hand updated successfully.', 'success')");

        // ✅ Close the correct modal (the one in your top bar)
        $this->dispatch('close-modal', modalId: 'addCashInHandModal');
        $this->dispatch('close-modal', modalId: 'editCashModal');
                            $this->dispatch('refreshPage');

    }


    public function render()
    {
        $deposits = Deposit::latest()->get();

        return view('livewire.admin.income', [
            'deposits' => $deposits,
        ])->layout($this->layout);
    }
}
