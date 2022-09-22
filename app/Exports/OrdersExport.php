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
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;


class OrdersExport implements FromCollection, withMapping, WithHeadings,WithStyles,WithPreCalculateFormulas,WithColumnWidths
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
        $this->count= Order::where('status_id',3)->count();
        return Order::with('customer:id,name')->with('order_taken_by:id,name')->with('last_updated_by:id,name')->where('status_id',3)->get();
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
                $order->tax,
                $order->service_charge,
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
            'Tax',
            'Service Charge',
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
        $sheet->setCellValue("H{$totalRow}", "=SUM(H2:H{$numOfRows})");
        $sheet->setCellValue("I{$totalRow}", "=SUM(I2:I{$numOfRows})");



    }
    public function columnWidths(): array
    {
        return [
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,

        ];
    }

    // public function query()
    // {
    //     return Order::query()->whereBetween('order_datetime', [$this->startDate, $this->endDate]);
    // }
}
