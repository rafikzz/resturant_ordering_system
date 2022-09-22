<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->middleware('permission:report_list', ['only' => ['index']]);
        $this->title = 'Sales Reports';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Sales Report'=>route('admin.reports.sales.index')];

        $totalSales = DB::table('table_orders')->sum(DB::raw('net_total'));
        $todaysSales = DB::table('table_orders')->whereDate('order_datetime','=',Carbon::today())->sum(DB::raw('net_total'));


        return view('admin.reports.sales.index', compact('title','totalSales','todaysSales','breadcrumbs'));
    }


    public function getSalesData(Request $request)
    {
        if ($request->ajax()) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $data = Order::select('*')->with('status:id,title,color')->whereBetween('order_datetime', [$startDate, $endDate]);

            return DataTables::of($data)
                ->editColumn('created_at', function ($order) {
                    return [
                        'display' => $order->created_at->diffForHumans(),
                        'timestamp' => $order->created_at
                    ];
                })
                ->make(true);
        }
    }



    public function exportSales(Request $request){
            return Excel::download(new OrdersExport,'sales.xlsx');
    }
}
