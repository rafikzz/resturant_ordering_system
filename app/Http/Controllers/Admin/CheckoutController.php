<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderCheckoutRequest;
use App\Models\CustomerWalletTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Setting;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;

class CheckoutController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:order_create', ['only' => ['index', 'store']]);
        $this->title = 'Order Checkout';
    }
    public function index($id)
    {

        $breadcrumbs = ['Order' => route('admin.orders.index'), 'Checkout' => '#'];
        $title = $this->title;
        $processingStatus = Status::where('title', 'Processing')->first()->id;
        $order = Order::where('status_id', '=', $processingStatus)->with('customer:id,name')->findOrFail($id);
        $order_items = OrderItem::where('order_id', $order->id)->with('item:id,name')->where('total', '>', 0)->get()->groupBy('order_no');
        $setting = Setting::first();
        $tax = isset($setting) ? $setting->getTax() : 0;
        $service_charge = isset($setting) ? $setting->getServiceCharge() : 0;
        $payment_types = PaymentType::where('status', 1)->get();
        $wallet_balance = isset($order->customer_id) ? $order->customer->wallet_balance() : null;


        return view('admin.checkout.index', compact('order', 'payment_types', 'order_items', 'wallet_balance', 'title', 'breadcrumbs', 'tax', 'service_charge'));
    }

    public function store(OrderCheckoutRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $processingStatus = Status::where('title', 'Processing')->first()->id;

            $order = Order::where('status_id', '=', $processingStatus)->with('status:id,title')->with('customer:id,name')->findOrFail($id);

            if ($request->discount > $order->total) {
                if($request->ajax){
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Discount is greater than total',
                    ]);
                }
                return back()->with('success', 'Discount is greater than total');
            }
            $completedStatus = Status::where('title', 'Completed')->first()->id;
            $service_charge = $order->serviceCharge($request->discount ?: 0);
            $tax_amount = $order->taxAmount($request->discount ?: 0);
            $netTotal = $order->totalWithTax($request->discount ?: 0);
            $dueAmount = round(($netTotal - $request->paid_amount), 2);

            $order->update([
                'discount' => $request->discount ?: 0,
                'service_charge' => $service_charge,
                'tax' => $tax_amount,
                'status_id' => $completedStatus,
                'payment_type_id' => $request->payment_type_id,
                'net_total' => $netTotal,
                'updated_by' => auth()->id(),
            ]);

            if ($request->payment_type_id == 3) {
                if ($dueAmount != 0) {

                    $this->store_customer_wallet_transacion($order, $dueAmount,$request->paid_amount);
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        DB::commit();

        // $orderItems = OrderItem::where('order_id',$order->id)->where('total','>',0)->get();
        // foreach($orderItems as $item){
        //     $items[$item->id] =  (new InvoiceItem())->title($item->item->name)->pricePerUnit($item->price)->quantity($item->total)->subTotalPrice($item->price*$item->total);
        // }
        //

        if ($request->ajax()) {

            $order = Order::with('status:id,title')->with('payment_type:id,name')->with('customer:id,name')->findOrFail($id);
            $orderItems = OrderItem::with('item:id,name')->where('order_id', $order->id)->where('total', '>', 0)->get();
            $billRoute = route('orders.getBill', $order->id);
            if ($order) {
                return response()->json([
                    'order' => $order,
                    'billRoute' => $billRoute,
                    'orderItems' => $orderItems,
                    'status' => 'success',
                    'message' => 'Order fetched successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'No Order found',
                ]);
            }
        }


        return redirect()->route('admin.orders.index')->with('success', 'Order Checked Out Successfully');
    }

    public function store_customer_wallet_transacion($order, $dueAmount,$paidAmount)
    {
        $wallet_balance = isset($order->customer_id) ? $order->customer->wallet_balance() : 0;
        $current_balance = $wallet_balance - $dueAmount;

        CustomerWalletTransaction::create([
            'customer_id' => $order->customer_id,
            'order_id' => $order->id,
            'previous_amount' => $wallet_balance,
            'amount' => $dueAmount,
            'total_amount'=>$paidAmount,
            'current_amount' => $current_balance,
            'transaction_type_id' => 3,
            'author_id' => auth()->id(),
        ]);
    }
}
