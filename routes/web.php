<?php

use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\ProductApiController;
use Illuminate\Http\Request;
use App\Livewire\CustomLogin;
use App\Livewire\Admin\Products;
use App\Livewire\Staff\Billing;
use App\Livewire\Admin\MadeByList;
use App\Livewire\Admin\ProductTypes;
use App\Livewire\Admin\BillingPage;
use App\Livewire\Admin\ManageAdmin;
use App\Livewire\Admin\ManageStaff;
use App\Livewire\Staff\DuePayments;
use App\Livewire\Admin\SupplierList;
use App\Livewire\Admin\ViewPayments;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\AddProductColor;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\ManageCustomer;
use App\Livewire\Admin\ProductBrandlist;
use App\Livewire\Staff\StaffDashboard;
use App\Livewire\Admin\StaffDueDetails;
use App\Livewire\Admin\PaymentApprovals;
use App\Livewire\Admin\StaffSaleDetails;
use App\Livewire\Admin\StaffStockDetails;
use App\Livewire\Admin\ProductCategorylist;
use App\Livewire\Admin\ProductStockDetails;
use App\Livewire\Admin\ProductDialColorlist;
use App\Livewire\Admin\ProductGlassTypeList;
use App\Livewire\Admin\ProductStrapMaterial;
use App\Livewire\Staff\StaffStockOverview;
use App\Http\Controllers\ReceiptController;
use App\Livewire\Admin\CustomerSaleDetails;
use App\Livewire\Admin\ProductStrapColorlist;
use App\Livewire\Staff\CustomerSaleManagement;
use App\Livewire\Admin\StoreBilling;
use App\Livewire\Admin\DuePayments as AdminDuePayments;
use App\Livewire\Admin\StaffStockDetails as StaffStockDetailsExport;
use App\Livewire\Staff\StoreBilling as StaffStoreBilling;
use App\Http\Controllers\ProductsExportController;
use App\Http\Controllers\StaffSaleExportController;
use App\Livewire\Admin\GRN;
use App\Livewire\Admin\StaffAttendance;
use App\Livewire\Admin\StaffSallary;
use App\Livewire\Admin\LoanManage;
use App\Livewire\Admin\Quotation;
use App\Livewire\Admin\SalesApproval;
use App\Livewire\Admin\SupplierManage;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\Analytics;
use App\Livewire\Admin\QuotationSystem;
use App\Livewire\Admin\QuotationList;
use App\Livewire\Admin\SalesSystem;
use App\Livewire\Admin\SalesList;
use App\Livewire\Admin\PosSales;
use App\Livewire\Admin\PurchaseOrderList;
use App\Models\Setting as ModelsSetting;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Expenses;
use App\Livewire\Admin\Income;
use App\Livewire\Admin\ReturnList;
use App\Livewire\Admin\ReturnProduct;
use App\Livewire\Admin\AddCustomerReceipt;
use App\Livewire\Admin\AddSupplierReceipt;
use App\Livewire\Admin\ChequeList;
use App\Livewire\Admin\ListCustomerReceipt;
use App\Livewire\Admin\ListSupplierReceipt;
use App\Livewire\Admin\ReturnCheque;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', CustomLogin::class)->name('welcome')->middleware('guest');

// Custom logout route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Routes that require authentication
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    
    // Settings route - accessible to all authenticated users
    
    // API route for products (client-side caching)
    Route::get('/api/products/all', [ProductApiController::class, 'getAllProducts'])->name('api.products.all');

    // !! Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/Product-list', Products::class)->name('Productes');
        Route::get('/add-Product-brand', ProductBrandlist::class)->name('Product-brand');
        Route::get('/Product-category', ProductCategorylist::class)->name('Product-category');
        Route::get('/billing-page', BillingPage::class)->name('billing-page');
        Route::get('/manage-admin', ManageAdmin::class)->name('manage-admin');
        Route::get('/manage-staff', ManageStaff::class)->name('manage-staff');
        Route::get('/manage-customer', ManageCustomer::class)->name('manage-customer');
        Route::get('/Product-stock-details', ProductStockDetails::class)->name('Product-stock-details');
        Route::get('/staff-stock-details', StaffStockDetails::class)->name('staff-stock-details');
        
        
        Route::get('/staff-due-details', StaffDueDetails::class)->name('staff-due-details');
        Route::get('/customer-sale-details', CustomerSaleDetails::class)->name('customer-sale-details');
       
        Route::get('/view-payments', ViewPayments::class)->name('view-payments');
        Route::get('/admin/staff/{staffId}/reentry', \App\Livewire\Admin\StockReentry::class)->name('staff.reentry');
        Route::get('/store-billing', StoreBilling::class)->name('store-billing');
        Route::get('/due-payments', AdminDuePayments::class)->name('due-payments');
        Route::get('/staff-attendance', StaffAttendance::class)->name('staff-attendance');
        Route::get('/staff-salary', StaffSallary::class)->name('staff-salary');
        Route::get('/loan-management', LoanManage::class)->name('loan-management');
        Route::get('/sales-system', SalesSystem::class)->name('sales-system');
        Route::get('/pos-sales', PosSales::class)->name('pos-sales');

        Route::get('/supplier-management', SupplierManage::class)->name('supplier-management');
        Route::get('/quotation', Quotation::class)->name('quotation');
        Route::get('/goods-receive-note', GRN::class)->name('grn');
        Route::get('/expenses', Expenses::class)->name('expenses');
        Route::get('/income', Income::class)->name('income');

        Route::get('/systemsetting', Settings::class)->name('systemsetting');
        Route::get('/reports', Reports::class)->name('reports');
        Route::get('/analytics', Analytics::class)->name('analytics');
        Route::get('/quotation-system', QuotationSystem::class)->name('quotation-system');
        Route::get('/quotation-list', QuotationList::class)->name('quotation-list');
        Route::get('/sales-list', SalesList::class)->name('sales-list');
        Route::get('/settings', Settings::class)->name('settings');
        Route::get('/return-product', ReturnProduct::class)->name('return-product');
        Route::get('/purchase-order-list', PurchaseOrderList::class)->name('purchase-order-list');
        Route::get('/return-list', ReturnList::class)->name('return-list');
        Route::get('/add-customer-receipt', AddCustomerReceipt::class)->name('add-customer-receipt');
        Route::get('/cheque-list', ChequeList::class)->name('cheque-list');
        Route::get('/return-cheque', ReturnCheque::class)->name('return-cheque');
        Route::get('/list-customer-receipt', ListCustomerReceipt::class)->name('list-customer-receipt');
        Route::get('/add-supplier-receipt', AddSupplierReceipt::class)->name('add-supplier-receipt');
        Route::get('/list-supplier-receipt', ListSupplierReceipt::class)->name('list-supplier-receipt');



    });
    Route::post('/admin/update-cash', [CashController::class, 'updateCashInHand'])
    ->name('admin.updateCashInHand')
    ->middleware(['auth', 'role:admin']);

    //!! Staff routes
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', StaffDashboard::class)->name('dashboard');
        Route::get('/billing', Billing::class)->name('billing');
        Route::get('/customer-sale-management', CustomerSaleManagement::class)->name('customer-sale-management');
        Route::get('/staff-stock-overview', StaffStockOverview::class)->name('staff-stock-overview');
        Route::get('/due-payments', DuePayments::class)->name('due-payments');
    });

    // !! Export routes (accessible to authenticated users)
    Route::get('/Productes/export', [ProductsExportController::class, 'export'])->name('Productes.export');
    Route::get('/staff-sales/export', [StaffSaleExportController::class, 'export'])->name('staff-sales.export');
    
    // Receipt download (accessible to authenticated users)
    Route::get('/receipts/{id}/download', [ReceiptController::class, 'download'])->name('receipts.download');

    // Export staff stock details
    Route::get('/export/staff-stock', function() {
        return app(StaffStockDetails::class)->exportToCSV();
    })->name('export.staff-stock');
});