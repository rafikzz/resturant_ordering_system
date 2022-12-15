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
        $this->middleware('permission:order_create', ['only' => ['index', 'store']]);
        $this->title = 'Order Management';
    }
    public function index($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Edit' => '#'];

        $processingStatus = Status::where('title', 'processing')->first()->id;

        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $orderItems = OrderItem::select('item_id', DB::raw('sum(total) as total_quantity'))->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->orderBy('total_quantity')->get();
        $customers = Customer::where('id', '!=', $order->customer_id)->where('customer_type_id',2)->get();

        return view('admin.orders.break_down', compact('order', 'orderItems', 'breadcrumbs', 'title', 'customers', 'title'));
    }
    public function test($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Edit' => '#'];

        $processingStatus = Status::where('title', 'processing')->first()->id;

        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $orderItems = OrderItem::select('item_id', DB::raw('sum(total) as quantity'))->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->orderBy('quantity')->get();
        $customers = Customer::select('id', 'name', 'phone_no')->where('id', '!=', $order->customer_id)->where('id', '!=', $order->customer_id)->get();
        $itemQuantityDictionary = [];
        $itemDictionary = [];

        foreach ($orderItems as $item) {
            $itemQuantityDictionary[$item->item_id] = $item->quantity;
            $itemDictionary[$item->item_id] = $item->item->name;
        }
        $itemQuantityDictionary = collect($itemQuantityDictionary);
        $itemDictionary = collect($itemDictionary);

        return view('admin.orders.break_down_test', compact('order', 'orderItems', 'breadcrumbs', 'title', 'customers', 'itemDictionary', 'itemQuantityDictionary', 'title'));
    }
    public function store(Request $request, $id)
    {
        DB::beginTransaction();
        $processingStatus = Status::where('title', 'processing')->first()->id;
        $order = Order::where('status_id', $processingStatus)->findOrFail($id);
        $orderItems = OrderItem::select('item_id', DB::raw('sum(quantity) as total_quantity'))->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->orderBy('total_quantity')->get();
        try {
            $totalQuantity = 0; //To check if there is any change in quantity
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
            foreach ($order->order_items as $order_item) {
                $order_item->delete();
            }

            foreach ($orderItems as  $item) {
                if ($item->total_quantity > 0) {
                    if ($order->guest_menu == 1) {
                        $price = $item->item->guest_price;
                    } else {
                        $price = $item->item->price;
                    }
                    $this->storeOrderItem($order->id, $item->item_id, $item->total_quantity, $price);
                }
            }
            $orderTotal = $order->setTotal();
            $newOrder = Order::where('bill_no', $order->bill_no)->where('customer_id', $request->customer_id)->first();;
            if ($newOrder) {
                $newOrder->update([
                    'bill_no' => $order->bill_no,
                    'customer_id' => $request->customer_id,
                    'destination' => $order->destination,
                    'destination_no' => $order->destination_no,
                    'total' =>  0,
                    'status_id' => 1,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),

                ]);
                $orderNo = $newOrder->getOrderNo();
            } else {
                $newOrder = Order::create([
                    'bill_no' => $order->bill_no,
                    'customer_id' => $request->customer_id,
                    'destination' => $order->destination,
                    'destination_no' => $order->destination_no,
                    'total' =>  0,
                    'status_id' => 1,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'order_datetime' => $order->order_datetime,
                    'guest_menu' => $order->guest_menu,
                    'is_delivery' =>  $order->is_delivery,

                ]);
                $orderNo = 1;
            }


            foreach ($request->qunatity as $item_id => $quantity) {
                if ($quantity > 0) {
                    $item = Item::findOrFail($item_id);
                    if ($order->guest_menu == 1) {
                        $price = $item->guest_price;
                    } else {
                        $price = $item->price;
                    }
                    $this->storeOrderItem($newOrder->id, $item_id, $quantity, $item->price, $orderNo);
                }
            }
            $newOrder->setTotal();
            $order->setTotal();

            // if($orderTotal ==0)
            // {
            //     $order->forceDelete();
            // }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();
        if (isset($request->new)) {
            return redirect()->back()->with('success', 'Order BreakDown Successful');
        } else {
            return redirect()->route('admin.orders.index')->with('success', 'Order BreakDown Successful');
        }
    }
    public function store_test(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customerOrderItems = [];
            $processingStatus = Status::where('title', 'processing')->first()->id;
            $order = Order::where('status_id', $processingStatus)->findOrFail($id);
            $orderItems = OrderItem::select('item_id', DB::raw('sum(quantity) as total_quantity'))->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->orderBy('total_quantity')->get();
            foreach ($request->customer_id as $key => $customer) {

                if (!array_key_exists($customer, $customerOrderItems)) {
                    $customerOrderItems[$customer] = [];
                }
                array_push($customerOrderItems[$customer], array('item_id' => $request->item_id[$key], 'quantity' => intval($request->quantity[$key])));
            }
            $customerOrderItems = collect($customerOrderItems);

            foreach ($customerOrderItems as $key => $customerItems) {
                $newOrder = Order::where('bill_no', $order->bill_no)->where('customer_id', $key)->first();
                if ($newOrder) {
                    $newOrder->update([
                        'bill_no' => $order->bill_no,
                        'destination' => $order->destination,
                        'destination_no' => $order->destination_no,
                        'total' =>  0,
                        'status_id' => 1,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                        'order_datetime' => $order->order_datetime,
                        'guest_menu' => $order->guest_menu,
                        'is_delivery' =>  $order->is_delivery,

                    ]);
                    $orderNo = $newOrder->getOrderNo();
                } else {
                    $newOrder = Order::create([
                        'bill_no' => $order->bill_no,
                        'customer_id' => $key,
                        'destination' => $order->destination,
                        'destination_no' => $order->destination_no,
                        'total' =>  0,
                        'status_id' => 1,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                        'order_datetime' => $order->order_datetime,
                        'guest_menu' => $order->guest_menu,
                        'is_delivery' =>  $order->is_delivery,

                    ]);
                    $orderNo = 1;
                }
                foreach ($customerItems as $customerItem) {
                    foreach ($orderItems as $orderItem) {
                        if ($orderItem->item_id == $customerItem['item_id']) {
                            $item = Item::findOrFail($customerItem['item_id']);
                            $orderItem->total_quantity -= $customerItem['quantity'];
                            $newOrderItem = OrderItem::where('order_id', $newOrder->id)->where('item_id', $customerItem['item_id'])->first();
                            if ($newOrderItem) {
                                $quantity = $customerItem['quantity'] + $newOrderItem->quantity;

                                $newOrderItem->update(
                                    [
                                        'total' => $quantity,
                                        'quantity' => $quantity,
                                    ]
                                );
                            } else {
                                if ($order->guest_menu == 1) {
                                    $price = $item->guest_price;
                                } else {
                                    $price = $item->price;
                                }
                                $this->storeOrderItem($newOrder->id, $customerItem['item_id'], $customerItem['quantity'], $price, $orderNo);
                            }
                        }
                    }
                }
             $newOrder->setTotal();

            }
            foreach ($order->order_items as $order_item) {
                $order_item->delete();
            }

            foreach ($orderItems as  $item) {
                if ($item->total_quantity > 0) {
                    if ($order->guest_menu == 1) {
                        $price =  $item->item->guest_price;
                    } else {
                        $price =  $item->item->price;
                    }
                    $this->storeOrderItem($order->id, $item->item_id, $item->total_quantity,$price);
                }
            }
           $orderAmount = $order->setTotal();
           if($order->order_items->count() == 0)
           {
            $order->delete();
           }

        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();
        if (isset($request->new)) {
            return redirect()->back()->with('success', 'Order BreakDown Successful');
        } else {
            return redirect()->route('admin.orders.index')->with('success', 'Order BreakDown Successful');
        }
    }


    public function storeOrderItem($order_id, $item_id, $quantity, $price, $orderNo = 1)
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
