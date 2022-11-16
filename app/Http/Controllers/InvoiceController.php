<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Facades\Invoice;

class InvoiceController extends Controller
{
    public function index(Order $order)
    {
        $orderItems = OrderItem::where('order_id',$order->id)->where('total','>',0)->get();
        $items =[];
        foreach($orderItems as $item){
            $items[$item->id] =  (new InvoiceItem())->title($item->item->name)->pricePerUnit($item->price)->quantity($item->total)->subTotalPrice($item->price*$item->total);
        }
        $setting =Setting::first();
        $cashier = new Party([
            'name'          =>  auth()->user()->name?:'Cashier',
        ]);
        $time =Carbon::now()->format('H:i A');
        $seller = new Party([
            'name'          =>  isset($setting)? $setting->company_name:'Seller',
            'address'       =>   isset($setting)? $setting->office_location:'XYZ Address',
        ]);

        $customer = new Buyer([
            'name'          => $order->customer->name,
            'phone'          => $order->customer->phone_no
        ]);

        $serviceCharge =$order->service_charge?:0.00;
        $totalAmount =$order->net_total?:$order->total;


        $invoice = Bill::make('receipt')
            ->filename('Order_' . $order->bill_no)
            ->sequence($order->bill_no)
            ->series('B')
            ->seller($seller)
            ->buyer($customer)
            ->time($time)
            ->cashier($cashier)
            ->serviceCharge($serviceCharge)
            ->totalTaxes($order->tax?:0)
            ->totalDiscount($order->discount)
            ->totalAmount( $totalAmount)
            ->addItems($items)->template('invoice');
        return view('vendor.invoices.templates.invoice',compact('invoice'));

        return $invoice->stream();
    }
}
