<?php

namespace App\Livewire\Admin;
use Exception;
use App\Models\ProductMadeBy;
use Livewire\Component;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title("Product Country List")]
#[Layout('components.layouts.admin')]
class MadeByList extends Component
{
    public $countryName;
    
    public function render()
    {
        $countries = ProductMadeBy::orderBy('id','desc')->get();
        return view('livewire.admin.made-by-list',[
            'countries'=> $countries,
        ]);
    }

    public function createCountry(){
       $this->dispatch('create-country-modal');
    }

    public function resetForm(){
       $this->reset([
            'countryName',
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function saveCountry(){
        $this->validate([
            'countryName' => 'required|unique:product_made_bies,country_name'
        ]);
        try{
            
            ProductMadeBy::create([
                'country_name' => $this->countryName,
            ]);
            $this->js("Swal.fire('Success!', 'Country Created Successfully', 'success')");
        }catch(Exception $e){
            // log($e->getMessage());
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }
        
        $this->js('$("#createCountryModal").modal("hide")');
        return redirect()->route('admin.made-by-list');
        
    }

    public $editCountryId;
    public $editCountryName;
  
    public function editCountry($id){
        $country = ProductMadeBy::find($id);
        $this->editCountryName = $country->country_name;
        $this->editCountryId = $country->id;
        
        // $this->js("$('#editCountryModal').modal('show')");
        $this->dispatch('edit-country-modal');
    }

    public function updateCountry($id){
        // dd($id);
        $this->validate([
            'editCountryName' => 'required|unique:product_made_bies,country_name,'.$id
        ]);
        try{
            ProductMadeBy::where('id', $id)->update([
                'country_name' => $this->editCountryName,
            ]);
            $this->js('$("#editCountryModal").modal("hide")');
            $this->js("Swal.fire('Success!', 'Country Updated Successfully', 'success')");
        }catch(Exception $e){
            // log($e->getMessage());
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }
        
        $this->js('$("#editCountryModal").modal("hide")');
    }
    
    public $deleteId;
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-delete');
    }
    #[On('confirmDelete')]
    public function deleteCountry(){
        try{
            ProductMadeBy::where('id', $this->deleteId )->delete();
        }catch(Exception $e){
            // log($e->getMessage());
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }
    }
}
