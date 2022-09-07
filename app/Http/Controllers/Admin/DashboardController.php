<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->title = 'Dashboard';
    }
    public function index()
    {
        $title =$this->title;
        $totalSales = DB::table('table_orders')->sum(DB::raw('total - discount'));

        $todaysSales = DB::table('table_orders')->whereDate('order_datetime','=',Carbon::today())->sum(DB::raw('total - discount'));
        // dd($this->getSalesChartData());

        $totalCustomers =Customer::count();
        $topSoldItems =CartItem::with('item')->select('item_id', DB::raw('sum(quantity) as total'))
        ->groupBy('item_id')->orderBy('total','desc')->take(5)->get();
        $topSoldItem =($topSoldItems->first())? $topSoldItems->first()->item->name:null;

        return view('admin.dashboard',compact('title','totalSales','todaysSales','totalCustomers','topSoldItems','topSoldItem'));
    }
    public function getSalesChartData(){
        $totalSalesDataCount = [];

        $startDate = Carbon::today()->subDays(31);
        $endDate = Carbon::today()->addDays(1);

        $orders = Order::whereBetween("created_at", [$startDate, $endDate])->get();

        if ($orders != null) {

            $currentDate = Carbon::today()->subDays(31);

            while ((Carbon::today())->gte($currentDate)) {
                $totalSalesDataCount[$currentDate->format("d M")] = 0;

                $currentDate = $currentDate->addDays(1);
            }


            foreach ($totalSalesDataCount as $key => $totalSalesData) {
                foreach ($orders as $order) {
                    $date = Carbon::parse($order->created_at)->format("d M");
                    if ($key == $date) {
                        $totalSalesDataCount[$key] += $order->net_total;
                    }
                }
            }



            return response()->json([ 'salesData'=>$totalSalesDataCount]);
        }

    }
}
