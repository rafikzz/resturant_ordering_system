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
use App\Models\Customer;
use App\Models\CustomerType;
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
        $breadcrumbs = ['Item Report' => route('admin.reports.item_sales.index')];


        $title = $this->title;
        $totalSales = DB::table('table_orders')->sum(DB::raw('total - discount'));
        $todaysSales = DB::table('table_orders')->whereDate('order_datetime', '=', Carbon::today())->sum(DB::raw('total - discount'));
        $customer_types = CustomerType::orderBy('name')->get();

        return view('admin.reports.items-sales.index', compact('title', 'totalSales', 'customer_types', 'todaysSales', 'breadcrumbs'));
    }



    public function getItemSalesData(Request $request)
    {
        if ($request->ajax()) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $data = OrderItem::with('item')->selectRaw('table_order_items.item_id,sum(table_order_items.total * table_order_items.price) as total_price, sum(table_order_items.total) as total_quantity')
                ->groupBy('item_id')->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('order_datetime', [$startDate, $endDate])->where('status_id', 3);
                })->customer($request->customer_id);


            return DataTables::of($data)->orderColumn('item', function ($query, $order) {
                $query->orderBy('item_id', $order);
            })
                ->make(true);
        }
    }

    public function exportSales(Request $request)
    {
        $startDate = null;
        $endDate = null;
        $cusomterName = '';
        $customer_id = $request->customer_id;
        $cusomterName = '';

        if ($customer_id) {
            $customer = Customer::find($customer_id);
            if (!$customer) {
                return redirect()->back()->with('error', 'Customer Not Found');
            }
            $cusomterName = $customer->customer_name_with_detail() . ' ';
        }
        if ($request->date_range) {
            $date = explode('-', $request->date_range);
            $startDate = Carbon::parse(trim($date[0]))->startOfDay();
            $endDate = Carbon::parse(trim($date[1]))->endOfDay();
        }
        $excelName =  $cusomterName . 'Item Sales Report-' . Carbon::parse(trim($date[0]))->format('Y-m-d') . ' To ' . Carbon::parse(trim($date[1]))->format('Y-m-d') . '.xlsx';

        return Excel::download(new ItemSalesReport($startDate, $endDate, $customer_id),    $excelName);
    }
}
