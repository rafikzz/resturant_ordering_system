<?php

namespace App\Exports;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\withMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemSalesReport implements FromCollection, withMapping, WithHeadings, ShouldAutoSize, WithStyles
{

    public function __construct($startDate, $endDate, $customer_id)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->customer_id = $customer_id;
        $this->customer = null;
        $this->title = 'Item Sales Report From ' . $this->startDate . ' To ' . $this->endDate;
        if ($this->customer_id) {
            $this->customer = Customer::with('customer_type')->with('staff')->with('patient')->find($this->customer_id);
            $this->name = 'Name:' . $this->customer->name;
            $this->customer_type = 'Type:' . $this->customer->customer_type->name;
            $this->extra = '';
            if ($this->customer->customer_type_id == 2) {
                $this->extra = ($this->customer->staff->department) ? 'Department:' . $this->customer->staff->department->name : 'N/A';
            }
            if ($this->customer->customer_type_id == 3) {
                $this->extra = ($this->customer->patient) ? 'Register No:' . $this->customer->staff->department->name : 'N/A';
            }
        }
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $date = ['start' => $this->startDate, 'end' => $this->endDate];
        return OrderItem::with('item:id,name')->select('item_id', DB::raw('sum(total * price) as total_price'), DB::raw('sum(total) as total_quantity'))
            ->groupBy('item_id')->customer($this->customer_id)->dateBetween($date)->orderBy('total_quantity', 'desc')->get();
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
    public function styles(Worksheet $sheet)
    {

        $sheet->mergeCells("A1:E1");

    }

    public function headings(): array
    {
        if ($this->customer) {

            return [
                [$this->title],
                [$this->name, $this->customer_type, $this->extra], [
                    'Id',
                    'Item Name',
                    'Total Quantity',
                    'Total',

                ]
            ];
        } else {

            return [
                [$this->title], [
                    'Id',
                    'Item Name',
                    'Total Quantity',
                    'Total',

                ]
            ];
        }
    }
}
