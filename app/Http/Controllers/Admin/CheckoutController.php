<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderCheckoutRequest;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerWalletTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Setting;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;

class CheckoutController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:order_create', ['only' => ['index', 'store']]);
        $this->title = 'Order Checkout';
    }
    public function index($id)
    {

        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Checkout' => '#'];
        $title = $this->title;
        $processingStatus = Status::where('title', 'Processing')->first()->id;
        $order = Order::where('status_id', '=', $processingStatus)->with('customer')->findOrFail($id);
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;

        $coupons = Coupon::select('id', 'title', 'discount')->where('expiry_date', '>=', Carbon::today())->get();
        $couponsDictionary = $coupons->pluck('discount', 'id');

        $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
        $order_couponable_discount_amount = $this->getCouponableDiscountAmount($order_items);
        $order_non_couponable_discount_amount = $order->total - $order_couponable_discount_amount;
        $order_items =  $order_items->groupBy('order_no');


        return view('admin.checkout.index', compact('order', 'order_couponable_discount_amount', 'order_non_couponable_discount_amount', 'coupons', 'delivery_charge', 'couponsDictionary', 'order_items', 'title', 'breadcrumbs', 'tax', 'service_charge'));
    }

    public function store(OrderCheckoutRequest $request, $id)
    {
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $delivery_charge = isset($setting) ? $setting->getDeliveryCharge() : 0;
        DB::beginTransaction();
        try {
            $processingStatus = Status::where('title', 'Processing')->first()->id;

            $order = Order::where('status_id', '=', $processingStatus)->with('status:id,title')->with('customer')->findOrFail($id);

            if ($request->discount > $order->total) {
                if ($request->ajax) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Discount is greater than total',
                    ]);
                }
                return back()->with('success', 'Discount is greater than total');
            }
            $completedStatus = Status::where('title', 'Completed')->first()->id;
            $coupon_amount = 0;
            $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
            $coupon_discoutable_amount = $this->getCouponableDiscountAmount($order_items);
            if ($request->coupon_id) {
                $coupon = Coupon::where('id', $request->coupon_id)->where('expiry_date', '>=', Carbon::today())->first();
                $coupon_amount = ($coupon) ? $coupon->discount : 0;
                if ($coupon_amount >= $coupon_discoutable_amount) {
                    $coupon_amount = $coupon_discoutable_amount;
                }
            }
            $total = $order->total;
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
                'discount' => $total_discount ?: 0,
                'service_charge' => $service_charge,
                'tax' => $tax_amount,
                'status_id' => $completedStatus,
                'payment_type_id' => $request->payment_type_id,
                'net_total' => $grand_total,
                'updated_by' => auth()->id(),
                'coupon_id' => $request->coupon_id,
                'delivery_charge' => $delivery_charge_amount,
                'is_delivery' =>  $request->is_delivery,
                'is_credit' => $request->payment_type


            ]);

            if ($request->payment_type == 1 && $order->customer->customer_type_id == 2) {
                $dueAmount = round(($grand_total - $request->paid_amount), 2);

                if ($dueAmount != 0) {
                    $this->store_customer_wallet_transacion($order, $dueAmount, $request->paid_amount);
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        DB::commit();

        // $orderItems = OrderItem::where('order_id',$order->id)->where('total','>',0)->get();
        // foreach($orderItems as $item){
        //     $items[$item->id] =  (new InvoiceItem())->title($item->item->name)->pricePerUnit($item->price)->quantity($item->total)->subTotalPrice($item->price*$item->total);
        // }
        //

        if ($request->ajax()) {

            $order = Order::with('status:id,title')->with('payment_type:id,name')->with('customer')->findOrFail($id);
            $orderItems = OrderItem::with('item:id,name')->where('order_id', $order->id)->where('total', '>', 0)->get();
            $customer= Customer::with('staff.department')->with('patient')->with('customer_type')->find($order->customer_id);
            $billRoute = route('orders.getBill', $order->id);
            if ($order) {
                return response()->json([
                    'customer'=>$customer,
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


        return redirect()->route('admin.orders.index')->with('success', 'Order Checked Out Successfully');
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
}
