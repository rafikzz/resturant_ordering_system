<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
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
        $seller = new Party([
            'name'          =>  isset($setting)? $setting->company_name:'Seller',
            'custom_fields' => [
                'contact_no' => '9898932232',
            ],
        ]);

        $customer = new Buyer([
            'name'          => $order->customer->name,
            'custom_fields' => [
                'contact_no' => $order->customer->phone_no,
            ],
        ]);
        $serviceCharge =$order->service_charge?:0.00;
        $totalAmount =$order->net_total?:$order->total;


        $invoice = Bill::make('receipt')
            ->filename('Order_' . $order->bill_no)
            ->sequence($order->bill_no)
            ->series('B')
            ->seller($seller)
            ->buyer($customer)
            ->serviceCharge($serviceCharge)
            ->totalTaxes($order->tax?:0)
            ->totalDiscount($order->discount)
            ->totalAmount( $totalAmount)
            ->addItems($items)->template('default');
            // dd($invoice);
        // return view('vendor.invoices.templates.default',compact('invoice'));

        return $invoice->stream();
    }
}
