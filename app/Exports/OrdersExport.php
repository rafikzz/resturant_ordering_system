<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\withMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;


class OrdersExport implements FromCollection, withMapping, WithHeadings,WithStyles,WithPreCalculateFormulas
{
    // use Exportable;

    // public function __construct($startDate,$endDate)
    // {
    //     $this->startDate = $startDate;
    //     $this->endDate = $endDate;

    // }
    private $count;

    public function collection()
    {
        $this->count= Order::count();
        return Order::with('customer:id,name')->with('order_taken_by:id,name')->with('last_updated_by:id,name')->get();
    }
    public function map($order): array
    {

        return [
            [
                $order->id,
                $order->bill_no,
                $order->table_no,
                $order->customer->name,
                $order->total,
                $order->discount,
                $order->net_total,
                $order->created_at,
                $order->order_taken_by->name,
                $order->last_updated_by->name,



            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Bill No',
            'Table No',
            'Cusomter Name',
            'Total',
            'Discount',
            'Net Total',
            'Created At',
            'Created By',
            'Last Edited By',

        ];
    }


    public function styles(Worksheet $sheet)
    {
        $numOfRows =$this->count +1;
        $totalRow = $numOfRows + 1;


        // Add cell with SUM formula to last row
        $sheet->setCellValue("A{$totalRow}", "Total");
        $sheet->setCellValue("E{$totalRow}", "=SUM(E2:E{$numOfRows})");
        $sheet->setCellValue("F{$totalRow}", "=SUM(F2:F{$numOfRows})");
        $sheet->setCellValue("G{$totalRow}", "=SUM(G2:G{$numOfRows})");


    }

    // public function query()
    // {
    //     return Order::query()->whereBetween('order_datetime', [$this->startDate, $this->endDate]);
    // }
}
