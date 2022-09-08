<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Status;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:order_list|order_create|order_edit|order_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:order_create', ['only' => ['create','store']]);
        $this->middleware('permission:order_edit', ['only' => ['edit','update',]]);
        $this->middleware('permission:order_delete', ['only' => ['destroy','restore','forceDelete']]);
        $this->title = 'Order Management';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Order'=>route('admin.orders.index')];

        return view('admin.orders.index', compact('title','breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs =[ 'Order'=>route('admin.orders.index'),'Create'=>'#'];
        $title = $this->title;
        Cart::clear();
        $categories = Category::all();
        $statuses = Status::all();
        $customers = Customer::all();

        return view('admin.orders.create', compact('title', 'categories', 'customers', 'statuses','breadcrumbs'));
    }

    public function store(StoreOrderRequest $request)
    {
        if (Cart::isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Items Orderd',
            ]);
        }

        DB::beginTransaction();
        try {
            if ($request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                $customer =  Customer::create([
                    'name' => $request->customer_name,
                    'phone_no' => $request->customer_phone_no,
                ]);
                $customerId = $customer->id;
            }
            $billNo = time();
            $order = Order::create([
                'bill_no' => $billNo,
                'table_no' => $request->table_no,
                'customer_id' => $customerId,
                'total' => Cart::getTotal(),
                'discount' => ($request->discount) ? $request->discount : 0,
                'status_id' => $request->status_id,
                'order_datetime' => now(),
            ]);
            $cartItems = Cart::getContent();
            foreach ($cartItems as $item) {
                CartItem::create([
                    'item_id' => $item->id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'order_id' => $order->id
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();
        Cart::clear();

        return redirect()->route('admin.orders.index')->with('success','Order Created Successfully');

    }

    public function edit($id)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Order'=>route('admin.orders.index'),'Edit'=>'#'];

        $order = Order::findOrFail($id);
        Cart::clear();
        $orderItems = CartItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $item) {
            Cart::add(array(
                'id' => $item->item_id, // uinique row ID
                'name' => $item->item->name,
                'price' => $item->price,
                'quantity' => $item->quantity
            ));
        }
        $categories = Category::all();
        $statuses = Status::all();
        $customers = Customer::all();

        return view('admin.orders.edit', compact('title', 'order', 'categories', 'customers', 'statuses','breadcrumbs'));
    }

    public function update(UpdateOrderRequest $request,Order $order)
    {

        if (Cart::isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Items Orderd',
            ]);
        }

        DB::beginTransaction();
        try {
            if ($request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                $customer =  Customer::create([
                    'name' => $request->customer_name,
                    'phone_no' => $request->customer_phone_no,
                ]);
                $customerId = $customer->id;
            }
            $billNo = time();
            $order->update([
                'bill_no' => $billNo,
                'table_no' => $request->table_no,
                'customer_id' => $customerId,
                'total' => Cart::getTotal(),
                'discount' => ($request->discount) ? $request->discount : 0,
                'status_id' => $request->status_id,
            ]);
            $orderItems = CartItem::where('order_id', $order->id)->get();
            foreach ($orderItems as $item) {
               $item->forceDelete();
            }
            $cartItems = Cart::getContent();
            foreach ($cartItems as $item) {
                CartItem::create([
                    'item_id' => $item->id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'order_id' => $order->id
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();
        Cart::clear();

        return redirect()->route('admin.orders.index')->with('success','Order Edited Successfully');
    }
    public function destroy(Order $order)
    {
        $order->forceDelete();
        return redirect()->route('admin.orders.index')->with('success','Order Deleted Successfully');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            switch ($request->mode) {
                case ('all'):
                    $data = Order::select('*')->with('status:id,title,color');
                    break;
                case ('daily'):
                    $today = Carbon::today();

                    $data = Order::select('*')->with('status:id,title,color')->whereDate('order_datetime', $today);
                    break;
                case ('weekly'):
                    $startDate = Carbon::parse('last sunday')->startOfDay();
                    $endDate = Carbon::parse('next saturday')->endOfDay();

                    $data = Order::select('*')->with('status:id,title,color')->whereBetween('order_datetime', [$startDate, $endDate]);
                    break;
                case ('monthly'):
                    $startDate = Carbon::now()->firstOfMonth();
                    $endDate = Carbon::parse('this month')->now();

                    $data = Order::select('*')->with('status:id,title,color')->whereBetween('order_datetime', [$startDate, $endDate]);
                    break;
                case ('history'):
                    $data = Order::select('*')->with('status:id,title,color')->where('customer_id',$request->customer_id);
                    break;
                default:
                    $data = Order::select('*')->with('status:id,title,color');
            }


            return DataTables::of($data)
                ->editColumn('created_at', function ($order) {
                    return [
                        'display' => $order->created_at->diffForHumans(),
                        'timestamp' => $order->created_at
                    ];
                })->addColumn(
                    'action',
                    function ($row, Request $request) {
                        if (auth()->user()->can('order_edit') || auth()->user()->can('order_delete')) {
                            $editBtn =  auth()->user()->can('order_edit') ? '<a class="btn btn-sm btn-warning"  href="' . route('admin.orders.edit', $row->id) . '">Edit</a>' : '';
                            $deleteBtn =  auth()->user()->can('order_delete') ? '<button type="submit" class="btn btn-sm btn-danger btn-delete">Delete</button>' : '';
                            $formStart = '<form action="' . route('admin.orders.destroy', $row->id) . '" method="POST">
                            <input type="hidden" name="_method" value="delete"><a href="'.route('orders.get',$row->id).'" target="_blank" class="btn btn-secondary btn-sm">Get Bill</a>' . csrf_field();
                            $detail = '<button rel="' . $row->id . '"  class="btn btn-primary btn-sm get-detail my-2">Order Detail</button>';
                            $formEnd = '</form>';
                            $btn = $formStart .' '. $detail . ' ' . $editBtn .  ' ' . $deleteBtn . $formEnd;

                            return $btn;
                        }
                    }
                )->orderColumn('status', function ($query, $order) {
                    $query->orderBy('status_id', $order);
                })->orderColumn('price', function ($query, $order) {
                    $query->orderBy('price', $order);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getOrderDetail(Request $request)
    {
        if (request()->ajax()) {
            $order = Order::with('status:id,title')->with('customer:id,name,phone_no')->where('id', $request->order_id)->first();
            $orderItems = CartItem::with('item:id,name')->where('order_id', $order->id)->get();
            if ($order) {
                return response()->json([
                    'order' => $order,
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
    }




}
