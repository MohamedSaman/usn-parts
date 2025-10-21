<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use Livewire\Component;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('components.layouts.admin')]
#[Title('Manage Customer')]
class ManageCustomer extends Component
{
    public $name;
    public $contactNumber;
    public $address;
    public $email;
    public $customerType;
    public $businessName;

    public $editCustomerId;
    public $editName;
    public $editContactNumber;
    public $editAddress;
    public $editEmail;
    public $editCustomerType;
    public $editBusinessName;

    public $deleteId;

    public function render()
    {
        $customers = Customer::all();
        return view('livewire.admin.manage-customer', [
            'customers' => $customers,
        ]);
    }

    /** ----------------------------
     * Create Customer
     * ---------------------------- */
    public function createCustomer()
    {
        $this->reset(['name', 'contactNumber', 'address', 'email', 'customerType', 'businessName']);
        $this->js("$('#createCustomerModal').modal('show')");
    }

    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required',
            'customerType' => 'required',
            'contactNumber' => 'required',
            'address' => 'required',
            'email' => 'email|unique:customers,email',
            'businessName' => 'nullable',
        ]);

        try {
            Customer::create([
                'name' => $this->name,
                'phone' => $this->contactNumber,
                'address' => $this->address,
                'email' => $this->email,
                'type' => $this->customerType,
                'business_name' => $this->businessName,
            ]);

            $this->js("Swal.fire('Success!', 'Customer Created Successfully', 'success')");
            $this->reset(['name', 'contactNumber', 'address', 'email', 'customerType', 'businessName']);
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }

        $this->js("$('#createCustomerModal').modal('hide')");
    }

    /** ----------------------------
     * Edit Customer
     * ---------------------------- */
    public function editCustomer($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            $this->js("Swal.fire('Error!', 'Customer not found.', 'error')");
            return;
        }

        $this->editCustomerId = $customer->id;
        $this->editName = $customer->name;
        $this->editContactNumber = $customer->phone;
        $this->editBusinessName = $customer->business_name;
        $this->editCustomerType = $customer->type;
        $this->editAddress = $customer->address;
        $this->editEmail = $customer->email;

        $this->dispatch('open-edit-modal');
    }

    public function updateCustomer($id)
    {
        $this->validate([
            'editName' => 'required',
            'editCustomerType' => 'required',
            'editBusinessName' => 'nullable',
            'editContactNumber' => 'required',
            'editAddress' => 'required',
            'editEmail' => 'email|unique:customers,email,' . $id,
        ]);

        try {
            $customer = Customer::find($id);
            if (!$customer) {
                $this->js("Swal.fire('Error!', 'Customer not found.', 'error')");
                return;
            }

            $customer->update([
                'name' => $this->editName,
                'phone' => $this->editContactNumber,
                'business_name' => $this->editBusinessName,
                'type' => $this->editCustomerType,
                'address' => $this->editAddress,
                'email' => $this->editEmail,
            ]);

            $this->js("Swal.fire('Success!', 'Customer Updated Successfully', 'success')");
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }

        $this->js("$('#editCustomerModal').modal('hide')");
    }

    /** ----------------------------
     * Delete Customer
     * ---------------------------- */
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-delete');
    }

    #[On('confirmDelete')]
    public function deleteCustomer()
    {
        try {
            Customer::where('id', $this->deleteId)->delete();
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }
    }
}
