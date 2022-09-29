<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderBreakDownController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:order_list|order_create|order_edit|order_delete', ['only' => ['index', 'show', 'getData']]);
        $this->middleware('permission:order_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:order_edit', ['only' => ['edit', 'update',]]);
        $this->middleware('permission:order_delete', ['only' => ['destroy', 'restore', 'forceDelete']]);
        $this->title = 'Order Management';
    }
    public function index($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Edit' => '#'];

        $processingStatus = Status::where('title', 'processing')->first()->id;

        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $orderItems = OrderItem::select('item_id', DB::raw('sum(quantity) as total_quantity'))->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->orderBy('total_quantity')->get();
        $customers = Customer::where('id', '!=', $order->customer_id)->get();

        return view('admin.orders.break_down', compact('order', 'orderItems', 'breadcrumbs', 'title', 'customers', 'title'));
    }

    public function store(Request $request, $id)
    {
        DB::beginTransaction();
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $orderItems = OrderItem::select('item_id', DB::raw('sum(quantity) as total_quantity'))->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->orderBy('total_quantity')->get();
        try {
            $totalQuantity = 0;//To check if there is any change in quantity
            foreach ($request->qunatity as $item_id => $quantity) {
                $totalQuantity += $quantity;
                if ($quantity > 0) {
                    foreach ($orderItems as  $item) {
                        if ($item_id == $item->item_id) {
                            $item->total_quantity -= $quantity;
                        }
                    }
                }
            }
            if ($totalQuantity == 0) {
                return redirect()->back()->with('error', 'No item to Breakdown');
            }
            foreach($order->order_items as $order_item)
            {
                $order_item->delete();
            }

            foreach ($orderItems as  $item) {
                if ($item->total_quantity > 0) {
                    $this->storeOrderItem($order->id, $item->item_id,$item->total_quantity,$item->item->price);
                }
            }
            $orderTotal= $order->setTotal();
            $newOrder=Order::where('bill_no',$order->bill_no)->where('customer_id',$request->customer_id)->first();;
            if($newOrder)
            {
                $newOrder->update([
                    'bill_no' => $order->bill_no,
                    'customer_id' => $request->customer_id,
                    'is_take_away' => $order->is_take_away,
                    'table_no' => $order->table_no,
                    'total' =>  0,
                    'status_id' => 1,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),

                ]);
                $orderNo = $newOrder->getOrderNo();
            }else{
                $newOrder = Order::create([
                    'bill_no' => $order->bill_no,
                    'customer_id' => $request->customer_id,
                    'is_take_away' => $order->is_take_away,
                    'table_no' => $order->table_no,
                    'total' =>  0,
                    'status_id' => 1,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'order_datetime' => $order->order_datetime,

                ]);
                $orderNo=1;
            }


            foreach ($request->qunatity as $item_id => $quantity) {
                if ($quantity > 0) {
                    $item = Item::findOrFail($item_id);
                    $this->storeOrderItem( $newOrder->id, $item_id,$quantity,$item->price,$orderNo);
                }
            }
            $newOrder->setTotal();

            if($orderTotal ==0)
            {
                $order->forceDelete();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();
        if(isset($request->new))
        {
            return redirect()->route('admin.orders.index')->with('success','Order BreakDown Successful');
        }
        else
        {
            return redirect()->back()->with('success','Order BreakDown Successful');

        }
    }

    public function storeOrderItem($order_id, $item_id,$quantity,$price,$orderNo=1)
    {
        OrderItem::create([
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'order_no' => $orderNo,
            'item_id' => $item_id,
            'price' => $price,
            'quantity' => $quantity,
            'total' => $quantity,
            'order_id' => $order_id,
        ]);
    }
}
