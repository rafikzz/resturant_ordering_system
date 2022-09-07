<?php

namespace App\Exports;

use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\withMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ItemSalesReport implements FromCollection,withMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CartItem::with('item:id,name')->select('item_id', DB::raw('sum(quantity * price) as total_price'),DB::raw('sum(quantity) as total_quantity'))
        ->groupBy('item_id')->orderBy('total_quantity','desc')->get();
    }
    public function map($cartItem): array
    {

        return [
            [
                $cartItem->item_id,
                $cartItem->item->name,
                $cartItem->total_quantity,
                $cartItem->total_price,
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
