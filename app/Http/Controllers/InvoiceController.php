<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
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
        foreach($orderItems as $item){
            $items[$item->id] =  (new InvoiceItem())->title($item->item->name)->pricePerUnit($item->price)->quantity($item->total)->subTotalPrice($item->price*$item->total);
        }
        $seller = new Party([
            'name'          =>'Seller',
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

        $invoice = Invoice::make('receipt')
            ->filename('Order_' . $order->bill_no)
            ->sequence($order->bill_no)
            ->series('B')
            ->seller($seller)
            ->buyer($customer)
            ->totalDiscount($order->discount)
            ->totalAmount($order->net_total)
            ->addItems($items)->template('default');


        return $invoice->stream();
    }
}
