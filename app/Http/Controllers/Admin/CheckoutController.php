<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderCheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
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
        $setting=Setting::first();
        $tax = isset($setting)?$setting->getTax() : 0;
        $service_charge = isset($setting)?$setting->getServiceCharge() : 0;
        return view('admin.checkout.index',compact('order','order_items','title','breadcrumbs','tax','service_charge'));

    }

    public function store(OrderCheckoutRequest $request,$id)
    {
        // dd($request);
        DB::beginTransaction();
        try{
            $processingStatus =Status::where('title','Processing')->first()->id;
            $order = Order::where('status_id','=',$processingStatus)->with('status:id,title')->with('customer:id,name')->findOrFail($id);

            if($request->discount> $order->total){
                return back()->with('success','Discount is greater than total');
            }
            $completedStatus =Status::where('title','Completed')->first()->id;
            $service_charge=$order->serviceCharge($request->discount?:0);
            $tax_amount=$order->taxAmount($request->discount?:0);
            $netTotal=$order->totalWithTax($request->discount?:0) ;
            $order->update([
                'discount'=>$request->discount?:0,
                'service_charge'=>$service_charge,
                'tax'=>$tax_amount,
                'status_id'=>$completedStatus,
                'payment_type'=>$request->payment_type,
                'net_total'=>$netTotal,
                'updated_by'=>auth()->id(),
            ]);

        }catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        DB::commit();

        // $orderItems = OrderItem::where('order_id',$order->id)->where('total','>',0)->get();
        // foreach($orderItems as $item){
        //     $items[$item->id] =  (new InvoiceItem())->title($item->item->name)->pricePerUnit($item->price)->quantity($item->total)->subTotalPrice($item->price*$item->total);
        // }
        if($request->ajax())
        {
            $order = Order::with('status:id,title')->with('customer:id,name')->findOrFail($id);
            $orderItems = OrderItem::with('item:id,name')->where('order_id', $order->id)->where('total', '>', 0)->get();
            $billRoute = route('orders.getBill',$order->id);
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


        return redirect()->route('admin.orders.index')->with('success','Order Checked Out Successfully');


    }
}
