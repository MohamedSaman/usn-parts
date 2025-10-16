<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Carbon\Carbon;

#[Title("Expenses")]
#[Layout('components.layouts.admin')]
class Expenses extends Component
{
    // Data variables
    public $dailyExpenses = [];
    public $monthlyExpenses = [];

    // Totals
    public $todayTotal = 0;
    public $monthTotal = 0;
    public $overallTotal = 0;

    // Form inputs
    public $category, $amount, $date, $status, $description, $expense_type;

    public function mount()
    {
        $this->loadExpenses();
    }

    public function loadExpenses()
    {
        // Daily and Monthly lists
        $this->dailyExpenses = Expense::where('expense_type', 'daily')->latest()->get();
        $this->monthlyExpenses = Expense::where('expense_type', 'monthly')->latest()->get();

        // Totals
        $this->todayTotal = Expense::whereDate('date', Carbon::today())->sum('amount');
        $this->monthTotal = Expense::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');
        $this->overallTotal = Expense::sum('amount');
    }

    public function saveDailyExpense()
    {
        $this->validate([
            'category' => 'required',
            'amount' => 'required|numeric',
        ]);

        Expense::create([
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => now(),
            'expense_type' => 'daily',
        ]);

        $this->reset(['category', 'amount', 'description']);
        $this->loadExpenses();
        $this->js('window.location.reload()');


        session()->flash('success', 'Daily expense added successfully.');
        $this->dispatch('close-modal', id: 'addDailyExpenseModal');
    }

    public function saveMonthlyExpense()
    {
        $this->validate([
            'date' => 'required|date',
            'category' => 'required',
            'amount' => 'required|numeric',
        ]);

        Expense::create([
            'date' => $this->date,
            'category' => $this->category,
            'amount' => $this->amount,
            'status' => $this->status,
            'description' => $this->description,
            'expense_type' => 'monthly',
        ]);

        $this->reset(['date', 'category', 'amount', 'status', 'description']);
        $this->loadExpenses();
        $this->js('window.location.reload()');


        session()->flash('success', 'Monthly expense added successfully.');
        $this->dispatch('close-modal', id: 'addMonthlyExpenseModal');
    }

    public function deleteExpense($id)
    {
        Expense::findOrFail($id)->delete();
        $this->loadExpenses();
        $this->js('window.location.reload()');

        session()->flash('success', 'Expense deleted.');
    }

    public $expenseId;

    public function editExpense($id)
    {
        $expense = Expense::findOrFail($id);

        $this->expenseId   = $expense->id;
        $this->category    = $expense->category;
        $this->description = $expense->description;
        $this->amount      = $expense->amount;
        $this->date        = $expense->date ? date('Y-m-d', strtotime($expense->date)) : '';
        $this->status      = $expense->status;
        $this->expense_type = $expense->expense_type;

        // Open modal based on expense type
        if ($expense->expense_type === 'daily') {
            $this->dispatch('open-modal', id: 'editDailyExpenseModal');
        } else {
            $this->dispatch('open-modal', id: 'editMonthlyExpenseModal');
        }
    }

    public function updateExpense()
    {
        $this->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense = Expense::findOrFail($this->expenseId);

        $updateData = [
            'category' => $this->category,
            'description' => $this->description,
            'amount' => $this->amount,
        ];

        if ($expense->expense_type === 'monthly') {
            $this->validate(['date' => 'required|date']);
            $updateData['date'] = $this->date;
            $updateData['status'] = $this->status;
        } else {
            // For daily, use today's date
            $updateData['date'] = now();
        }

        $expense->update($updateData);

        // Close the right modal
        if ($expense->expense_type === 'daily') {
            $this->dispatch('close-modal', id: 'editDailyExpenseModal');
        } else {
            $this->dispatch('close-modal', id: 'editMonthlyExpenseModal');
        }

        $this->reset(['category', 'description', 'amount', 'date', 'status', 'expenseId', 'expense_type']);
        $this->loadExpenses();
        session()->flash('message', 'Expense updated successfully!');
    }


    public function resetFields()
    {
        $this->reset(['category', 'amount', 'date', 'status', 'description', 'expense_type']);
        $this->resetErrorBag(); // clear validation errors too
    }


    public function render()
    {
        return view('livewire.admin.expenses');
    }
}
