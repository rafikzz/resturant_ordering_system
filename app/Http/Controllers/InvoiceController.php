<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Facades\Invoice;

class InvoiceController extends Controller
{
    public function index(Order $order)
    {
        $orderItems = OrderItem::with('item')->where('order_id',$order->id)->where('total','>',0)->get();

        $orderItems = OrderItem::select('item_id', DB::raw('sum(total * price) as total_price'),DB::raw('sum(total * price)/sum(total) as average_price'),DB::raw('sum(total) as total_quantity'))
        ->with('item')->where('order_id',$order->id)->where('total','>',0)->groupBy('item_id')->get();
        $items =[];
        foreach($orderItems as $item){
            $items[$item->item_id] =  (new InvoiceItem())->title($item->item->name)->pricePerUnit($item->average_price)->quantity($item->total_quantity)->subTotalPrice($item->total_price);
        }
        $setting =Setting::first();
        $cashier = new Party([
            'name'          =>  auth()->user()->name?:'Cashier',
        ]);
        $time =Carbon::now()->format('Y/m/d H:i A');
        $seller = new Party([
            'name'          =>  isset($setting)? $setting->company_name:'Seller',
            'address'       =>   isset($setting)? $setting->office_location:'XYZ Address',
        ]);

        $customer = new Buyer([
            'name'          => $order->customer->name,
            'phone'          => $order->customer->phone_no
        ]);
        $setting=Setting::first();
        $prefix =isset($setting)?($setting->bill_no_prefix?:'B'):'B';
        $serviceCharge =$order->service_charge?:0.00;
        $deliveryCharge =$order->delivery_charge?:0.00;

        $totalAmount =$order->net_total?:$order->total;
        $orderDiscount=$order->discount?$order->discount:0;

        $invoice = Bill::make('receipt')
            ->filename('Order_' . $order->bill_no)
            ->sequence($order->bill_no)
            ->series( $prefix )
            ->seller($seller)
            ->buyer($customer)
            ->time($time)
            ->cashier($cashier)
            ->serviceCharge($serviceCharge)
            ->totalTaxes($order->tax?:0)
            ->totalDiscount($orderDiscount)
            ->deliveryCharge($deliveryCharge)
            ->totalAmount( $totalAmount)
            ->addItems($items)->template('invoice');
        // return view('vendor.invoices.templates.invoice',compact('invoice'));

        return $invoice->stream();
    }
}
