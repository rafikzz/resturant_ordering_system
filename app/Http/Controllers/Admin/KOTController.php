<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KOTController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:order_list', ['only' => ['index', 'getData', 'getOrderDetail',]]);
        $this->title = 'KOT';
    }
    public function index()
    {
        $title = $this->title;
        $breadcrumbs = ['Order' => route('admin.kot.index')];

        return view('admin.kot.index', compact('title', 'breadcrumbs'));
    }


    public function getData(Request $request)
    {

        if ($request->ajax()) {

            $processingStatus = Status::where('title', 'processing')->first()->id;
            switch ($request->mode) {
                case ('all'):
                    $today = Carbon::today();
                    $data = Order::select('*')->with('customer:id,name')->with('status:id,title,color');
                    break;
                case ('completed'):
                    $today = Carbon::today();
                    $data = Order::select('*')->with('customer:id,name')->with('status:id,title,color')->where('status_id', 3);

                case ('processing'):
                    $today = Carbon::today();
                    $data = Order::select('*')->with('customer:id,name')->with('status:id,title,color')->where('status_id', 1);
                    break;
            }

            return DataTables::of($data)
                ->editColumn('created_at', function ($order) {
                    return [
                        'display' => $order->created_at->diffForHumans(),
                        'timestamp' => $order->created_at
                    ];
                })->addColumn(
                    'action',
                    function ($row, Request $request) use ($processingStatus) {

                            $detail = '<button rel="' . $row->id . '"  class="btn btn-primary btn-xs get-detail my-2"  data-toggle="tooltip" title="Detail"><i class="fa fa-eye"></i></button>';
                            return  $detail;
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
            $orderItems = OrderItem::with('item:id,name')->where('order_id', $order->id)->where('total', '>', 0)->get()->groupBy('order_no');
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
    }
}
