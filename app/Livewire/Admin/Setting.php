<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Exception;

#[Title("System Settings")]
#[Layout('components.layouts.admin')]
class Setting extends Component
{
    public $category;
    public $expense_type = 'daily';
    public $isEditing = false;
    public $editingId = null;

    protected function rules()
    {
        return [
            'category' => 'required|string|max:255',
            'expense_type' => 'required|in:daily,monthly',
        ];
    }

    public function addCategory()
    {
        $this->validate();

        $exists = Expense::whereRaw('LOWER(category) = ?', [strtolower($this->category)])
            ->where('expense_type', $this->expense_type)
            ->exists();

        if ($exists) {
            $this->js("Swal.fire('Error!', 'This expense category already exists.', 'error')");
            return;
        }

        try {
            Expense::create([
                'category' => trim($this->category),
                'expense_type' => $this->expense_type,
                'amount' => 0,
                'date' => now(),
            ]);

            $this->js("Swal.fire('Success!', 'Expense category added successfully.', 'success')");
            $this->dispatch('resetForm');
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', 'Failed to add category.', 'error')");
        }
    }

    public function editCategory($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            $this->js("Swal.fire('Error!', 'Category not found.', 'error')");
            return;
        }

        $this->editingId = $expense->id;
        $this->category = $expense->category;
        $this->expense_type = $expense->expense_type;
        $this->isEditing = true;

        $this->js("$('#editCategoryModal').modal('show')");
    }

    public function updateCategory()
{
    $this->validate();

    $expense = Expense::find($this->editingId);
    if ($expense) {
        $expense->update([
            'category' => $this->category,
            'expense_type' => $this->expense_type,
        ]);

        // ✅ Reset and close modal
        $this->reset(['category', 'expense_type', 'isEditing', 'editingId']);
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Updated!',
            'text' => 'Expense category updated successfully.',
        ]);
    }
}

    public function deleteCategory($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($id)
    {
        $expense = Expense::find($id);

        if ($expense) {
            $expense->delete();
            $this->js("Swal.fire('Deleted!', 'Expense category deleted successfully.', 'success')");
        } else {
            $this->js("Swal.fire('Error!', 'Category not found.', 'error')");
        }
    }

    private function resetFields()
    {
        $this->reset(['category', 'expense_type', 'isEditing', 'editingId']);
        $this->expense_type = 'daily';
    }

    public function closeEditModal()
{
    $this->reset(['category', 'expense_type', 'isEditing', 'editingId']);
    $this->expense_type = 'daily'; // reset to default
    $this->dispatch('closeModal');
}

    public function render()
    {
        return view('livewire.admin.setting', [
            'expenseCategories' => Expense::orderBy('expense_type')
                ->orderBy('category')
                ->get(),
        ]);
    }
}
