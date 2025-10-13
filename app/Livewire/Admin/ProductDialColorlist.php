<?php

namespace App\Livewire\Admin;
use App\Models\DialColorList;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Exception;

#[Title("Product Dial Color")]
#[Layout('components.layouts.admin')]
class ProductDialColorlist extends Component
{
    public $dialColorName;
    public $dialColorCode;
    public function render()
    {
        $dialColors = DialColorList::orderBy('id','asc')->get();
        return view('livewire.admin.Product-dial-colorlist',[
            'dialColors'=> $dialColors
        ]);
    }

    public function createDialColor(){
        $this->dialColorName = '';
        $this->dialColorCode = '';
        $this->dispatch('create-dial-color');
    }

    public function resetForm(){
        $this->reset([
            'dialColorName',
            'dialColorCode',
        ]);
    }
    public function saveDialColor(){
        $this->validate([
            'dialColorName' => 'required|unique:dial_color_lists,dial_color_name',
            'dialColorCode' => 'required|unique:dial_color_lists,dial_color_code'
        ]);
        try{
           
            DialColorList::create([
                'dial_color_name' => $this->dialColorName,
                'dial_color_code' => $this->dialColorCode,
            ]);
            
            $this->js("Swal.fire('Success!', 'Dial Color Created Successfully', 'success')");
        }catch(Exception $e){
            // log($e->getMessage());
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }
        
        $this->js('$("#createDialColorModal").modal("hide")');
        return redirect()->route('admin.Product-dial-color');
        
    }

    public $editDialColorId;
    public $editDialColorName;
    public $editDialColorCode;
    public function editDialColor($id){
        $color = DialColorList::find($id);
        $this->editDialColorName = $color->dial_color_name;
        $this->editDialColorCode = $color->dial_color_code;
        $this->editDialColorId = $color->id;
        
        // $this->js("$('#editDialColorModal').modal('show')");
        $this->dispatch('edit-dial-color');
    }

    public function updateDialColor($id){
        $this->validate([
            'editDialColorName' => 'required|unique:dial_color_lists,dial_color_name,'.$id,
            'editDialColorCode' => 'required|unique:dial_color_lists,dial_color_code,'.$id
        ]);
        try{
            
            DialColorList::where('id', $id)->update([
                'dial_color_name' => $this->editDialColorName,
                'dial_color_code' => $this->editDialColorCode,
            ]);
            $this->js('$("#editDialColorModal").modal("hide")');
            $this->js("Swal.fire('Success!', 'Color Updated Successfully', 'success')");
        }catch(Exception $e){
            // log($e->getMessage());
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }
        
        $this->js('$("#editDialColorModal").modal("hide")');
    }
    
    public $deleteId;
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-delete');
    }
    #[On('confirmDelete')]
    public function deleteDialColor(){
        try{
            DialColorList::where('id', $this->deleteId )->delete();
        }catch(Exception $e){
            // log($e->getMessage());
            $this->js("Swal.fire('Error!', '".$e->getMessage()."', 'error')");
        }
    }
}
