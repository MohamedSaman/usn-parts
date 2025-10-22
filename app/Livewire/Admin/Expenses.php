<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Expense;
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

    // Form inputs for creating
    public $category, $amount, $date, $status, $description;

    // Form inputs for editing
    public $expenseId;
    public $edit_category, $edit_amount, $edit_date, $edit_status, $edit_description, $edit_expense_type;

    // Delete confirmation
    public $expenseToDelete;

    // Modal states
    public $showEditDailyModal = false;
    public $showEditMonthlyModal = false;
    public $showDeleteModal = false;
    public $showViewModal = false;
    public $viewExpense = null;

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
            'amount' => 'required|numeric|min:0',
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
        $this->js("swal.fire('Success!', 'Daily expense added successfully.', 'success')");
        $this->dispatch('close-modal', 'addDailyExpenseModal');
        $this->dispatch('refreshPage');
    }

    public function saveMonthlyExpense()
    {
        $this->validate([
            'date' => 'required|date',
            'category' => 'required',
            'amount' => 'required|numeric|min:0',
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
        $this->js("swal.fire('Success!', 'Monthly expense added successfully.', 'success')");
        $this->dispatch('close-modal', 'addMonthlyExpenseModal');
        $this->dispatch('refreshPage');
    }

    public function confirmDelete($id)
    {
        $this->expenseToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteExpense()
    {
        if ($this->expenseToDelete) {
            Expense::findOrFail($this->expenseToDelete)->delete();
            $this->loadExpenses();
            $this->js("swal.fire('Deleted!', 'Expense has been deleted.', 'success')");
            $this->showDeleteModal = false;
            $this->expenseToDelete = null;
            $this->dispatch('refreshPage');
        }
    }

    public function editExpense($id)
    {
        $expense = Expense::findOrFail($id);

        $this->expenseId = $expense->id;
        $this->edit_category = $expense->category;
        $this->edit_description = $expense->description;
        $this->edit_amount = $expense->amount;
        $this->edit_date = $expense->date ? $expense->date->format('Y-m-d') : '';
        $this->edit_status = $expense->status;
        $this->edit_expense_type = $expense->expense_type;

        // Open modal based on expense type
        if ($expense->expense_type === 'daily') {
            $this->showEditDailyModal = true;
        } else {
            $this->showEditMonthlyModal = true;
        }
    }

    public function viewExpense($id)
    {
        $this->viewExpense = Expense::findOrFail($id);
        $this->showViewModal = true;
    }

    public function updateExpense()
    {
        $this->validate([
            'edit_category' => 'required|string',
            'edit_amount' => 'required|numeric|min:0',
        ]);

        $expense = Expense::findOrFail($this->expenseId);

        $updateData = [
            'category' => $this->edit_category,
            'description' => $this->edit_description,
            'amount' => $this->edit_amount,
        ];

        if ($expense->expense_type === 'monthly') {
            $this->validate(['edit_date' => 'required|date']);
            $updateData['date'] = $this->edit_date;
            $updateData['status'] = $this->edit_status;
        } else {
            // For daily, use today's date
            $updateData['date'] = now();
        }

        $expense->update($updateData);

        // Close the modals
        $this->showEditDailyModal = false;
        $this->showEditMonthlyModal = false;

        $this->resetEditFields();
        $this->loadExpenses();
        $this->js("swal.fire('Success!', 'Expense updated successfully.', 'success')");
        $this->dispatch('refreshPage');
    }

    public function resetEditFields()
    {
        $this->reset([
            'expenseId', 'edit_category', 'edit_amount', 'edit_date', 
            'edit_status', 'edit_description', 'edit_expense_type'
        ]);
        $this->resetErrorBag();
    }

    public function resetFields()
    {
        $this->reset(['category', 'amount', 'date', 'status', 'description']);
        $this->resetErrorBag();
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewExpense = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->expenseToDelete = null;
    }

    public function closeEditDailyModal()
    {
        $this->showEditDailyModal = false;
        $this->resetEditFields();
    }

    public function closeEditMonthlyModal()
    {
        $this->showEditMonthlyModal = false;
        $this->resetEditFields();
    }

    public function render()
    {
        return view('livewire.admin.expenses');
    }
}