<?php

namespace App\Exports;

use App\Models\Customer;
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


class OrdersExport implements FromCollection, withMapping, WithHeadings, WithStyles, WithPreCalculateFormulas, WithColumnWidths
{
    // use Exportable;
    private $count;

    public function __construct($startDate, $endDate, $customer_id)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->customer_id = $customer_id;
        $this->customer = null;
        $this->title = 'Sales Report From ' . $this->startDate . ' To ' . $this->endDate;
        if ($this->customer_id) {
            $this->customer = Customer::with('customer_type')->with('staff')->with('patient')->find($this->customer_id);
            $this->name = 'Name:' . $this->customer->name;
            $this->customer_type_name = 'Type:' . $this->customer->customer_type->name;
            $this->extra = '';
            if ($this->customer->customer_type_id == 2) {
                $this->extra = ($this->customer->staff->department) ? 'Department:' . $this->customer->staff->department->name : 'N/A';
            }
            if ($this->customer->customer_type_id == 3) {
                $this->extra = ($this->customer->patient) ? 'Register No:' . $this->customer->staff->department->name : 'N/A';
            }
        }
    }

    public function collection()
    {
        $date = ['start' => $this->startDate, 'end' => $this->endDate];

        $orders = Order::with('customer:id,name')->with('order_taken_by:id,name')->with('last_updated_by:id,name')->with('coupon')
            ->customer($this->customer_id)->dateBetween($date)->where('status_id', 3)->get();
        $this->count = $orders->count();
        return $orders;
    }
    public function map($order): array
    {

        return [
            [
                $order->id,
                $order->bill_no,
                $order->destination . ' ' . $order->destination_no,
                $order->customer->name,
                $order->total,
                $order->discount,
                $order->tax,
                $order->service_charge,
                $order->delivery_charge,
                $order->net_total,
                $order->created_at,
                $order->order_taken_by->name,
                $order->last_updated_by->name,
                $order->coupon ? $order->coupon->title : '',
                $order->guest_menu ? 'Guest Menu' : 'Staff Menu',




            ],
        ];
    }

    public function headings(): array
    {
        if ($this->customer) {
            return [
                [$this->title],
                [$this->name, $this->customer_type_name, $this->extra],
                [
                    'Id',
                    'Bill No',
                    'Destination No',
                    'Cusomter Name',
                    'Total',
                    'Discount',
                    'Tax',
                    'Service Charge',
                    'Packaging Charge',
                    'Net Total',
                    'Created At',
                    'Created By',
                    'Last Edited By',
                    'Coupon',
                    'Menu Type',
                ]

            ];
        } else {

            return [
                [$this->title], [
                    'Id',
                    'Bill No',
                    'Destination No',
                    'Cusomter Name',
                    'Total',
                    'Discount',
                    'Tax',
                    'Service Charge',
                    'Packaging Charge',
                    'Net Total',
                    'Created At',
                    'Created By',
                    'Last Edited By',
                    'Coupon',
                    'Menu Type',
                ]
            ];
        }
    }


    public function styles(Worksheet $sheet)
    {
        if ($this->customer) {
            $numOfRows = $this->count + 3;
            $totalRow = $numOfRows + 1;
            $startRow=4;
        } else {
            $startRow=3;
            $numOfRows = $this->count + 2;
            $totalRow = $numOfRows + 1;
        }

        $sheet->mergeCells("A1:E1");


        // Add cell with SUM formula to last row
        $sheet->setCellValue("A{$totalRow}", "Total");
        $sheet->setCellValue("E{$totalRow}", "=SUM(E{$startRow}:E{$numOfRows})");
        $sheet->setCellValue("F{$totalRow}", "=SUM(F{$startRow}:F{$numOfRows})");
        $sheet->setCellValue("G{$totalRow}", "=SUM(G{$startRow}:G{$numOfRows})");
        $sheet->setCellValue("H{$totalRow}", "=SUM(H{$startRow}:H{$numOfRows})");
        $sheet->setCellValue("I{$totalRow}", "=SUM(I{$startRow}:I{$numOfRows})");
        $sheet->setCellValue("J{$totalRow}", "=SUM(J{$startRow}:J{$numOfRows})");
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
