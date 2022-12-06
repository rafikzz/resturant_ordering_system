<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersByCustomerExport implements FromView,ShouldAutoSize
{

    use Exportable;

    public function view(): View
    {
        $customers =Customer::with('orders_summary')->with('patient')->whereHas('patient')->orderBy('name','asc')->get();

        // dd($customers);
        return view('admin.excel_export.customer_order_items', [
            'customers' => $customers
        ]);
    }




}
