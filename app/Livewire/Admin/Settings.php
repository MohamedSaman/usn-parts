<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use App\Models\User;
use App\Models\StaffPermission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\WithDynamicLayout;

#[Title("System Settings")]
class Settings extends Component
{
    use WithDynamicLayout;

    public $settings = [];
    public $key;
    public $value;
    public $showModal = false;
    public $isEdit = false;
    public $editingId = null;
    public $deleteId = null;

    // Staff Permission Management
    public $staffMembers = [];
    public $selectedStaffId = null;
    public $selectedStaffName = '';
    public $staffPermissions = [];
    public $showPermissionModal = false;
    public $availablePermissions = [];
    public $permissionCategories = [];

    protected $listeners = ['deleteConfirmed' => 'deleteConfiguration'];

    protected $rules = [
        'key' => 'required|string|max:255|unique:settings,key',
        'value' => 'required|string|max:255',
    ];

    protected $messages = [
        'key.required' => 'The configuration key is required.',
        'key.unique' => 'This configuration key already exists. Please use a different key.',
        'key.max' => 'The configuration key cannot exceed 255 characters.',
        'value.required' => 'The configuration value is required.',
        'value.max' => 'The configuration value cannot exceed 255 characters.',
    ];

    public function mount()
    {
        $this->loadSettings();
        $this->loadStaffMembers();
        $this->availablePermissions = StaffPermission::availablePermissions();
        $this->permissionCategories = StaffPermission::permissionCategories();
    }

    public function loadStaffMembers()
    {
        $this->staffMembers = User::where('role', 'staff')->get();
    }

    public function loadSettings()
    {
        $this->settings = Setting::orderBy('created_at', 'desc')->get();
    }

    public function resetForm()
    {
        $this->reset(['key', 'value', 'editingId', 'isEdit', 'deleteId']);
        $this->resetErrorBag();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEdit = false;
    }

    public function openEditModal($id)
    {
        $setting = Setting::findOrFail($id);
        $this->editingId = $id;
        $this->key = $setting->key;
        $this->value = $setting->value;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveConfiguration()
    {
        try {
            $this->validate();

            Setting::create([
                'key' => $this->key,
                'value' => $this->value,
                'date' => now(),
            ]);

            $this->closeModal();
            $this->loadSettings();

            $this->js("Swal.fire('Success!', 'Configuration has been added successfully.', 'success')");
        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Unable to add configuration. Please try again.', 'error')");
        }
    }

    public function updateConfiguration()
    {
        try {
            $this->validate([
                'key' => 'required|string|max:255|unique:settings,key,' . $this->editingId,
                'value' => 'required|string|max:255',
            ]);

            $setting = Setting::findOrFail($this->editingId);
            $setting->update([
                'key' => $this->key,
                'value' => $this->value,
            ]);

            $this->closeModal();
            $this->loadSettings();

            $this->js("Swal.fire('Success!', 'Configuration has been updated successfully.', 'success')");
        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Unable to update configuration. Please try again.', 'error')");
        }
    }

    public function confirmDelete($id = null)
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm-delete', ['id' => $id]);
    }

    public function deleteConfiguration($id = null)
    {
        try {
            $deleteId = $id ?? $this->deleteId;
            
            if (!$deleteId) {
                throw new \Exception('No configuration selected for deletion.');
            }

            $setting = Setting::findOrFail($deleteId);
            $setting->delete();
            $this->loadSettings();

            $this->deleteId = null;

            $this->js("Swal.fire('Success!', 'Configuration has been deleted successfully.', 'success')");
        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Unable to delete configuration. Please try again.', 'error')");
        }
    }

    // Staff Permission Management Methods
    public function openPermissionModal($staffId)
    {
        $staff = User::findOrFail($staffId);
        $this->selectedStaffId = $staffId;
        $this->selectedStaffName = $staff->name;
        
        // Load current permissions for this staff
        $this->staffPermissions = StaffPermission::getUserPermissions($staffId);
        
        $this->showPermissionModal = true;
    }

    public function closePermissionModal()
    {
        $this->showPermissionModal = false;
        $this->selectedStaffId = null;
        $this->selectedStaffName = '';
        $this->staffPermissions = [];
    }

    public function togglePermission($permissionKey)
    {
        if (in_array($permissionKey, $this->staffPermissions)) {
            // Remove permission
            $this->staffPermissions = array_diff($this->staffPermissions, [$permissionKey]);
        } else {
            // Add permission
            $this->staffPermissions[] = $permissionKey;
        }
    }

    public function savePermissions()
    {
        try {
            if (!$this->selectedStaffId) {
                throw new \Exception('No staff member selected.');
            }

            StaffPermission::syncPermissions($this->selectedStaffId, $this->staffPermissions);

            $this->closePermissionModal();
            $this->loadStaffMembers();

            $this->js("Swal.fire('Success!', 'Staff permissions have been updated successfully.', 'success')");
        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Unable to update permissions. Please try again.', 'error')");
        }
    }

    public function selectAllPermissions()
    {
        $this->staffPermissions = array_keys($this->availablePermissions);
    }

    public function clearAllPermissions()
    {
        $this->staffPermissions = [];
    }

    public function render()
    {
        return view('livewire.admin.settings')->layout($this->layout);
    }
}