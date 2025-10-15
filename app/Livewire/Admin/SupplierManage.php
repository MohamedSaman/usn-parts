<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\ProductSupplier;
use Livewire\WithPagination;

#[Title("Supplier Management")]
#[Layout('components.layouts.admin')]
class SupplierManage extends Component
{
    use WithPagination;

    public $supplierId ;
    public $name;
    public $businessname;
    public $contact;
    public $address;
    public $email;
    public $phone;
    public $status = 'active';
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:255',
        'businessname' => 'nullable|string|max:255',
        'contact' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'notes' => 'nullable|string|max:500',
    ];

    // -------------------- CREATE MODAL --------------------
    public function createSupplier()
    {
        $this->resetForm();
        $this->dispatch('open-modal', modal: 'createSupplierModal');
    }

    // -------------------- CREATE --------------------
    public function save()
    {
        $this->validate();

        ProductSupplier::create([
            'name' => $this->name,
            'businessname' => $this->businessname,
            'contact' => $this->contact,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->resetForm();

        $this->dispatch('close-modal', modal: 'createSupplierModal');
        $this->dispatch('swal', title: 'Success!', text: 'Supplier added successfully.', icon: 'success');
    }

    // -------------------- EDIT --------------------
    public function edit($id)
    {
        $supplier = ProductSupplier::findOrFail($id);
        // dd($supplier->name);
        $this->supplierId = $supplier->id;
        $this->name = $supplier->name;
        $this->businessname = $supplier->businessname;
        $this->contact = $supplier->contact;
        $this->address = $supplier->address;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->status = $supplier->status;
        $this->notes = $supplier->notes;
        // dd($this->supplierId);
        // Force Livewire to update the DOM and then open modal
        $this->js('
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById("editSupplierModal"));
                modal.show();
            }, 100);
        ');
    }



    // -------------------- UPDATE --------------------
    public function updateSupplier()
    {
        $this->validate();

        if (!$this->supplierId) {
            $this->dispatch('swal', title: 'Error', text: 'No supplier selected.', icon: 'error');
            return;
        }

        $supplier = ProductSupplier::findOrFail($this->supplierId);

        $supplier->update([
            'name' => $this->name,
            'businessname' => $this->businessname,
            'contact' => $this->contact,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->resetForm();
        $this->dispatch('close-modal', modal: 'editSupplierModal');
        $this->dispatch('swal', title: 'Updated!', text: 'Supplier updated successfully.', icon: 'success');
    }

    // -------------------- CONFIRM DELETE --------------------
    public function confirmDelete($id)
    {
        $this->dispatch('swal-delete', id: $id);
    }

    // -------------------- DELETE --------------------
    public function delete($id)
    {
        $supplier = ProductSupplier::find($id);

        if ($supplier) {
            $supplier->delete();
            $this->dispatch('swal', title: 'Deleted!', text: 'Supplier has been deleted.', icon: 'success');
        } else {
            $this->dispatch('swal', title: 'Error', text: 'Supplier not found.', icon: 'error');
        }
    }

    public function resetForm()
    {
        $this->reset(['supplierId', 'name', 'businessname', 'contact', 'address', 'email', 'phone', 'status', 'notes']);
        $this->status = 'active';
    }

    public function render()
    {
        $suppliers = ProductSupplier::latest()->paginate(10);
        return view('livewire.admin.supplier-manage', compact('suppliers'));
    }
}
