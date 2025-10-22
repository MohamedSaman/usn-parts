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
            ->leftJoin('brand_lists', 'product_details.brand_id', '=', 'brand_lists.id')
            ->leftJoin('category_lists', 'product_details.category_id', '=', 'category_lists.id')
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
                    ->orWhere('category_lists.category_name', 'like', '%' . $this->search . '%')
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

    // 🔹 Validation Rules for Create
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:product_details,code',
            'model' => 'nullable|string|max:255',
            
            'supplier' => 'nullable|exists:product_suppliers,id',
            'image' => 'nullable|url',
            'description' => 'nullable|string|max:1000',
            'barcode' => 'nullable|string|max:255|unique:product_details,barcode',
            'supplier_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:supplier_price',
            'discount_price' => 'nullable|numeric|min:0|lte:selling_price',
            'available_stock' => 'required|integer|min:0',
            'damage_stock' => 'nullable|integer|min:0',
        ];
    }

    // 🔹 Validation Messages
    protected function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name must not exceed 255 characters.',
            'code.required' => 'Product code is required.',
            'code.unique' => 'This product code already exists.',
            
            'category.required' => 'Please select a category.',
            'category.exists' => 'Selected category is invalid.',
            'supplier_price.required' => 'Supplier price is required.',
            'supplier_price.numeric' => 'Supplier price must be a number.',
            'supplier_price.min' => 'Supplier price cannot be negative.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.numeric' => 'Selling price must be a number.',
            'selling_price.min' => 'Selling price cannot be negative.',
            'selling_price.gte' => 'Selling price must be greater than or equal to supplier price.',
            'discount_price.lte' => 'Discount price cannot be greater than selling price.',
            'available_stock.required' => 'Available stock is required.',
            'available_stock.integer' => 'Available stock must be a whole number.',
            'available_stock.min' => 'Available stock cannot be negative.',
            'damage_stock.integer' => 'Damage stock must be a whole number.',
            'damage_stock.min' => 'Damage stock cannot be negative.',
            'image.url' => 'Please provide a valid image URL.',
            'barcode.unique' => 'This barcode already exists.',
        ];
    }

    // 🔹 Open Create Modal
    public function openCreateModal()
    {
        $this->resetForm();
        $this->resetValidation();
        $this->js("$('#createProductModal').modal('show')");
    }

    // 🔹 Get or create default brand
    private function getDefaultBrand()
    {
        $defaultBrand = BrandList::where('brand_name', 'Default')->first();
        
        if (!$defaultBrand) {
            $defaultBrand = BrandList::create([
                'brand_name' => 'Default',
                'status' => 'active'
            ]);
        }
        
        return $defaultBrand->id;
    }

    // 🔹 Get or create default category
    private function getDefaultCategory()
    {
        $defaultCategory = CategoryList::where('category_name', 'Default')->first();
        
        if (!$defaultCategory) {
            $defaultCategory = CategoryList::create([
                'category_name' => 'Default',
                'status' => 'active'
            ]);
        }
        
        return $defaultCategory->id;
    }

    // 🔹 Create Product
    public function createProduct()
    {
        // Validate the form data
        $validatedData = $this->validate();

        try {
            // Use selected brand/category or get defaults
            $brandId = $this->brand ?: $this->getDefaultBrand();
            $categoryId = $this->category ?: $this->getDefaultCategory();

            // Generate product code if not provided
            $productCode = $this->code ?: 'PROD-' . strtoupper(Str::random(8));

            $product = ProductDetail::create([
                'code' => $productCode,
                'name' => $this->name,
                'model' => $this->model,
                'image' => $this->image,
                'description' => $this->description,
                'barcode' => $this->barcode,
                'status' => 'active',
                'brand_id' => $brandId,
                'category_id' => $categoryId,
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
            
            $this->dispatch('refreshPage');

        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Failed to create product. Please try again.', 'error')");
        }
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

        $this->resetValidation();

        $this->js("
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                modal.show();
            }, 100);
        ");
    }

    // 🔹 Validation Rules for Update
    protected function updateRules()
    {
        return [
            'editName' => 'required|string|max:255',
            'editCode' => 'required|string|max:100|unique:product_details,code,' . $this->editId,
            'editModel' => 'nullable|string|max:255',
            'editBrand' => 'required|exists:brand_lists,id',
            'editCategory' => 'required|exists:category_lists,id',
            'editImage' => 'nullable|url',
            'editDescription' => 'nullable|string|max:1000',
            'editBarcode' => 'nullable|string|max:255|unique:product_details,barcode,' . $this->editId,
            'editStatus' => 'required|in:active,inactive,discontinued',
            'editSupplierPrice' => 'required|numeric|min:0',
            'editSellingPrice' => 'required|numeric|min:0|gte:editSupplierPrice',
            'editDiscountPrice' => 'nullable|numeric|min:0|lte:editSellingPrice',
            'editDamageStock' => 'required|integer|min:0',
        ];
    }

    // 🔹 Update Product
    public function updateProduct()
    {
        // Validate the form data
        $validatedData = $this->validate($this->updateRules());

        try {
            $product = ProductDetail::findOrFail($this->editId);

            // Use selected brand/category or get defaults
            $brandId = $this->editBrand ?: $this->getDefaultBrand();
            $categoryId = $this->editCategory ?: $this->getDefaultCategory();

            $product->update([
                'code' => $this->editCode,
                'name' => $this->editName,
                'model' => $this->editModel,
                'brand_id' => $brandId,
                'category_id' => $categoryId,
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
            $this->dispatch('refreshPage');

        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Failed to update product. Please try again.', 'error')");
        }
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
        try {
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
            $this->dispatch('refreshPage');

        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Failed to delete product. Please try again.', 'error')");
        }
    }

    // 🔹 View Product Details
    public $viewProduct;

    public function viewProductDetails($id)
    {
        $this->viewProduct = ProductDetail::with(['price', 'stock'])
            ->leftJoin('brand_lists', 'product_details.brand_id', '=', 'brand_lists.id')
            ->leftJoin('category_lists', 'product_details.category_id', '=', 'category_lists.id')
            ->select(
                'product_details.*',
                'brand_lists.brand_name as brand',
                'category_lists.category_name as category'
            )
            ->where('product_details.id', $id)
            ->first();

        $this->js("$('#viewProductModal').modal('show')");
    }

    // 🔹 Real-time validation for specific fields
    public function updated($propertyName)
    {
        // Only validate specific fields in real-time to improve performance
        if (in_array($propertyName, [
            'name', 'code', 'brand', 'category', 'supplier_price', 'selling_price', 
            'available_stock', 'editName', 'editCode', 'editBrand', 'editCategory',
            'editSupplierPrice', 'editSellingPrice'
        ])) {
            $this->validateOnly($propertyName);
        }
    }
}