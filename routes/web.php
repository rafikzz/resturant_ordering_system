<?php

use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CheckoutController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerStatementController;
use App\Http\Controllers\Admin\CustomerWalletTransactionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemSalesReportController;
use App\Http\Controllers\Admin\KOTController;
use App\Http\Controllers\Admin\OrderBreakDownController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\PatientDischargePaymentRecordController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return redirect()->route('dashboard');
});

Route::get('/dashboard',  [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::post('/dashboard/getSalesData',  [DashboardController::class, 'getSalesChartData'])->middleware('auth')->name('admin.dashboard.getSalesData');


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    //User route
    Route::resource('users', UserController::class);
    Route::get('get-user-data', [UserController::class, 'getData'])->name('users.getData');
    Route::delete('users/force-delete/{user}', [UserController::class, 'forceDelete'])->name('users.forceDelete');
    Route::get('users/restore/{user}', [UserController::class, 'restore'])->name('users.restore');
    //Role route
    Route::resource('roles', RoleController::class);
    Route::get('get-role-data', [RoleController::class, 'getData'])->name('roles.getData');

    //Category route
    Route::get('get-category-data', [CategoryController::class, 'getData'])->name('categories.getData');
    Route::delete('categories/force-delete/{user}', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');
    Route::get('categories/restore/{user}', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::post('categories/updateOrder', [CategoryController::class, 'updateOrder'])->name('category.updateOrder');
    Route::get('categories/change/status', [CategoryController::class, 'changeStatus'])->name('category.changeStatus');
    Route::resource('categories', CategoryController::class);

    //Coupon route
    Route::resource('coupons', CouponController::class);


    //Item route
    Route::get('get-item-data', [ItemController::class, 'getData'])->name('items.getData');
    Route::delete('items/force-delete/{user}', [ItemController::class, 'forceDelete'])->name('items.forceDelete');
    Route::get('items/restore/{user}', [ItemController::class, 'restore'])->name('items.restore');
    Route::get('items/change/status', [ItemController::class, 'changeStatus'])->name('item.changeStatus');
    Route::get('items/get-category-items-data', [ItemController::class, 'getCategoryItemsData'])->name('item.getCategoryItemsData');
    Route::resource('items', ItemController::class);

    //Status route
    Route::get('statuses', [StatusController::class, 'index'])->name('statuses.index');
    Route::put('statuses{status}', [StatusController::class, 'update'])->name('statuses.update');
    Route::get('statuses/{status}/edit', [StatusController::class, 'edit'])->name('statuses.edit');
    Route::get('get-status-data', [StatusController::class, 'getData'])->name('statuses.getData');

    //Order route
    Route::resource('orders', OrderController::class);
    Route::get('orders/add/{order}', [OrderController::class, 'addMoreItem'])->name('orders.addItem');
    Route::get('orders/{order}/edit-checkout', [OrderController::class, 'edit_checkout'])->name('orders.editCheckout');
    Route::patch('orders/{order}/update-checkout', [OrderController::class, 'update_checkout'])->name('orders.updateCheckout');


    Route::put('orders/add/{order}', [OrderController::class, 'updateMoreItem'])->name('orders.addItem.update');
    Route::get('get-order-data', [OrderController::class, 'getData'])->name('orders.getData');
    Route::get('get-order-detail', [OrderController::class, 'getOrderDetail'])->name('orders.getOrderDetail');

    //Order Breakdown controller
    // Route::get('orders/breakdown/{order}',[OrderBreakDownController::class,'index'])->name('orders.breakdown.index');
    Route::get('orders/breakdown/{order}', [OrderBreakDownController::class, 'test'])->name('orders.breakdown.index');
    Route::post('orders/breakdown/{order}/test', [OrderBreakDownController::class, 'store_test'])->name('orders.breakdown.store.test');
    // Route::post('orders/breakdown/{order}',[OrderBreakDownController::class,'store'])->name('orders.breakdown.store');

    //OrderItem Route
    Route::get('order_items/', [OrderItemController::class, 'index'])->name('order_items.index');
    Route::put('order_items/{order_item}', [OrderItemController::class, 'update'])->name('order_items.update');
    Route::delete('order_items/{order_item}', [OrderItemController::class, 'destory'])->name('order_items.delete');

    //Checkout Route
    Route::get('orders/checkout/{id}', [CheckoutController::class, 'index'])->name('orders.checkout');
    Route::post('orders/checkout/{id}', [CheckoutController::class, 'store'])->name('orders.checkout.store');


    //Cart route
    Route::get('get-cart-items', [CartController::class, 'getCartItems'])->name('cart.getCartItems');
    Route::get('add-cart-item', [CartController::class, 'addCartItem'])->name('cart.addCartItem');
    Route::get('remove-cart-item', [CartController::class, 'removeCartItem'])->name('cart.removeCartItem');
    Route::get('clear-cart-item', [CartController::class, 'clearCart'])->name('cart.clearCartItem');
    Route::get('edit-cart-item-quantity', [CartController::class, 'editCartItemQuantity'])->name('cart.editCartItemQuantity');

    //Customer Controller
    Route::resource('customers', CustomerController::class);
    Route::get('get-customer-data', [CustomerController::class, 'getData'])->name('customer.getData');
    Route::get('get-order-type-data', [CustomerController::class, 'getType'])->name('customer.getType');


    //Staff Controller
    Route::resource('staffs', StaffController::class);
    Route::get('get-staff-data', [StaffController::class, 'getData'])->name('staff.getData');
    Route::get('staffs/change/status', [StaffController::class, 'changeStatus'])->name('staff.changeStatus');


    Route::get('staffs/{id}/walllet-transaction', [StaffController::class, 'wallet_transaction'])->name('staffs.wallet_transaction');
    Route::post('staffs/{id}/wallet-transaction', [StaffController::class, 'store_wallet_transaction'])->name('staffs.wallet_transactions.store');

    //Patient Controller
    Route::resource('patients', PatientController::class);
    Route::post('patients/{id}/discharge', [PatientController::class,'discharge'])->name('patients.discharge');
    Route::get('patients/{id}/discharge', [PatientController::class,'discharge_show'])->name('patients.discharge');
    Route::get('patients/{id}/export', [PatientController::class,'export'])->name('patients.export');
    Route::get('patients/export/export-order-items', [PatientController::class,'exportOrderItems'])->name('patients.exportOrderItems');


    Route::get('get-patient-order-item-data', [PatientController::class, 'getOrderItemData'])->name('patient.getOrderItemData');

    Route::get('get-patient-data', [PatientController::class, 'getData'])->name('patient.getData');

    //Department Controller
    Route::resource('departments',DepartmentController::class);

    //Customer Wallet Transacton
    // Route::get('customers/{customer}/wallet-transaction', [CustomerWalletTransactionController::class, 'index'])->name('customers.wallet_transactions.index');
    // Route::get('customers/{customer}/wallet-transaction/create', [CustomerWalletTransactionController::class, 'create'])->name('customers.wallet_transactions.create');
    // Route::post('customers/{customer}/wallet-transaction', [CustomerWalletTransactionController::class, 'store'])->name('customers.wallet_transactions.store');

    Route::get('get-customer-wallet-transaction-data', [CustomerWalletTransactionController::class, 'getData'])->name('customers.wallet_transactions.getData');



    //Setting Controller
    Route::get('/settings', [SettingController::class, 'create'])->name('settings.create');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');



    //Report Controller
    Route::get('reports/sales', [ReportController::class, 'index'])->name('reports.sales.index');
    Route::get('get-sales-report-data', [ReportController::class, 'getSalesData'])->name('reports.salesData');
    Route::post('export-sales-report-data', [ReportController::class, 'exportSales'])->name('reports.exportSales');

    //ItemSalesReport Controller
    Route::get('reports/item-sales', [ItemSalesReportController::class, 'index'])->name('reports.item_sales.index');
    Route::get('get-item-sales-report-data', [ItemSalesReportController::class, 'getItemSalesData'])->name('reports.itemSalesData');
    Route::post('export-item-sales-report-data', [ItemSalesReportController::class, 'exportSales'])->name('reports.exportItemSales');

    //Customer Statement Controller
    Route::get('reports/customer-statement', [CustomerStatementController::class, 'index'])->name('reports.customer_statement');
    //KOT Controller
    Route::get('kot', [KOTController::class, 'index'])->name('kot.index');
    Route::post('complete-order-item', [KOTController::class, 'completeOrderItem'])->name('kot.completeOrderItem');
    Route::get('get-order-data-for-kot', [KOTController::class, 'getData'])->name('kot.getData');
    Route::get('get-order-detail-for-kot', [KOTController::class, 'getOrderDetail'])->name('kot.getOrderDetail');

    //Payment Type Controller
    Route::get('payment-types', [PaymentTypeController::class, 'index'])->name('payment_types.index');
    Route::get('payment-types/changeStatus', [PaymentTypeController::class, 'changeStatus'])->name('payment_types.changeStatus');
    //Patient Discharge Payment Record Controller
    Route::get('/patient-discharge-payments', [PatientDischargePaymentRecordController::class, 'index'])->name('patient_discharge_payments.index');
    Route::get('/get-payment-discharge-payments-data', [PatientDischargePaymentRecordController::class, 'getData'])->name('patient_discharge_payment.getData');

});

Auth::routes(['register' => false]);

Route::get('/home', function(){
    return redirect()->route('dashboard');
})->name('home');
//Invoice Controller
Route::get('/order/invoice/{order}', [App\Http\Controllers\InvoiceController::class, 'index'])->name('orders.getBill');
