<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PatientOrderItems implements FromCollection, WithMapping, WithHeadings,WithStyles,WithTitle
{

    private $count;
    public function __construct($id)
    {
        $this->customer_id = $id;
        $this->customer= Customer::find($id);
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->count = OrderItem::with('item:id,name')->with('order')->whereHas('order',function($q) {
            $q->where('customer_id', $this->customer_id);
        })->where('total','>',0)->count();
        return OrderItem::with('item:id,name')->with('order')->whereHas('order',function($q) {
            $q->where('customer_id', $this->customer_id);
        })->where('total','>',0)->get();
    }

    public function map($orderItem): array
    {

        return [
            [
                $orderItem->order->bill_no,
                $orderItem->item->name,
                $orderItem->total,
                $orderItem->price,
                $orderItem->price *$orderItem->total,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Bill NO',
            'Item Name',
            'Quantity',
            'Price',
            'Sub Total',

        ];
    }

    public function styles(Worksheet $sheet)
    {
        $numOfRows =$this->count +1;
        $totalRow = $numOfRows + 1;

        // Add cell with SUM formula to last row
        $sheet->setCellValue("A{$totalRow}", "Total");
        $sheet->setCellValue("E{$totalRow}", "=SUM(E2:E{$numOfRows})");


    }

       /**
     * @return string
     */
    public function title(): string
    {
        return  $this->customer->name.'_'.$this->patient->register_no;
    }
}
