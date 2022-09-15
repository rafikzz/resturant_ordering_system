<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ItemSalesReport;
use App\Models\OrderItem;

class ItemSalesReportController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->middleware('permission:report_list', ['only' => ['index']]);
        $this->title = 'Item Sales Reports';
    }

    public function index(Request $request)
    {
        $breadcrumbs =[ 'Item Report'=>route('admin.reports.item_sales.index')];


        $title = $this->title;
        $totalSales = DB::table('table_orders')->sum(DB::raw('total - discount'));
        $todaysSales = DB::table('table_orders')->whereDate('order_datetime','=',Carbon::today())->sum(DB::raw('total - discount'));


        return view('admin.reports.items-sales.index', compact('title','totalSales','todaysSales','breadcrumbs'));
    }



    public function getItemSalesData(Request $request)
    {
        if ($request->ajax()) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            if(!$request->order){
                $data = OrderItem::with('item:id,name')->select('item_id', DB::raw('sum(total * price) as total_price'),DB::raw('sum(total) as total_quantity'))
                ->groupBy('item_id')->whereHas('order',function($q) use($startDate,$endDate){
                    $q->whereBetween('order_datetime', [$startDate, $endDate]);
                })->orderBy('total_quantity','desc');
            }else{
                $data = OrderItem::with('item:id,name')->select('item_id', DB::raw('sum(total * price) as total_price'),DB::raw('sum(total) as total_quantity'))
                ->groupBy('item_id')->whereHas('order',function($q) use($startDate,$endDate){
                    $q->whereBetween('order_datetime', [$startDate, $endDate]);
                });
            }

            return DataTables::of($data)->orderColumn('item', function ($query, $order) {
                $query->orderBy('item_id', $order);
            })
                ->make(true);
        }
    }

    public function exportSales(Request $request){
            return Excel::download(new ItemSalesReport,'item_sales.xlsx');
    }
}
