<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductDetail;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\BrandList;
use App\Models\CategoryList;
use App\Models\ProductSupplier;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title("Product List")]
#[Layout('components.layouts.admin')]
class Products extends Component
{
    use WithPagination;

    public $search = '';

    // Create form fields
    public $code, $name, $model, $brand, $category, $image, $description, $barcode, $status, $supplier;
    public $supplier_price, $selling_price, $discount_price, $available_stock, $damage_stock;

    // Edit form fields
    public $editId, $editCode, $editName, $editModel, $editBrand, $editCategory, $editImage, $existingImage,
        $editDescription, $editBarcode, $editStatus, $editSupplierPrice, $editSellingPrice,
        $editDiscountPrice, $editDamageStock;

    public function render()
    {
        $brands = BrandList::orderBy('brand_name')->get();
        $categories = CategoryList::orderBy('category_name')->get();
        $suppliers = ProductSupplier::orderBy('name')->get();

        $products = ProductDetail::join('product_prices', 'product_details.id', '=', 'product_prices.product_id')
            ->join('product_stocks', 'product_details.id', '=', 'product_stocks.product_id')
            ->join('brand_lists', 'product_details.brand_id', '=', 'brand_lists.id')
            ->join('category_lists', 'product_details.category_id', '=', 'category_lists.id')
            ->select(
                'product_details.id',
                'product_details.code',
                'product_details.name as product_name',
                'product_details.model',
                'product_details.image',
                'product_details.description',
                'product_details.barcode',
                'product_details.status',
                'product_prices.supplier_price',
                'product_prices.selling_price',
                'product_prices.discount_price',
                'product_stocks.available_stock',
                'product_stocks.damage_stock',
                'product_stocks.total_stock',
                'brand_lists.brand_name as brand',
                'category_lists.category_name as category'
            )
            ->where(function ($query) {
                $query->where('product_details.name', 'like', '%' . $this->search . '%')
                    ->orWhere('product_details.code', 'like', '%' . $this->search . '%')
                    ->orWhere('product_details.model', 'like', '%' . $this->search . '%')
                    ->orWhere('brand_lists.brand_name', 'like', '%' . $this->search . '%')
                    ->orWhere('product_details.status', 'like', '%' . $this->search . '%')
                    ->orWhere('product_details.barcode', 'like', '%' . $this->search . '%');
            })
            ->orderBy('product_details.created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.productes', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    // 🔹 Open Create Modal
    public function openCreateModal()
    {
        $this->resetForm();
        $this->js("$('#createProductModal').modal('show')");
    }

    // 🔹 Validate Create Form
    private function validateCreateProduct()
    {
        return $this->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'required|exists:brand_lists,id',
            'category' => 'required|exists:category_lists,id',
            'supplier' => 'nullable|exists:product_suppliers,id',
            'image' => 'nullable',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255',
            'supplier_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'available_stock' => 'nullable|integer|min:0',
        ]);
    }

    // 🔹 Create Product
    public function createProduct()
    {
        $this->validateCreateProduct();

        

        $product = ProductDetail::create([
            'code' => $this->code,
            'name' => $this->name,
            'model' => $this->model,
            'image' => $this->image,
            'description' => $this->description,
            'barcode' => $this->barcode,
            'status' => 'active',
            'brand_id' => $this->brand,
            'category_id' => $this->category,
        ]);

        ProductPrice::create([
            'product_id' => $product->id,
            'supplier_price' => $this->supplier_price,
            'selling_price' => $this->selling_price,
            'discount_price' => $this->discount_price,
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'available_stock' => $this->available_stock ?? 0,
            'damage_stock' => $this->damage_stock ?? 0,
            'total_stock' => ($this->available_stock ?? 0) + ($this->damage_stock ?? 0),
            'sold_count' => 0,
            'restocked_quantity' => 0,
        ]);

        $this->resetForm();
        $this->js("$('#createProductModal').modal('hide')");
        $this->js("Swal.fire('Success!', 'Product created successfully!', 'success')");
    }

    // 🔹 Reset form fields
    private function resetForm()
    {
        $this->reset([
            'code',
            'name',
            'model',
            'brand',
            'category',
            'image',
            'description',
            'barcode',
            'status',
            'supplier',
            'supplier_price',
            'selling_price',
            'discount_price',
            'available_stock',
            'damage_stock'
        ]);
        $this->resetValidation();
    }

    // 🔹 Edit Product
    public function editProduct($id)
    {
        $product = ProductDetail::with(['price', 'stock'])->findOrFail($id);

        $this->editId = $product->id;
        $this->editCode = $product->code;
        $this->editName = $product->name;
        $this->editModel = $product->model;
        $this->editBrand = $product->brand_id;
        $this->editCategory = $product->category_id;
        $this->existingImage = $product->image;
        $this->editDescription = $product->description;
        $this->editBarcode = $product->barcode;
        $this->editStatus = $product->status;
        $this->editSupplierPrice = $product->price->supplier_price ?? 0;
        $this->editSellingPrice = $product->price->selling_price ?? 0;
        $this->editDiscountPrice = $product->price->discount_price ?? 0;
        $this->editDamageStock = $product->stock->damage_stock ?? 0;

        // Use proper modal opening with small delay to ensure data is loaded
        $this->js("
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                modal.show();
            }, 100);
        ");
    }

    // 🔹 Update Product
    public function updateProduct()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editModel' => 'nullable|string|max:255',
            'editBrand' => 'required|exists:brand_lists,id',
            'editCategory' => 'required|exists:category_lists,id',
            'editDescription' => 'nullable|string',
            'editBarcode' => 'nullable|string|max:255',
            'editStatus' => 'required|string|max:50',
            'editImage' => 'nullable',
        ]);

        $product = ProductDetail::findOrFail($this->editId);

        $product->update([
            'code' => $this->editCode,
            'name' => $this->editName,
            'model' => $this->editModel,
            'brand_id' => $this->editBrand,
            'category_id' => $this->editCategory,
            'image' => $this->editImage,
            'description' => $this->editDescription,
            'barcode' => $this->editBarcode,
            'status' => $this->editStatus,
        ]);

        $product->price()->updateOrCreate([], [
            'supplier_price' => $this->editSupplierPrice,
            'selling_price' => $this->editSellingPrice,
            'discount_price' => $this->editDiscountPrice,
        ]);

        $product->stock()->updateOrCreate([], [
            'damage_stock' => $this->editDamageStock,
        ]);

        $this->js("$('#editProductModal').modal('hide')");
        $this->js("Swal.fire('Success!', 'Product updated successfully!', 'success')");
    }

    // 🔹 Confirm Delete Product
    public function confirmDeleteProduct($id)
    {
        $this->js("
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    \$wire.deleteProduct($id);
                }
            });
        ");
    }

    // 🔹 Delete Product
    public function deleteProduct($id)
    {
        $product = ProductDetail::findOrFail($id);

        // Delete related records first
        ProductPrice::where('product_id', $id)->delete();
        ProductStock::where('product_id', $id)->delete();

        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete the product
        $product->delete();

        $this->js("Swal.fire('Success!', 'Product deleted successfully!', 'success')");
    }

    // 🔹 View Product Details
    public $viewProduct;

    public function viewProductDetails($id)
    {
        $this->viewProduct = ProductDetail::with(['price', 'stock'])
            ->join('brand_lists', 'product_details.brand_id', '=', 'brand_lists.id')
            ->join('category_lists', 'product_details.category_id', '=', 'category_lists.id')
            ->select(
                'product_details.*',
                'brand_lists.brand_name as brand',
                'category_lists.category_name as category'
            )
            ->where('product_details.id', $id)
            ->first();

        $this->js("$('#viewProductModal').modal('show')");
    }

    // 🔹 Duplicate Product
    public function duplicateProduct($id)
    {
        $product = ProductDetail::with(['price', 'stock'])->findOrFail($id);

        $newProduct = ProductDetail::create([
            'code' => $product->code.'copy',
            'name' => $product->name . ' Copy',
            'model' => $product->model,
            'image' => $product->image,
            'description' => $product->description,
            'barcode' => $product->barcode,
            'status' => 'inactive',
            'brand_id' => $product->brand_id,
            'category_id' => $product->category_id,
        ]);

        if ($product->price) {
            ProductPrice::create([
                'product_id' => $newProduct->id,
                'supplier_price' => $product->price->supplier_price,
                'selling_price' => $product->price->selling_price,
                'discount_price' => $product->price->discount_price,
            ]);
        }

        if ($product->stock) {
            ProductStock::create([
                'product_id' => $newProduct->id,
                'available_stock' => 0,
                'damage_stock' => 0,
                'total_stock' => 0,
                'sold_count' => 0,
                'restocked_quantity' => 0,
            ]);
        }

        $this->js("$('#editProductModal').modal('hide')");
        $this->js("Swal.fire('Success!', 'Product duplicated successfully!', 'success')");
    }

    
}
