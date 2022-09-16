<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderCheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Status;
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
        $this->middleware('permission:order_create', ['only' => ['index','store']]);
        $this->title = 'Order Checkout';
    }
    public function index($id)
    {

        $breadcrumbs =[ 'Order'=>route('admin.orders.index'), 'Checkout'=>'#'];
        $title =$this->title;
        $processingStatus =Status::where('title','Processing')->first()->id;
        $order = Order::where('status_id','=',$processingStatus)->with('customer:id,name')->findOrFail($id);
        $order_items = OrderItem::where('order_id',$order->id)->with('item:id,name')->where('total','>',0)->get()->groupBy('order_no');
        return view('admin.checkout.index',compact('order','order_items','title','breadcrumbs'));

    }

    public function store(OrderCheckoutRequest $request,$id)
    {
        $processingStatus =Status::where('title','Processing')->first()->id;
        $order = Order::where('status_id','=',$processingStatus)->with('customer:id,name')->findOrFail($id);
        if($request->discount> $order->total){
            return back()->with('success','Discount is greater than total');
        }
        $completedStatus =Status::where('title','completed')->first()->id;

        $order->update([
            'discount'=>$request->discount?:0,
            'status_id'=>$completedStatus,
            'payment_type'=>$request->payment_type,
            'updated_by'=>auth()->id(),

        ]);
        $orderItems = OrderItem::where('order_id',$order->id)->where('total','>',0)->get();
        foreach($orderItems as $item){
            $items[$item->id] =  (new InvoiceItem())->title($item->item->name)->pricePerUnit($item->price)->quantity($item->total)->subTotalPrice($item->price*$item->total);
        }
        $billRoute = route('orders.getBill',$order->id);
        Session::flash('download.in.the.next.request',$billRoute);

        return redirect()->route('admin.orders.index')->with('success','Order Checked Out Successfully');


    }
}
