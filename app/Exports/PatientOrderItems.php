<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PatientOrderItems implements FromView,ShouldAutoSize
{

    public function __construct($id)
    {
        $this->customer_id = $id;
        $this->customer= Customer::with('orders_summary')->with('patient')->find($id);
    }

    public function view(): View
    {
        $order_items=OrderItem::select('item_id', DB::raw('sum(total * price) as total_price'),DB::raw('sum(total) as total_quantity'))->with('item:id,name')->with('order')->whereHas('order',function($q) {
            $q->where('customer_id', $this->customer_id)->where('status_id',3);
        })->where('total','>',0)->groupBy('item_id')->get();

        return view('admin.excel_export.customer_detailed_order',
            ['orders_item'=>$order_items,'customer'=> $this->customer]);

    }



}
