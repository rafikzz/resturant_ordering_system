<?php

use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CheckoutController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemSalesReportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
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
    Route::get('categories/changeStatus', [CategoryController::class, 'changeStatus'])->name('category.changeStatus');
    Route::resource('categories', CategoryController::class);

    //Item route
    Route::get('get-item-data', [ItemController::class, 'getData'])->name('items.getData');
    Route::delete('items/force-delete/{user}', [ItemController::class, 'forceDelete'])->name('items.forceDelete');
    Route::get('items/restore/{user}', [ItemController::class, 'restore'])->name('items.restore');
    Route::post('items/updateOrder', [ItemController::class, 'updateOrder'])->name('item.updateOrder');
    Route::get('items/changeStatus', [ItemController::class, 'changeStatus'])->name('item.changeStatus');
    Route::get('items/get-category-items-data', [ItemController::class, 'getCategoryItemsData'])->name('item.getCategoryItemsData');
    Route::resource('items', ItemController::class);

    //Status route
    Route::get('statuses', [StatusController::class, 'index'])->name('statuses.index');
    Route::put('statuses{status}', [StatusController::class, 'update'])->name('statuses.update');
    Route::get('statuses/{status}/edit', [StatusController::class, 'edit'])->name('statuses.edit');
    Route::get('get-status-data', [StatusController::class, 'getData'])->name('statuses.getData');

    //Order route
    Route::resource('orders', OrderController::class);
    Route::get('orders/add/{order}', [OrderController::class,'addMoreItem'])->name('orders.addItem');
    Route::put('orders/add/{order}', [OrderController::class,'updateMoreItem'])->name('orders.addItem.update');
    Route::get('get-order-data', [OrderController::class, 'getData'])->name('orders.getData');
    Route::get('get-order-detail', [OrderController::class, 'getOrderDetail'])->name('orders.getOrderDetail');

    //OrderItem Route
    Route::get('order_items/',[ OrderItemController::class,'index'])->name('order_items.index');
    Route::put('order_items/{order_item}',[ OrderItemController::class,'update'])->name('order_items.update');
    Route::delete('order_items/{order_item}',[ OrderItemController::class,'destory'])->name('order_items.delete');

    //Checkout Route
    Route::get('orders/checkout/{id}',[CheckoutController::class,'index'])->name('orders.checkout');
    Route::post('orders/checkout/{id}',[CheckoutController::class,'store'])->name('orders.checkout.store');


    //Cart route
    Route::get('get-cart-items', [CartController::class, 'getCartItems'])->name('cart.getCartItems');
    Route::get('add-cart-item', [CartController::class, 'addCartItem'])->name('cart.addCartItem');
    Route::get('remove-cart-item', [CartController::class, 'removeCartItem'])->name('cart.removeCartItem');
    Route::get('edit-cart-item-quantity', [CartController::class, 'editCartItemQuantity'])->name('cart.editCartItemQuantity');

    //Customer Controller
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('get-customer-data', [CustomerController::class, 'getData'])->name('customer.getData');

    //Setting Controller
    Route::get('/settings',[SettingController::class,'create'])->name('settings.create');
    Route::post('/settings',[SettingController::class,'store'])->name('settings.store');



    //Report Controller
    Route::get('reports/sales',[ReportController::class,'index'])->name('reports.sales.index');
    Route::get('get-sales-report-data', [ReportController::class, 'getSalesData'])->name('reports.salesData');
    Route::get('export-sales-report-data', [ReportController::class, 'exportSales'])->name('reports.exportSales');

    //ItemSalesReport Controller
    Route::get('reports/item-sales',[ItemSalesReportController::class,'index'])->name('reports.item_sales.index');
    Route::get('get-item-sales-report-data', [ItemSalesReportController::class, 'getItemSalesData'])->name('reports.itemSalesData');
    Route::get('export-item-sales-report-data', [ItemSalesReportController::class, 'exportSales'])->name('reports.exportItemSales');




});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Invoice Controller
Route::get('/order/invoice/{order}', [App\Http\Controllers\InvoiceController::class, 'index'])->name('orders.getBill');



