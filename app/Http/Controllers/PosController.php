<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Department;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Create' => '#'];
        $title = 'Order Management';
        \Cart::clear();
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


        return view('pos.index', compact('title', 'categories', 'guest_menu', 'code_no', 'delivery_charge', 'customer_types', 'coupons', 'couponsDictionary', 'default_customer_type_id', 'customers', 'tax', 'service_charge', 'breadcrumbs', 'departments'));
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
        $latestOrder = Order::withTrashed()->select('id')->orderBy('created_at', 'desc')->first();

        if ($latestOrder instanceof  Order) {
            $orderNo = $latestOrder->id + 1;
        } else {
            $orderNo = 1;
        }
        $order_no = str_pad($orderNo, 5, 0, STR_PAD_LEFT);
        return $order_no;
    }

    public function getCartDiscoutableAmount()
    {
        $discountable_amount = 0;
        $items = \Cart::getContent();
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
