<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function update(Request $request, $id)
    {
        $order_item = OrderItem::where('id', $id)->first();
        $removedQuantity = $order_item->total - $request->quantity + $order_item->removed_quantity;
        $order = Order::find($order_item->order_id);
        if ($removedQuantity < 0 && $removedQuantity > $order_item->quantity) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Order Item Quantity More Than Ordered Value',
            ]);
        }
        $userId = auth()->id();

        $order_item->update([
            'updated_by' => $userId,
            'removed_quantity' => $removedQuantity,
            'total' => $request->quantity,
        ]);
        $total = $order->setTotal($userId);
        $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
        $discountable_amount = $this->getCouponableDiscountAmount($order_items);
        $non_discountable_amount = $total - $discountable_amount;

        return response()->json([
            'discountable_amount' => $discountable_amount,
            'non_discountable_amount' => $non_discountable_amount,
            'total' => $total,
            'status' => 'success',
            'message' => 'Order Item Edited Sucessfully',
        ]);
    }
    public function destory(Request $request, $id)
    {

        $order_item = OrderItem::where('id', $id)->first();
        if ($order_item) {
            $removedQuantity = $order_item->quantity;
            $userId = auth()->id();

            $order_item->update([
                'updated_by' => $userId,
                'removed_quantity' => $removedQuantity,
                'total' => 0,
            ]);
            $order = Order::find($order_item->order_id);


            $total = $order->setTotal($userId);
            $order_items = OrderItem::where('order_id', $order->id)->with('item.category')->where('total', '>', 0)->get();
            $discountable_amount = $this->getCouponableDiscountAmount($order_items);
            $non_discountable_amount = $total - $discountable_amount;

            return response()->json([
                'discountable_amount' => $discountable_amount,
                'non_discountable_amount' => $non_discountable_amount,
                'total' => $total,
                'status' => 'success',
                'message' => 'Order Item Edited Sucessfully',
            ]);
        }
    }
    public function getCouponableDiscountAmount($orderItems)
    {
        $order_couponable_discount_amount = 0;
        if (isset($orderItems)) {
            foreach ($orderItems as $order_item) {
                $coupon_discount_percentage = $order_item->item->category->coupon_discount_percentage;
                $order_couponable_discount_amount += $order_item->price * ($order_item->total * $coupon_discount_percentage) / 100;
            }
        }
        return  $order_couponable_discount_amount;
    }
}
