<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerWalletTransaction;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
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
        $categories = Category::with('active_items')->whereHas('active_items')->orderBy('order')->get();
        $customers = Customer::where('is_staff', null)->orderBy('name')->get();

        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;

        return view('admin.orders.create', compact('title', 'categories', 'coupons', 'couponsDictionary', 'customers', 'tax', 'service_charge', 'breadcrumbs'));
    }
    public function test_create()
    {
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Create' => '#'];
        $title = $this->title;
        Cart::clear();
        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');
        $categories = Category::with('active_items')->whereHas('active_items')->orderBy('order')->get();
        $customers = Customer::where('is_staff', null)->orderBy('name')->get();

        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;

        return view('admin.orders.test_create', compact('title', 'categories', 'coupons', 'couponsDictionary', 'customers', 'tax', 'service_charge', 'breadcrumbs'));
    }

    public function store(StoreOrderRequest $request)
    {
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;

        DB::beginTransaction();
        try {

            if ($request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                $customer =  Customer::create([
                    'name' => $request->customer_name,
                    'phone_no' => $request->customer_phone_no,
                    'is_staff' => $request->customer_type,
                ]);
                $customerId = $customer->id;
            }
            $billNo = time();
            $total = Cart::getTotal();

            $coupon_amount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
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
            $grand_total = $net_total + $service_charge_amount + $tax_amount;


            $completedStatus = Status::where('title', 'Completed')->first()->id;

            $order = Order::create([

                'bill_no' => $billNo,
                'destination_no' => $request->destination_no,
                'destination' => ucfirst($request->destination),
                'customer_id' => $customerId,
                'total' =>  $total,
                'service_charge' =>  $service_charge_amount,
                'tax' =>  $tax_amount,
                'net_total' =>  $grand_total,
                'discount' => $total_discount,
                'status_id' => $request->checkout ? $completedStatus : 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'order_datetime' => Carbon::now(),
            ]);

            if (isset($request->customer_type) && $request->payment_type == 1 && $request->checkout == 1) {
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

        return redirect()->route('admin.orders.index')->with('success', 'Order Created Successfully');
    }

    public function edit($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Edit' => '#'];
        $processingStatus = Status::where('title', 'processing')->first()->id;



        $breadcrumbs = ['Order' => route('admin.orders.index'), 'AddItem' => '#'];
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->findOrFail($id);

        $customer = Customer::where('id', $order->customer_id)->first();

        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');
        $categories = Category::with('active_items')->whereHas('active_items')->orderBy('order')->get();
        $customers = Customer::where('is_staff', $customer->is_staff)->where('status', 1)->orderBy('name')->get();

        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $orderItems = OrderItem::where('order_id', $order->id)->where('total', '>', 0)->get()->groupBy('order_no');


        return view('admin.orders.edit', compact(
            'title',
            'orderItems',
            'setting',
            'tax',
            'service_charge',
            'order',
            'customer',
            'coupons',
            'couponsDictionary',
            'categories',
            'customers',
            'breadcrumbs'
        ));
    }


    public function update(UpdateOrderRequest $request, $id)
    {
        $processingStatus = Status::where('title', 'processing')->first()->id;

        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        DB::beginTransaction();
        try {
            if ($request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                $customer =  Customer::create([
                    'name' => $request->customer_name,
                    'phone_no' => $request->customer_phone_no,
                    'is_staff' => $request->customer_type,

                ]);
                $customerId = $customer->id;
            }

            $cartItems = Cart::getContent();
            $this->storeOrderItem($order, $cartItems);
            $total = $order->total + Cart::getTotal();

            $coupon_amount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
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
            $grand_total = $net_total + $service_charge_amount + $tax_amount;


            $completedStatus = Status::where('title', 'Completed')->first()->id;


            $order->update([
                'customer_id' =>  $customerId,
                'destination_no' => $request->destination_no,
                'destination' => ucfirst($request->destination),
                'total' =>  $total,
                'service_charge' =>  $service_charge_amount,
                'tax' =>  $tax_amount,
                'net_total' =>  $grand_total,
                'discount' => $total_discount,
                'status_id' => $request->checkout ? $completedStatus : 1,
                'updated_by' => auth()->id(),
            ]);

            if (isset($request->customer_type) && $request->payment_type == 1 && $request->checkout == 1) {
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
        $order->forceDelete();
        return redirect()->route('admin.orders.index')->with('success', 'Order Deleted Successfully');
    }

    public function addMoreItem($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'AddItem' => '#'];
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->findOrFail($id);

        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');
        $categories = Category::with('active_items')->whereHas('active_items')->orderBy('order')->get();
        $customers = Customer::where('is_staff', null)->where('status', 1)->orderBy('name')->get();

        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        Cart::clear();
        $orderItems = OrderItem::where('order_id', $order->id)->where('total', '>', 0)->get()->groupBy('order_no');


        // $categories = Category::all();
        // $customers = Customer::all();

        return view('admin.orders.addItem', compact(
            'title',
            'coupons',
            'couponsDictionary',
            'service_charge',
            'tax',
            'order',
            'categories',
            'customers',
            'breadcrumbs',
            'orderItems'
        ));
    }

    public function updateMoreItem(Request $request, $id)
    {
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $customer = Customer::where('id', $order->customer_id)->first();
        DB::beginTransaction();
        try {

            $cartItems = Cart::getContent();
            $this->storeOrderItem($order, $cartItems);

            $orderItems = OrderItem::where('order_id', $order->id)->where('total', '>', 0)->get();
            foreach ($orderItems as $item) {
                Cart::add(array(
                    'id' => $item->item_id, // uinique row ID
                    'name' => $item->item->name,
                    'price' => $item->price,
                    'quantity' => $item->total
                ));
            }
            $total = Cart::getTotal();
            $coupon_amount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
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
            $grand_total = $net_total + $service_charge_amount + $tax_amount;



            $completedStatus = Status::where('title', 'Completed')->first()->id;
            $order->update([
                'total' =>  $total,
                'service_charge' =>  $service_charge_amount,
                'tax' =>  $tax_amount,
                'net_total' =>  $grand_total,
                'discount' => $total_discount,
                'status_id' => $request->checkout ? $completedStatus : 1,
                'updated_by' => auth()->id(),
            ]);
            if (isset($customer->is_staff) && $request->payment_type == 1 && $request->checkout == 1) {
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


            return DataTables::of($data)
                ->editColumn('destination', function ($order) {
                    return [
                        'display' => $order->destination.' '.$order->destination_no,
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
                    function ($row, Request $request) use ($processingStatus, $canEdit, $canDelete, $canAdd, $canCreate) {
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
                        } else {
                            if (auth()->user()->can('order_list')) {

                                $detail = '<button rel="' . $row->id . '"  class="btn btn-primary btn-xs get-detail my-2"><i class="fa fa-eye"></i></button>';
                                return  $detail;
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
            $order = Order::with('status:id,title')->with('payment_type:id,name')->with('customer:id,name,phone_no')->where('id', $request->order_id)->first();
            $orderItems = OrderItem::with('item:id,name')->where('order_id', $order->id)->where('total', '>', 0)->get();
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
        $customer = Customer::where('id', $order->customer_id)->whereNotNull('is_staff')->first();
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
}
