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
        $breadcrumbs = ['Sales Report' => route('admin.reports.sales.index')];

        // $totalSales = DB::table('table_orders')->where('status_id',3)->sum(DB::raw('net_total'));
        $todaysSales = DB::table('table_orders')->whereDate('order_datetime', '=', Carbon::today())->where('status_id',3)->sum(DB::raw('net_total'));


        return view('admin.reports.sales.index', compact('title', 'todaysSales', 'breadcrumbs'));
    }


    public function getSalesData(Request $request)
    {
        if ($request->ajax()) {
            if ($request->startDate && $request->endDate) {
                $startDate = Carbon::parse($request->startDate)->startOfDay();
                $endDate = Carbon::parse($request->endDate)->endOfDay();
                $data = Order::select('*')->with('status:id,title,color')->whereBetween('order_datetime', [$startDate, $endDate])->where('status_id',3);
            } else {

                $data = Order::select('*')->with('status:id,title,color')->whereBetween('order_datetime')->where('status_id',3);
            }

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



    public function exportSales(Request $request)
    {

        $startDate = null;
        $endDate = null;
        if ($request->date_range) {
            $date = explode('-', $request->date_range);
            $startDate = Carbon::parse(trim($date[0]))->startOfDay();
            $endDate = Carbon::parse(trim($date[1]))->endOfDay();
        }
        return Excel::download(new OrdersExport($startDate, $endDate), 'sales.xlsx');
    }
}
