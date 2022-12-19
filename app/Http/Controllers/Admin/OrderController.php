<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerWalletTransaction;
use App\Models\Department;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Status;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:order_list|order_create|order_edit|order_delete', ['only' => ['index', 'show', 'getData']]);
        $this->middleware('permission:order_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:order_edit', ['only' => ['edit', 'update',]]);
        $this->middleware('permission:order_delete', ['only' => ['destroy', 'restore', 'forceDelete']]);
        $this->middleware('permission:checkout_edit', ['only' => ['edit_checkout', 'update_checkout']]);

        $this->title = 'Order Management';
    }
    public function index(Request $request)
    {

        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index')];

        return view('admin.orders.index', compact('title', 'breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Create' => '#'];
        $title = $this->title;
        Cart::clear();
        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');
        $categories = Category::with('active_items')->where('status', 1)->whereHas('active_items')->orderBy('order')->get();
        $customer_types = CustomerType::get();

        if (old('customer_type')) {
            $default_customer_type_id = old('customer_type');
        } else {
            $default_customer_type = CustomerType::where('is_default', '1')->first();

            $default_customer_type_id = $default_customer_type ? $default_customer_type->id : null;
        }
        $customers = Customer::where('customer_type_id', $default_customer_type_id)->with('patient')->with('staff')->where('status', 1)->orderBy('name')->get();
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;
        $guest_menu = 1;
        $departments = Department::orderBy('name')->get();
        $code_no = $this->getCodeNo();

        return view('admin.orders.create', compact('title', 'categories', 'guest_menu', 'code_no','delivery_charge', 'customer_types', 'coupons', 'couponsDictionary', 'default_customer_type_id', 'customers', 'tax', 'service_charge', 'breadcrumbs', 'departments'));
    }

    public function store(StoreOrderRequest $request)
    {

        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;

        if (!Cart::getContent()->count() && $request->checkout) {
            return redirect()->back()->with('error', 'No Item In Cart For Checkout')->withInput();;
        }
        DB::beginTransaction();
        try {
            if ($request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                if ($request->customer_type  == 2 || $request->customer_type == 3) {
                    $is_staff = ($request->customer_type == 3) ? 0 : 1;
                } else {
                    $is_staff = null;
                }
                $customer =  Customer::create([
                    'name' => $request->customer_name,
                    'phone_no' => $request->customer_phone_no,
                    'customer_type_id' => $request->customer_type,
                    'is_staff' => $is_staff

                ]);
                $customerId = $customer->id;
                if ($request->patient_register_no) {
                    $customer->patient()->create([
                        'register_no' => $request->patient_register_no
                    ]);
                }
                if ($request->code) {
                    Staff::create([
                        'customer_id' => $customer->id,
                        'department_id' => $request->department_id,
                        'code' => $request->code,
                    ]);
                }
            }
            $billNo = $this->getBillNo();

            $total = Cart::getTotal();
            $coupon_discoutable_amount = $this->getCartDiscoutableAmount();
            $coupon_amount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
                if ($coupon_amount >= $coupon_discoutable_amount) {
                    $coupon_amount = $coupon_discoutable_amount;
                }
            }
            $total_discount = $coupon_amount + $request->discount ?: 0;


            if ($total_discount >= $total) {
                $total_discount = $total;
                $net_total = 0;
                $service_charge_amount = 0;
                $tax_amount = 0;
            } else {
                $net_total = $total - $total_discount;
                $service_charge_amount =    round(($service_charge  / 100) * ($net_total), 2);
                $tax_amount =    round(($tax / 100) * ($net_total + $service_charge_amount), 2);
            }

            $completedStatus = Status::where('title', 'Completed')->first()->id;
            $delivery_charge_amount = 0;
            if ($request->is_delivery) {
                $delivery_charge_amount = $request->delivery_charge;
            }

            $grand_total = $net_total + $service_charge_amount + $tax_amount + $delivery_charge_amount;
            $order = Order::create([
                'bill_no' =>    $billNo,
                'destination_no' => $request->destination_no,
                'destination' => $request->destination,
                'customer_id' =>  $customerId,
                'total' =>  $total,
                'service_charge' => $request->checkout ? $service_charge_amount : null,
                'tax' =>  $request->checkout ? $tax_amount : null,
                'net_total' =>  $request->checkout ? $grand_total : null,
                'discount' => $request->checkout ? $total_discount : null,
                'is_credit' => $request->checkout ? $request->payment_type : null,
                'status_id' => $request->checkout ? $completedStatus : 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'order_datetime' => Carbon::now(),
                'coupon_id' =>  $request->checkout ? $request->coupon_id : null,
                'is_delivery' =>  $request->is_delivery,
                'delivery_charge' => $request->checkout ? $delivery_charge_amount : null,
                'guest_menu' => $request->guest_menu ?: 0,
                'note'      => $request->note

            ]);


            if ($request->customer_type == 2 && $request->payment_type == 1 && $request->checkout == 1) {
                $dueAmount = round(($grand_total - $request->paid_amount), 2);

                if ($dueAmount != 0) {
                    $this->store_customer_wallet_transacion($order, $dueAmount, $request->paid_amount);
                }
            }
            $cartItems = Cart::getContent();
            $this->storeOrderItem($order, $cartItems);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();
        Cart::clear();

        return redirect()->route('admin.orders.create')->with('success', 'Order Created Successfully');
    }

    public function edit($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Edit' => '#'];
        $processingStatus = Status::where('title', 'processing')->first()->id;
        Cart::clear();
        $departments = Department::orderBy('name')->get();
        $code_no = $this->getCodeNo();


        $breadcrumbs = ['Order' => route('admin.orders.index'), 'AddItem' => '#'];
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->with('customer')->findOrFail($id);

        $customer = Customer::where('id', $order->customer_id)->with('patient')->with('staff')->first();

        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');
        $categories = Category::with('active_items')->where('status', 1)->whereHas('active_items')->orderBy('order')->get();

        $customer_types = CustomerType::get();

        if (old('customer_type')) {
            $default_customer_type_id = old('customer_type');
        } else {
            $default_customer_type = CustomerType::where('id', $order->customer->customer_type_id)->first();
            $default_customer_type_id = $default_customer_type ? $default_customer_type->id : null;
        }
        $customers = Customer::where('customer_type_id', $default_customer_type_id)->with('patient')->with('staff')->where('status', 1)->orderBy('name')->get();


        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;


        $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
        $order_couponable_discount_amount = $this->getCouponableDiscountAmount($order_items);
        $order_non_couponable_discount_amount = $order->total - $order_couponable_discount_amount;
        $orderItems = $order_items->groupBy('order_no');

        return view('admin.orders.edit', compact(
            'order_couponable_discount_amount',
            'order_non_couponable_discount_amount',
            'title',
            'orderItems',
            'setting',
            'tax',
            'departments',
            'service_charge',
            'delivery_charge',
            'order',
            'customer',
            'customer_types',
            'coupons',
            'couponsDictionary',
            'code_no',
            'categories',
            'customers',
            'breadcrumbs'
        ));
    }

    public function edit_checkout($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Edit' => '#'];
        $processingStatus = Status::where('title', 'completed')->first()->id;
        Cart::clear();


        $breadcrumbs = ['Order' => route('admin.orders.index'), 'AddItem' => '#'];
        $completedStatus = Status::where('title', 'completed')->first()->id;
        $order = Order::where('status_id', $completedStatus)->with('customer')->findOrFail($id);

        $customer = Customer::where('id', $order->customer_id)->first();

        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');
        $categories = Category::with('active_items')->where('status', 1)->whereHas('active_items')->orderBy('order')->get();

        $customer_types = CustomerType::get();

        if (old('customer_type')) {
            $default_customer_type_id = old('customer_type');
        } else {
            $default_customer_type = CustomerType::where('id', $order->customer->customer_type_id)->first();
            $default_customer_type_id = $default_customer_type ? $default_customer_type->id : null;
        }
        $customers = Customer::where('customer_type_id', $default_customer_type_id)->with('patient')->with('staff')->where('status', 1)->orderBy('name')->get();


        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = $order->delivery_charge;


        $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
        $order_couponable_discount_amount = $this->getCouponableDiscountAmount($order_items);
        $order_non_couponable_discount_amount = $order->total - $order_couponable_discount_amount;
        $orderItems = $order_items->groupBy('order_no');
        $departments = Department::orderBy('name')->get();
        $code_no = $this->getCodeNo();


        return view('admin.orders.edit_checkout', compact(
            'order_couponable_discount_amount',
            'order_non_couponable_discount_amount',
            'title',
            'orderItems',
            'setting',
            'tax',
            'service_charge',
            'delivery_charge',
            'order',
            'customer',
            'customer_types',
            'coupons',
            'couponsDictionary',
            'categories',
            'customers',
            'departments',
            'breadcrumbs',
            'code_no'

        ));
    }
    public function update_checkout(UpdateOrderRequest $request, $id)
    {
        $completedStatus = Status::where('title', 'Completed')->first()->id;

        $order = Order::where('status_id', $completedStatus)->findOrFail($id);
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;

        if (!(Cart::getContent()->count() || $order->order_items()->count()) && $request->checkout) {
            return redirect()->back()->with('error', 'No Item In Cart For Checkout')->withInput();;
        }

        DB::beginTransaction();
        try {
            if ($request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                if ($request->customer_type  == 2 || $request->customer_type == 3) {
                    $is_staff = ($request->customer_type == 3) ? 0 : 1;
                } else {
                    $is_staff = null;
                }
                $customer =  Customer::create([
                    'name' => $request->customer_name,
                    'phone_no' => $request->customer_phone_no,
                    'customer_type_id' => $request->customer_type,
                    'is_staff' => $is_staff

                ]);
                $customerId = $customer->id;
                if ($request->patient_register_no) {
                    $customer->patient()->create([
                        'register_no' => $request->patient_register_no
                    ]);
                }
                if ($request->code) {
                    Staff::create([
                        'customer_id' => $customer->id,
                        'department_id' => $request->department_id,
                        'code' => $request->code,
                    ]);
                }
            }


            $cartItems = Cart::getContent();
            $this->storeOrderItem($order, $cartItems);
            $total = $order->total + Cart::getTotal();

            $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
            $coupon_discoutable_amount = $this->getCouponableDiscountAmount($order_items);
            $coupon_amount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
                if ($coupon_amount >= $coupon_discoutable_amount) {
                    $coupon_amount = $coupon_discoutable_amount;
                }
            }
            $total_discount = $coupon_amount + $request->discount ?: 0;
            if ($total_discount >= $total) {
                $total_discount = $total;
                $net_total = 0;
                $service_charge_amount = 0;
                $tax_amount = 0;
            } else {
                $net_total = $total - $total_discount;
                $service_charge_amount =    round(($service_charge  / 100) * ($net_total), 2);
                $tax_amount =    round(($tax / 100) * ($net_total + $service_charge_amount), 2);
            }

            $delivery_charge_amount = 0;
            if ($request->is_delivery) {
                $delivery_charge_amount = $request->delivery_charge;
            }
            $grand_total = $net_total + $service_charge_amount + $tax_amount + $delivery_charge_amount;

            $order->update([
                'customer_id' =>  $customerId,
                'destination_no' => $request->destination_no,
                'destination' => ucfirst($request->destination),
                'total' =>  $total,
                'service_charge' => $request->checkout ? $service_charge_amount : null,
                'tax' =>  $request->checkout ? $tax_amount : null,
                'net_total' =>  $request->checkout ? $grand_total : null,
                'discount' => $request->checkout ? $total_discount : null,
                'status_id' => $request->checkout ? $completedStatus : 1,
                'updated_by' => auth()->id(),
                'coupon_id' =>  $request->checkout ? $request->coupon_id : null,
                'is_delivery' =>  $request->is_delivery,
                'delivery_charge' => $request->checkout ? $delivery_charge_amount : null,
                'is_credit' => $request->checkout ? $request->payment_type : null,
                'note'      => $request->note


            ]);

            if ($request->customer_type != 1 && $request->payment_type == 1 && $request->checkout == 1) {
                $dueAmount = round(($grand_total - $request->paid_amount), 2);

                if ($dueAmount != 0) {
                    $this->store_customer_wallet_transacion($order, $dueAmount, $request->paid_amount);
                }
            }
        } catch (\Throwable $th) {
            Cart::clear();
            DB::rollback();
            throw $th;
        }
        Cart::clear();
        DB::commit();

        return redirect()->route('admin.orders.index')->with('success', 'Order Edited Successfully');
    }


    public function update(UpdateOrderRequest $request, $id)
    {
        $processingStatus = Status::where('title', 'processing')->first()->id;

        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;

        if (!(Cart::getContent()->count() || $order->order_items()->count()) && $request->checkout) {
            return redirect()->back()->with('error', 'No Item In Cart For Checkout')->withInput();;
        }

        DB::beginTransaction();
        try {
            if ($request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                if ($request->customer_type  == 2 || $request->customer_type == 3) {
                    $is_staff = ($request->customer_type == 3) ? 0 : 1;
                } else {
                    $is_staff = null;
                }
                $customer =  Customer::create([
                    'name' => $request->customer_name,
                    'phone_no' => $request->customer_phone_no,
                    'customer_type_id' => $request->customer_type,
                    'is_staff' => $is_staff

                ]);
                $customerId = $customer->id;
                if ($request->patient_register_no) {
                    $customer->patient()->create([
                        'register_no' => $request->patient_register_no
                    ]);
                }
                if ($request->code) {
                    Staff::create([
                        'customer_id' => $customerId,
                        'department_id' => $request->department_id,
                        'code' => $request->code,
                    ]);
                }
            }

            $cartItems = Cart::getContent();
            $this->storeOrderItem($order, $cartItems);
            $total = $order->total + Cart::getTotal();

            $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
            $coupon_discoutable_amount = $this->getCouponableDiscountAmount($order_items);
            $coupon_amount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
                if ($coupon_amount >= $coupon_discoutable_amount) {
                    $coupon_amount = $coupon_discoutable_amount;
                }
            }
            $total_discount = $coupon_amount + $request->discount ?: 0;
            if ($total_discount >= $total) {
                $total_discount = $total;
                $net_total = 0;
                $service_charge_amount = 0;
                $tax_amount = 0;
            } else {
                $net_total = $total - $total_discount;
                $service_charge_amount =    round(($service_charge  / 100) * ($net_total), 2);
                $tax_amount =    round(($tax / 100) * ($net_total + $service_charge_amount), 2);
            }


            $completedStatus = Status::where('title', 'Completed')->first()->id;
            $delivery_charge_amount = 0;
            if ($request->is_delivery) {
                $delivery_charge_amount = $request->delivery_charge;
            }
            $grand_total = $net_total + $service_charge_amount + $tax_amount + $delivery_charge_amount;

            $order->update([
                'customer_id' =>  $customerId,
                'destination_no' => $request->destination_no,
                'destination' => ucfirst($request->destination),
                'total' =>  $total,
                'service_charge' => $request->checkout ? $service_charge_amount : null,
                'tax' =>  $request->checkout ? $tax_amount : null,
                'net_total' =>  $request->checkout ? $grand_total : null,
                'discount' => $request->checkout ? $total_discount : null,
                'status_id' => $request->checkout ? $completedStatus : 1,
                'updated_by' => auth()->id(),
                'coupon_id' =>  $request->checkout ? $request->coupon_id : null,
                'is_delivery' =>  $request->is_delivery,
                'delivery_charge' => $request->checkout ? $delivery_charge_amount : null,
                'is_credit' => $request->checkout ? $request->payment_type : null,
                'note'      => $request->note


            ]);

            if ($request->customer_type != 1 && $request->payment_type == 1 && $request->checkout == 1) {
                $dueAmount = round(($grand_total - $request->paid_amount), 2);

                if ($dueAmount != 0) {
                    $this->store_customer_wallet_transacion($order, $dueAmount, $request->paid_amount);
                }
            }
        } catch (\Throwable $th) {
            Cart::clear();
            DB::rollback();
            throw $th;
        }
        Cart::clear();
        DB::commit();

        return redirect()->route('admin.orders.index')->with('success', 'Order Edited Successfully');
    }
    public function destroy(Order $order)
    {
        $cancelledStatus = Status::where('title', 'cancelled')->first()->id;


        $order->update([
            'status_id' => $cancelledStatus
        ]);
        return redirect()->route('admin.orders.index')->with('success', 'Order Cancelled Successfully');
    }

    public function addMoreItem($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'AddItem' => '#'];
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->findOrFail($id);

        $customer_types = CustomerType::get();


        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');
        $categories = Category::with('active_items')->where('status', 1)->whereHas('active_items')->orderBy('order')->get();

        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;
        Cart::clear();
        $guest_menu = $order->guest_menu;
        $order_couponable_discount_amount = 0;
        $order_non_couponable_discount_amount = 0;
        $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();

        $order_couponable_discount_amount = $this->getCouponableDiscountAmount($order_items);
        $order_non_couponable_discount_amount = $order->total - $order_couponable_discount_amount;
        $orderItems = $order_items->groupBy('order_no');

        return view('admin.orders.addItem', compact(
            'title',
            'order_couponable_discount_amount',
            'order_non_couponable_discount_amount',
            'coupons',
            'couponsDictionary',
            'service_charge',
            'customer_types',
            'tax',
            'delivery_charge',
            'order',
            'categories',
            'breadcrumbs',
            'orderItems',
            'guest_menu'
        ));
    }

    public function updateMoreItem(Request $request, $id)
    {
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $customer = Customer::where('id', $order->customer_id)->first();
        if (!(Cart::getContent()->count() || $order->order_items()->count()) && $request->checkout) {
            return redirect()->back()->with('error', 'No Item In Cart For Checkout')->withInput();;
        }
        DB::beginTransaction();
        try {
            $cartItems = Cart::getContent();
            $this->storeOrderItem($order, $cartItems);
            $total = $order->getTotal();
            $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
            $coupon_discoutable_amount = $this->getCouponableDiscountAmount($order_items);
            $coupon_amount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
                if ($coupon_amount >= $coupon_discoutable_amount) {
                    $coupon_amount = $coupon_discoutable_amount;
                }
            }
            $total_discount = $coupon_amount + $request->discount ?: 0;
            if ($total_discount >= $total) {
                $total_discount = $total;
                $net_total = 0;
                $service_charge_amount = 0;
                $tax_amount = 0;
            } else {
                $net_total = $total - $total_discount;
                $service_charge_amount =    round(($service_charge  / 100) * ($net_total), 2);
                $tax_amount =    round(($tax / 100) * ($net_total + $service_charge_amount), 2);
            }
            $delivery_charge_amount = 0;
            if ($request->is_delivery) {
                $delivery_charge_amount = $request->delivery_charge;
            }
            $grand_total = $net_total + $service_charge_amount + $tax_amount + $delivery_charge_amount;
            $completedStatus = Status::where('title', 'Completed')->first()->id;
            $order->update([
                'total' =>  $total,
                'service_charge' => $request->checkout ? $service_charge_amount : null,
                'tax' =>  $request->checkout ? $tax_amount : null,
                'net_total' =>  $request->checkout ? $grand_total : null,
                'discount' => $request->checkout ? $total_discount : null,
                'status_id' => $request->checkout ? $completedStatus : 1,
                'updated_by' => auth()->id(),
                'coupon_id' =>  $request->checkout ? $request->coupon_id : null,
                'is_delivery' =>  $request->is_delivery,
                'delivery_charge' => $request->checkout ? $delivery_charge_amount : null,
                'is_delivery' =>  $request->is_delivery,
                'is_credit' => $request->checkout ? $request->payment_type : null,
                'note'      => $request->note

            ]);
            if ($customer->customer_type_id != 1  && $request->payment_type == 1 && $request->checkout == 1) {
                $dueAmount = round(($grand_total - $request->paid_amount), 2);

                if ($dueAmount != 0) {
                    $this->store_customer_wallet_transacion($order, $dueAmount, $request->paid_amount);
                }
            }
        } catch (\Throwable $th) {
            Cart::clear();
            DB::rollback();
            throw $th;
        }
        DB::commit();
        Cart::clear();
        return redirect()->route('admin.orders.index')->with('success', 'Order Added Successfully');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            switch ($request->mode) {
                case ('all'):
                    $data = Order::select('table_orders.*')->with('customer:id,name')->with('status:id,title,color');
                    break;
                case ('daily'):
                    $today = Carbon::today();

                    $data = Order::select('table_orders.*')->with('customer:id,name')->with('status:id,title,color')->whereDate('order_datetime', $today);
                    break;
                case ('weekly'):
                    $startDate = Carbon::parse('last sunday')->startOfDay();
                    $endDate = Carbon::parse('next saturday')->endOfDay();

                    $data = Order::select('table_orders.*')->with('customer:id,name')->with('status:id,title,color')->whereBetween('order_datetime', [$startDate, $endDate]);
                    break;
                case ('monthly'):
                    $startDate = Carbon::now()->firstOfMonth();
                    $endDate = Carbon::parse('this month')->now();

                    $data = Order::select('table_orders.*')->with('customer:id,name')->with('status:id,title,color')->whereBetween('order_datetime', [$startDate, $endDate]);
                    break;
                case ('history'):
                    //For Customer History Page
                    $data = Order::select('table_orders.*')->with('status:id,title,color')->where('customer_id', $request->customer_id);
                    break;
                default:
                    $data = Order::select('table_orders.*')->with('customer:id,name')->with('status:id,title,color');
            }
            $processingStatus = Status::where('title', 'processing')->first()->id;
            $canEdit = auth()->user()->can('order_edit');
            $canDelete = auth()->user()->can('order_delete');
            $canAdd = auth()->user()->can('order_add');
            $canCreate = auth()->user()->can('order_create');
            $editCheckout = auth()->user()->can('checkout_edit');
            $completedStatus = Status::where('title', 'Completed')->first()->id;

            return DataTables::of($data)
                ->editColumn('destination', function ($order) {
                    return [
                        'display' => $order->destination . ' ' . $order->destination_no,
                        'order' => $order->destination
                    ];
                })
                ->editColumn('created_at', function ($order) {
                    return [
                        'display' => $order->created_at->diffForHumans(),
                        'timestamp' => $order->created_at
                    ];
                })
                ->addColumn(
                    'action',
                    function ($row, Request $request) use ($processingStatus, $completedStatus, $canEdit, $canDelete, $canAdd, $canCreate, $editCheckout) {
                        if ($row->status_id == $processingStatus && $request->mode !== 'history') {
                            if ($canEdit || $canDelete) {
                                $checkoutBtn = $canCreate ? '<a href="' . route('admin.orders.checkout', $row->id) . '"  class="btn bg-orange btn-xs"
                                data-toggle="tooltip" title="Checkout"><i class="fa  fa-cash-register"></i> Checkout</a>' : '';
                                $breakdownBtn = $canCreate ? '<a href="' . route('admin.orders.breakdown.index', $row->id) . '"  class="btn btn-secondary btn-xs"
                                data-toggle="tooltip" title="Breakdown"><i class="fa  fa-sitemap"></i> Breakdown</a>' : '';
                                $editBtn =  $canEdit ? '<a class="btn btn-xs btn-warning"  href="' . route('admin.orders.edit', $row->id) . '"><i class="fa fa-pencil-alt"></i></a>' : '';
                                $deleteBtn =  $canDelete ? '<button type="submit" class="btn btn-xs btn-danger btn-delete"><i class="fa fa-trash-alt"></i></button>' : '';
                                $formStart = '<form action="' . route('admin.orders.destroy', $row->id) . '" method="POST">
                                <input type="hidden" name="_method" value="delete">' . csrf_field();

                                $detail = '<button rel="' . $row->id . '"  class="btn btn-primary btn-xs get-detail my-2"><i class="fa fa-eye"></i></button>';
                                $addBtn = $canAdd ? '<a class="btn btn-xs btn-success"  href="' . route('admin.orders.addItem', $row->id) . '"><i class="fa fa-plus"></i> Item</a>' : '';


                                $formEnd = '</form>';
                                $btn = $formStart . ' ' . $detail . ' ' . $checkoutBtn . ' ' . $addBtn . ' ' . $breakdownBtn . ' ' . $editBtn .  ' ' . $deleteBtn .  $formEnd;

                                return $btn;
                            }
                        } else if ($row->status_id == $completedStatus && $request->mode !== 'history') {
                            if (auth()->user()->can('order_list')) {
                                $editBtn =  $editCheckout ? '<a class="btn btn-xs btn-warning"  href="' . route('admin.orders.editCheckout', $row->id) . '"><i class="fa fa-edit"></i></a>' : '';
                                $detail = '<button rel="' . $row->id . '"  class="btn btn-primary btn-xs get-detail my-2"><i class="fa fa-eye"></i></button>';
                                return  $detail . ' ' . $editBtn;
                            }
                        } else {
                            if (auth()->user()->can('order_list')) {
                                $detail = '<button rel="' . $row->id . '"  class="btn btn-primary btn-xs get-detail my-2"><i class="fa fa-eye"></i></button>';
                                return   $detail;
                            }
                        }
                    }
                )
                ->orderColumn('status', function ($query, $order) {
                    $query->orderBy('status_id', $order);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getOrderDetail(Request $request)
    {
        if (request()->ajax()) {
            $order = Order::with('status:id,title')->with('payment_type:id,name')->with('customer')->where('id', $request->order_id)->first();
            $orderItems = OrderItem::select('item_id', DB::raw('sum(total * price) as total_price'), DB::raw('sum(total * price)/sum(total) as average_price'), DB::raw('sum(total) as total_quantity'))
                ->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->get();
            $billRoute = route('orders.getBill', $order->id);

            if ($order) {
                return response()->json([
                    'order' => $order,
                    'billRoute' => $billRoute,
                    'orderItems' => $orderItems,
                    'status' => 'success',
                    'message' => 'Order fetched successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'No Order found',
                ]);
            }
        }
    }
    public function storeOrderItem($order, $cartItems, $statusId = 1)
    {
        $orderNo = $order->getOrderNo();
        foreach ($cartItems as $item) {
            OrderItem::create([
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'order_no' => $orderNo,
                'item_id' => $item->id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'total' => $item->quantity,
                'order_id' => $order->id,
            ]);
        }
    }

    public function store_customer_wallet_transacion($order, $dueAmount, $paidAmount)
    {
        $wallet_balance = isset($order->customer_id) ? $order->customer->wallet_balance() : 0;
        $current_balance = $wallet_balance - $dueAmount;
        $customer = Customer::where('id', $order->customer_id)->where('customer_type_id', '!=', 1)->first();
        if ($customer) {
            $customer->update([
                'balance' => $current_balance
            ]);
        }

        CustomerWalletTransaction::create([
            'customer_id' => $order->customer_id,
            'order_id' => $order->id,
            'previous_amount' => $wallet_balance,
            'amount' => $dueAmount,
            'total_amount' => $paidAmount,
            'current_amount' => $current_balance,
            'transaction_type_id' => 3,
            'author_id' => auth()->id(),
        ]);
    }
    public function getCouponableDiscountAmount($orderItems)
    {
        $order_couponable_discount_amount = 0;
        if (isset($orderItems)) {
            foreach ($orderItems as $order_item) {
                $coupon_discount_percentage = $order_item->item->category->coupon_discount_percentage;
                $order_couponable_discount_amount += $order_item->price * ($order_item->total * $coupon_discount_percentage) / 100;
            }
        }
        return  $order_couponable_discount_amount;
    }

    public function getBillNo()
    {
        $latestOrder = Order::select('id')->orderBy('created_at', 'desc')->first();

        if ($latestOrder instanceof  Order) {
            $orderNo = $latestOrder->id + 1;
        } else {
            $orderNo = 1;
        }

        $order_no =  $prefix.''.str_pad($orderNo, 5, 0, STR_PAD_LEFT);
        return $order_no;
    }

    public function getCartDiscoutableAmount()
    {
        $discountable_amount = 0;
        $items = Cart::getContent();
        foreach ($items as $item) {
            $discountable_amount += $item->quantity * $item->attributes->coupon_discount_percentage * $item->price / 100;
        }
        return $discountable_amount;
    }

    public function getCodeNo()
    {
        $latestStaff = Staff::select('id')->orderBy('created_at', 'desc')->first();

        if ($latestStaff instanceof  Staff) {
            $codeNo = $latestStaff->id + 1;
        } else {
            $codeNo = 1;
        }
        $code_no = str_pad($codeNo, 4, 0, STR_PAD_LEFT);
        return $code_no;
    }
}
