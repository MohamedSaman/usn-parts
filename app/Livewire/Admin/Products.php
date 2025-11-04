<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProductDetail;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\BrandList;
use App\Models\CategoryList;
use App\Models\ProductSupplier;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Quotation;
use App\Models\ReturnsProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Imports\ProductsImport;
use App\Exports\ProductsTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

#[Title("Product List")]
#[Layout('components.layouts.admin')]
class Products extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    // Create form fields
    public $code, $name, $model, $brand, $category, $image, $description, $barcode, $status, $supplier;
    public $supplier_price, $selling_price, $discount_price, $available_stock, $damage_stock;

    // Import file
    public $importFile;

    // Edit form fields
    public $editId, $editCode, $editName, $editModel, $editBrand, $editCategory, $editImage, $existingImage,
        $editDescription, $editBarcode, $editStatus, $editSupplierPrice, $editSellingPrice,
        $editDiscountPrice, $editDamageStock;

    // Stock Adjustment fields
    public $adjustmentProductId, $adjustmentProductName, $adjustmentAvailableStock, $adjustmentDamageStock,
        $adjustmentQuantity;

    // View Product
    public $viewProduct;

    // History fields
    public $historyProductId, $historyProductName, $historyTab = 'sales';
    public $salesHistory = [], $purchasesHistory = [], $returnsHistory = [], $quotationsHistory = [];

    // Default IDs for brand, category, and supplier
    public $defaultBrandId, $defaultCategoryId, $defaultSupplierId;

    public function mount()
    {
        $this->setDefaultIds();
        $this->setDefaultValues();
    }

    /**
     * Set default IDs for brand, category, and supplier
     */
    private function setDefaultIds()
    {
        // Get or create default brand
        $defaultBrand = BrandList::where('brand_name', 'Default Brand')->first();
        if (!$defaultBrand) {
            $defaultBrand = BrandList::create([
                'brand_name' => 'Default Brand',
                'status' => 'active'
            ]);
        }
        $this->defaultBrandId = $defaultBrand->id;

        // Get or create default category
        $defaultCategory = CategoryList::where('category_name', 'Default Category')->first();
        if (!$defaultCategory) {
            $defaultCategory = CategoryList::create([
                'category_name' => 'Default Category',
                'status' => 'active'
            ]);
        }
        $this->defaultCategoryId = $defaultCategory->id;

        // Get or create default supplier
        $defaultSupplier = ProductSupplier::where('name', 'Default Supplier')->first();
        if (!$defaultSupplier) {
            $defaultSupplier = ProductSupplier::create([
                'name' => 'Default Supplier',
                'phone' => '0000000000',
                'email' => 'default@supplier.com',
                'address' => 'Default Address',
                'status' => 'active'
            ]);
        }
        $this->defaultSupplierId = $defaultSupplier->id;
    }

    /**
     * Set default values for brand, category, and supplier
     */
    private function setDefaultValues()
    {
        // Set default brand
        $this->brand = $this->defaultBrandId;

        // Set default category
        $this->category = $this->defaultCategoryId;

        // Set default supplier
        $this->supplier = $this->defaultSupplierId;

        // Set default status
        $this->status = 'active';

        // Set default stock values
        $this->available_stock = 0;
        $this->damage_stock = 0;

        // Set default prices
        $this->supplier_price = 0;
        $this->selling_price = 0;
        $this->discount_price = 0;
    }

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

        return view('livewire.admin.Productes', [
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
            'brand' => 'required|exists:brand_lists,id',
            'category' => 'required|exists:category_lists,id',
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
            'brand.required' => 'Please select a brand.',
            'brand.exists' => 'Selected brand is invalid.',
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
        
        // Set default values (like walking customer in sales system)
        $this->setDefaultValues();
        
        $this->js("$('#createProductModal').modal('show')");
    }

    // 🔹 Create Product
    public function createProduct()
    {
        // Validate the form data
        $validatedData = $this->validate();

        try {
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
            
            $this->dispatch('refreshPage');

        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Failed to create product. Please try again.', 'error')");
        }
    }

    // 🔹 Import Products from Excel
    public function importProducts()
    {
        // Validate file
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ], [
            'importFile.required' => 'Please select an Excel file to import.',
            'importFile.mimes' => 'File must be an Excel file (xlsx, xls, or csv).',
            'importFile.max' => 'File size must not exceed 10MB.',
        ]);

        try {
            // Create import instance
            $import = new ProductsImport();
            
            // Import the file
            Excel::import($import, $this->importFile->getRealPath());

            // Get import statistics
            $successCount = $import->getSuccessCount();
            $skipCount = $import->getSkipCount();
            $failures = $import->failures();

            // Build success message
            $message = "Import completed! ";
            $message .= "✅ {$successCount} product(s) imported successfully. ";
            
            if ($skipCount > 0) {
                $message .= "⚠️ {$skipCount} product(s) skipped (duplicates or errors). ";
            }

            // Reset file input
            $this->reset(['importFile']);

            // Close modal and show success
            $this->js("$('#importProductsModal').modal('hide')");
            $this->js("Swal.fire('Import Complete!', '{$message}', 'success')");
            
            $this->dispatch('refreshPage');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessage = "Import failed due to validation errors: <br>";
            
            foreach ($failures as $failure) {
                $errorMessage .= "Row {$failure->row()}: " . implode(', ', $failure->errors()) . "<br>";
            }
            
            $this->js("Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '{$errorMessage}',
                confirmButtonText: 'OK'
            })");
        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Failed to import products: {$e->getMessage()}', 'error')");
        }
    }

    // 🔹 Download Excel Template
    public function downloadTemplate()
    {
        return Excel::download(new ProductsTemplateExport(), 'products_import_template.xlsx');
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

    // 🔹 Open Stock Adjustment Modal
    public function openStockAdjustment($id)
    {
        $product = ProductDetail::with(['stock'])->findOrFail($id);

        $this->adjustmentProductId = $product->id;
        $this->adjustmentProductName = $product->name;
        $this->adjustmentAvailableStock = $product->stock->available_stock ?? 0;
        $this->adjustmentDamageStock = $product->stock->damage_stock ?? 0;
        $this->adjustmentQuantity = 0;

        $this->resetValidation();
        $this->js("$('#stockAdjustmentModal').modal('show')");
    }

    // 🔹 Stock Adjustment Validation Rules
    protected function adjustmentRules()
    {
        return [
            'adjustmentQuantity' => 'required|integer|min:1|max:' . $this->adjustmentAvailableStock,
        ];
    }

    // 🔹 Adjust Stock (Damage Only)
    public function adjustStock()
    {
        $this->validate([
            'adjustmentQuantity' => 'required|integer|min:1|max:' . $this->adjustmentAvailableStock,
        ]);

        try {
            $product = ProductDetail::with(['stock'])->findOrFail($this->adjustmentProductId);
            $stock = $product->stock;

            if (!$stock) {
                $stock = ProductStock::create([
                    'product_id' => $product->id,
                    'available_stock' => 0,
                    'damage_stock' => 0,
                    'total_stock' => 0,
                    'sold_count' => 0,
                ]);
            }

            $currentAvailable = $stock->available_stock;
            $currentDamage = $stock->damage_stock;
            $quantity = $this->adjustmentQuantity;

            // Move stock from available to damage
            if ($currentAvailable < $quantity) {
                $this->js("Swal.fire('Error!', 'Not enough available stock to mark as damaged. Available: ' + $currentAvailable, 'error')");
                return;
            }

            $stock->available_stock = $currentAvailable - $quantity;
            $stock->damage_stock = $currentDamage + $quantity;
            $stock->total_stock = $stock->available_stock + $stock->damage_stock;
            $stock->save();

            $this->js("$('#stockAdjustmentModal').modal('hide')");
            $this->js("Swal.fire('Success!', 'Stock marked as damaged successfully!', 'success')");
            $this->dispatch('refreshPage');

        } catch (\Exception $e) {
            $this->js("Swal.fire('Error!', 'Failed to adjust stock. Please try again.', 'error')");
        }
    }

    /**
     * Open Product History Modal
     */
    public function openProductHistory($id)
    {
        $product = ProductDetail::findOrFail($id);
        
        $this->historyProductId = $product->id;
        $this->historyProductName = $product->name;
        
        // Reset all history arrays
        $this->salesHistory = [];
        $this->purchasesHistory = [];
        $this->returnsHistory = [];
        $this->quotationsHistory = [];
        
        // Set default tab and load initial data
        $this->historyTab = 'sales';
        $this->loadSalesHistory();

        // Show the modal
        $this->js("
            setTimeout(() => {
                const historyModalEl = document.getElementById('productHistoryModal');
                if (historyModalEl) {
                    const historyModal = new bootstrap.Modal(historyModalEl);
                    historyModal.show();
                }
            }, 100);
        ");
    }

    /**
     * Switch History Tab
     */
    public function switchHistoryTab($tab)
    {
        $this->historyTab = $tab;
        
        // Load data for the selected tab
        switch ($tab) {
            case 'sales':
                $this->loadSalesHistory();
                break;
            case 'purchases':
                $this->loadPurchasesHistory();
                break;
            case 'returns':
                $this->loadReturnsHistory();
                break;
            case 'quotations':
                $this->loadQuotationsHistory();
                break;
        }
    }

    /**
     * Load Sales History
     */
    private function loadSalesHistory()
    {
        try {
            $salesItems = SaleItem::with(['sale.customer', 'sale.user'])
                ->where('product_id', $this->historyProductId)
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->select(
                    'sale_items.*', 
                    'sales.invoice_number', 
                    'sales.sale_type',
                    'sales.customer_type',
                    'sales.subtotal',
                    'sales.discount_amount',
                    'sales.total_amount',
                    'sales.payment_type',
                    'sales.payment_status',
                    'sales.status as sale_status',
                    'sales.due_amount',
                    'sales.notes',
                    'sales.created_at as sale_date'
                )
                ->orderBy('sales.created_at', 'desc')
                ->get();

            $this->salesHistory = $salesItems->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'sale_type' => $sale->sale_type,
                    'customer_type' => $sale->customer_type,
                    'quantity' => $sale->quantity,
                    'unit_price' => $sale->unit_price,
                    'discount_per_unit' => $sale->discount_per_unit,
                    'total_discount' => $sale->total_discount,
                    'total' => $sale->total,
                    'payment_type' => $sale->payment_type,
                    'payment_status' => $sale->payment_status,
                    'sale_status' => $sale->sale_status,
                    'due_amount' => $sale->due_amount,
                    'notes' => $sale->notes,
                    'sale_date' => $sale->sale_date,
                    'customer_name' => $sale->sale->customer->name ?? 'Walk-in Customer',
                    'customer_phone' => $sale->sale->customer->phone ?? 'N/A',
                    'user_name' => $sale->sale->user->name ?? 'N/A'
                ];
            })->toArray();

        } catch (\Exception $e) {
            $this->salesHistory = [];
        }
    }

    /**
     * Load Purchases History
     */
    private function loadPurchasesHistory()
    {
        try {
            $purchaseItems = PurchaseOrderItem::with(['order.supplier'])
                ->where('product_id', $this->historyProductId)
                ->join('purchase_orders', 'purchase_order_items.order_id', '=', 'purchase_orders.id')
                ->select(
                    'purchase_order_items.*', 
                    'purchase_orders.order_code', 
                    'purchase_orders.order_date', 
                    'purchase_orders.received_date',
                    'purchase_orders.status as order_status',
                    'purchase_orders.total_amount',
                    'purchase_orders.due_amount'
                )
                ->orderBy('purchase_orders.order_date', 'desc')
                ->get();

            $this->purchasesHistory = $purchaseItems->map(function($purchase) {
                return [
                    'id' => $purchase->id,
                    'order_code' => $purchase->order_code,
                    'order_date' => $purchase->order_date,
                    'received_date' => $purchase->received_date,
                    'quantity' => $purchase->quantity,
                    'unit_price' => $purchase->unit_price,
                    'total' => $purchase->quantity * $purchase->unit_price,
                    'order_status' => $purchase->order_status,
                    'total_amount' => $purchase->total_amount,
                    'due_amount' => $purchase->due_amount,
                    'supplier_name' => $purchase->order->supplier->name ?? 'N/A',
                    'supplier_phone' => $purchase->order->supplier->phone ?? 'N/A'
                ];
            })->toArray();

        } catch (\Exception $e) {
            $this->purchasesHistory = [];
        }
    }

    /**
     * Load Returns History
     */
    private function loadReturnsHistory()
    {
        try {
            $returns = ReturnsProduct::with(['sale.customer'])
                ->where('product_id', $this->historyProductId)
                ->join('sales', 'returns_products.sale_id', '=', 'sales.id')
                ->select(
                    'returns_products.*',
                    'sales.invoice_number'
                )
                ->orderBy('returns_products.created_at', 'desc')
                ->get();

            $this->returnsHistory = $returns->map(function($return) {
                return [
                    'id' => $return->id,
                    'invoice_number' => $return->invoice_number,
                    'return_quantity' => $return->return_quantity,
                    'selling_price' => $return->selling_price,
                    'total_amount' => $return->total_amount,
                    'notes' => $return->notes,
                    'return_date' => $return->created_at,
                    'customer_name' => $return->sale->customer->name ?? 'N/A',
                    'customer_phone' => $return->sale->customer->phone ?? 'N/A'
                ];
            })->toArray();

        } catch (\Exception $e) {
            $this->returnsHistory = [];
        }
    }

    /**
     * Load Quotations History
     */
    private function loadQuotationsHistory()
    {
        try {
            $quotations = Quotation::where('status', '!=', 'draft')
                ->orderBy('quotation_date', 'desc')
                ->get();

            $this->quotationsHistory = [];
            
            foreach ($quotations as $quotation) {
                $items = $quotation->items ?? [];
                foreach ($items as $item) {
                    if (isset($item['product_id']) && $item['product_id'] == $this->historyProductId) {
                        $this->quotationsHistory[] = [
                            'quotation_number' => $quotation->quotation_number,
                            'reference_number' => $quotation->reference_number,
                            'customer_name' => $quotation->customer_name,
                            'customer_phone' => $quotation->customer_phone,
                            'customer_email' => $quotation->customer_email,
                            'customer_address' => $quotation->customer_address,
                            'quotation_date' => $quotation->quotation_date,
                            'valid_until' => $quotation->valid_until,
                            'subtotal' => $quotation->subtotal,
                            'discount_amount' => $quotation->discount_amount,
                            'tax_amount' => $quotation->tax_amount,
                            'shipping_charges' => $quotation->shipping_charges,
                            'total_amount' => $quotation->total_amount,
                            'status' => $quotation->status,
                            'terms_conditions' => $quotation->terms_conditions,
                            'notes' => $quotation->notes,
                            'quantity' => $item['quantity'] ?? 0,
                            'unit_price' => $item['unit_price'] ?? 0,
                            'discount' => $item['discount'] ?? 0,
                            'total' => $item['total'] ?? 0,
                            'product_name' => $item['product_name'] ?? 'N/A',
                            'product_code' => $item['product_code'] ?? 'N/A',
                            'created_by_name' => $quotation->createdBy->name ?? 'N/A'
                        ];
                    }
                }
            }

        } catch (\Exception $e) {
            $this->quotationsHistory = [];
        }
    }

    // 🔹 Real-time validation for specific fields
    public function updated($propertyName)
    {
        // Only validate specific fields in real-time to improve performance
        if (in_array($propertyName, [
            'name', 'code', 'brand', 'category', 'supplier_price', 'selling_price', 
            'available_stock', 'editName', 'editCode', 'editBrand', 'editCategory',
            'editSupplierPrice', 'editSellingPrice', 'adjustmentQuantity'
        ])) {
            $this->validateOnly($propertyName);
        }

        // Real-time validation for stock adjustment quantity
        if ($propertyName === 'adjustmentQuantity' && $this->adjustmentQuantity) {
            if ($this->adjustmentQuantity > $this->adjustmentAvailableStock) {
                $this->addError('adjustmentQuantity', "Quantity cannot exceed available stock ({$this->adjustmentAvailableStock})");
            }
        }
    }
}