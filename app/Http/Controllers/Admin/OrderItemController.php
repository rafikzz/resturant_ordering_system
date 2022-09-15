<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function update(Request $request,$id)
    {
        $order_item=OrderItem::where('id',$id)->first();
        $removedQuantity= $order_item->total-$request->quantity + $order_item->removed_quantity;
        $order = Order::find($order_item->order_id);
        if($removedQuantity<0 && $removedQuantity> $order_item->quantity){
            return response()->json([
                'status'=>'fail',
                'message'=>'Order Item Quantity More Than Ordered Value',
            ]);
        }
        $userId=auth()->id();

        $order_item->update([
            'updated_by' => $userId,
            'removed_quantity' => $removedQuantity,
            'total' => $request->quantity,
        ]);
        $total =$order->setTotal($userId);

        return response()->json([
            'total' => $total,
            'status'=>'success',
            'message'=>'Order Item Edited Sucessfully',
        ]);
    }
    public function destory(Request $request,OrderItem $order_item)
    {
        $removedQuantity =$order_item->quantity;
        $userId=auth()->id();

        $order_item->update([
            'updated_by' => $userId,
            'removed_quantity' => $removedQuantity,
            'total' => 0,
        ]);
        $order = Order::find($order_item->order_id);

        $total =$order->setTotal($userId);
        return response()->json([
            'total' => $total,
            'status'=>'success',
            'message'=>'Order Item Edited Sucessfully',
        ]);

    }
}
