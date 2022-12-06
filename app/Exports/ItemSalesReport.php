<?php

namespace App\Exports;

use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\withMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ItemSalesReport implements FromCollection,withMapping, WithHeadings
{

    public function __construct($startDate,$endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $date=['start'=>$this->startDate,'end'=>$this->endDate];
        return OrderItem::with('item:id,name')->select('item_id', DB::raw('sum(total * price) as total_price'),DB::raw('sum(total) as total_quantity'))
        ->groupBy('item_id')->whereHas('order',function($q) use($date) {
            $q->where('status_id',3)->dateBetween($date);
        })->orderBy('total_quantity','desc')->get();

    }
    public function map($orderItem): array
    {

        return [
            [
                $orderItem->item_id,
                $orderItem->item->name,
                $orderItem->total_quantity,
                $orderItem->total_price,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Item Name',
            'Total Quantity',
            'Total',

        ];
    }
}
