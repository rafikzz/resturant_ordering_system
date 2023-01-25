<?php

namespace App\Http\Services;

use App\Models\Customer;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class PrintOrderService
{
    //For Printing KOT
    public static function  printKot($id, $order_no = null)
    {
        $printer = "CutePDF Writer";
        //Retrive order from Database
        $order = Order::where('id', $id)->with('customer.customer_type')->firstOrFail();
        if ($order) {
            if($order->status)
            try {
                $setting = Setting::first();
                $prefix = isset($setting) ? ($setting->bill_no_prefix ?: '') : '';

                if ($order_no) {
                    $orderItems = OrderItem::with('item')->where('order_id', $order->id)->where('order_no', $order_no)->where('total', '>', 0)->get();
                } else {
                    $orderItems = OrderItem::with('item')->where('order_id', $order->id)->where('total', '>', 0)->get();
                }
                // Connect to the printer
                $date = date('Y-m-d h:i A');
                $connector = new WindowsPrintConnector($printer);
                $printer = new Printer($connector);
                /* Title of receipt */
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                // $printer->setEmphasis(true);
                $printer->text("KOT LIST\n");
                // $printer->setEmphasis(false);
                $printer->feed();
                /* Bill Information */
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Bill No: {$prefix}{$order->bill_no} \n");
                $printer->text("Order From:  {$order->destination} {$order->destination_no} \n");
                $printer->text("Order Date: " . $order->order_datetime  . "\n");
                $printer->text("Customer Name: " . $order->customer->customer_name_with_detail()  . "\n");
                $printer->text("Customer Type: " . $order->customer->customer_type->name  . "\n");
                if ($order_no) {
                    $printer->text("Order No: {$order_no} \n");
                }

                $printer->feed();
                /* Items */
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setEmphasis(true);
                $printer->setUnderline(true);
                $heading = PrintOrderService::kot_format('Items', 'Qty.');
                $printer->setEmphasis(false);
                $printer->setUnderline(false);
                foreach ($heading as $k => $v) {
                    $printer->text($v);
                }
                if (!!empty($orderItems)) {
                    $n = 1;
                    foreach ($orderItems as $k => $orderItem) {
                        $itms = PrintOrderService::kot_format($n . '.' . $orderItem->item->name, $orderItem->quantity);
                        foreach ($itms as $k => $vv) {
                            $printer->text($vv);
                        }
                        $n++;
                    }
                }
                /* Footer */
                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                if (!!empty($order->note)) {
                    $printer->text("Note: " . $order->note . "\n");
                }
                $printer->text($date . "\n");
                $printer->feed(7);

                /* Cut the receipt and open the cash drawer */
                $printer->cut();
                $printer->pulse();
                $printer->setEmphasis(false);
                $printer->setUnderline(false);
            } catch (\Exception $e) {
                echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
            } finally {
                $printer->close();
            }
        }
    }
    public static function  printBill($id)
    {
        $printer = "CutePDF Writer";

        $completedStatus = Status::where('title', 'completed')->first()->id;
        $order = Order::where('id', $id)->with('customer.customer_type')->with('payment_type')->where('status_id',$completedStatus)->first();
        if ($order) {
            try {
                // Retrieve sale data from the database
                $order->load('customer');
                $setting = Setting::first();
                $prefix = isset($setting) ? ($setting->bill_no_prefix ?: '') : '';
                $customer = Customer::with('staff.department')->with('patient')->with('customer_type')->find($order->customer_id);
                $orderItems = OrderItem::select('item_id', DB::raw('sum(total * price) as total_price'), DB::raw('sum(total) as total_quantity'))
                    ->with('item')->where('order_id', $order->id)->where('total', '>', 0)->groupBy('item_id')->get();
                // Connect to the printer
                $date = date('Y-m-d h:i A');
                $connector = new WindowsPrintConnector($printer);
                $printer = new Printer($connector);
                /*Name of Company*/
                $printer->setEmphasis(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text(isset($setting) ? $setting->company_name : 'Seller');
                $printer->text(isset($setting) ? $setting->office_location : 'XYZ Address');
                $printer->feed();
                /* Title of receipt */
                // $printer->setEmphasis(true);
                $printer->text("ESTIMATE BILL\n");
                // $printer->setEmphasis(false);
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Bill No: {$prefix}{$order->bill_no} \n");
                $printer->text("Order From:  {$order->destination} {$order->destination_no} \n");
                $printer->text("Order Date: " . $order->order_datetime  . "\n");
                $printer->text("Customer Name: " . $order->customer->customer_name_with_detail()   . "\n");
                $printer->text("Customer Type: " . $customer->customer_type->name  . "\n");
                $printer->text( $order->is_credit?"Payment Type: Account":"Payment Type: Cash"  . "\n");
                $printer->feed();


                /* Items */
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setEmphasis(true);
                $printer->setUnderline(true);
                $heading = PrintOrderService::item_format('Items', 'Qty.', 'Total');
                foreach ($heading as $k => $v) {
                    $printer->text($v);
                }
                $printer->setEmphasis(false);
                $printer->setUnderline(false);
                if (!empty($orderItems)) {
                    $n = 1;
                    foreach ($orderItems as $k => $orderItem) {
                        $itms = PrintOrderService::item_format($n . '.' . $orderItem->item->name, $orderItem->total_quantity, floatval($orderItem->total_price));
                        foreach ($itms as $k => $vv) {
                            $printer->text($vv);
                        }
                        $n++;
                    }
                }
                /* Total And Charges */
                $printer->text("\n");
                $subtotal =   PrintOrderService::item_format('SubTotal', " ", $order->total);
                foreach ($subtotal as $k => $st) {
                    $printer->text($st);
                }
                $discount = PrintOrderService::item_format('Discount', " ", $order->discount);
                foreach ($discount as $k => $dt) {
                    $printer->text($dt);
                }

                if (!empty($order->service_charge) && $order->service_charge !=0) {
                    $service_charge = PrintOrderService::item_format('Service Charge', " ", $order->service_charge);
                    foreach ($service_charge as $k => $dt) {
                        $printer->text($dt);
                    }
                }
                if (!empty($order->tax)&& $order->tax !=0) {
                    $tax = PrintOrderService::item_format('Tax', " ", $order->tax);
                    foreach ($tax as $k => $dt) {
                        $printer->text($dt);
                    }
                }

                if (!empty($order->delivery_charge)&& $order->delivery_charge !=0) {

                    $delivery_charge = PrintOrderService::item_format('Delivery Charge', " ", $order->delivery_charge);

                    foreach ($delivery_charge as $k => $dt) {
                        $printer->text($dt);
                    }
                }

                $grand_total =   PrintOrderService::item_format('Grand Total', " ", $order->net_total ?: $order->total);
                foreach ($grand_total as $k => $st) {
                    $printer->text($st);
                }

                /* Footer */
                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text($date . "\n");
                $printer->text("Thank you for your visit\n");
                $printer->text("Cashier:".auth()->user()->name." \n" );
                $printer->text($date . "\n");
                $printer->feed(7);
                /* Cut the receipt and open the cash drawer */
                $printer->cut();
                $printer->pulse();

                $printer->setEmphasis(false);
                $printer->setUnderline(false);
            } catch (\Exception $e) {
                echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
            } finally {

                $printer->close();
            }
        }else{
            return 1;
        }
    }


    public static function  kot_format($item = "", $qnty = "")
    {

        $arr = array();
        $str_arr = str_split($item, 24);
        foreach ($str_arr as $k => $itm) {
            if ($k == 0) {
                $arr[] = str_pad(trim($itm), 35, " ") . str_pad($qnty, 5, " ") . "\n";
            } else {
                $arr[] = str_pad(trim($itm), 35, " ") . "\n";
            }
        }

        return $arr;
    }

    public static function  item_format($item = '', $qnty = '', $total = '')
    {
        $arr = array();
        $str_arr = str_split($item, 24);
        foreach ($str_arr as $k => $itm) {
            if ($k == 0) {
                $arr[] = str_pad(trim($itm), 28, " ") . str_pad($qnty, 5, " ") . str_pad($total, 7, " ", STR_PAD_LEFT) . "\n";
            } else {
                $arr[] = str_pad(trim($itm), 28, " ") . "\n";
            }
        }

        return $arr;
    }
}
