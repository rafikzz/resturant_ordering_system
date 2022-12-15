<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Cart;

class CartController extends Controller
{

    public function getCartItems(Request $request)
    {
        if ($request->ajax()) {
            $cartItems = Cart::getContent();
            $total = Cart::getTotal();
            if (isset($cartItems)) {
                $discountable_amount = $this->getDiscoutableAmount();
                $total = Cart::getTotal();
                $non_discountable_amount = $total - $discountable_amount;
                return response()->json([
                    'discountable_amount' => $discountable_amount,
                    'non_discountable_amount' => $non_discountable_amount,
                    'total' => $total,
                    'status' => 'success',
                    'message' => 'Item added successfully',
                ]);
            }
            return response()->json([
                'discountable_amount' => 0,
                'non_discountable_amount' => 0,
                'total' =>  0,
                'status' => 'fail',
                'message' => 'No Item found',
            ]);
        }
    }

    public function addCartItem(Request $request)
    {
        $item = Cart::getContent();


        if ($request->ajax()) {
            $item = Item::with('category')->where('id', $request->item_id)->first();
            if ($request->guest_menu == 1) {
                $price = $item->guest_price;
            } else {
                $price = $item->price;
            }

            if (isset($item)) {
                Cart::add(array(
                    'id' => $item->id, // uinique row ID
                    'name' => $item->name,
                    'price' => $price,
                    'quantity' => '1',
                    'attributes' => ['coupon_discount_percentage' => (float)$item->category->coupon_discount_percentage]

                ));
                $cartItems = Cart::getContent();
                $discountable_amount = $this->getDiscoutableAmount();
                $total = Cart::getTotal();
                $non_discountable_amount = $total - $discountable_amount;
                return response()->json([
                    'discountable_amount' => $discountable_amount,
                    'non_discountable_amount' => $non_discountable_amount,
                    'items' => $cartItems,
                    'total' => $total,
                    'status' => 'success',
                    'message' => 'Item added successfully',
                ]);
            }
            return response()->json([
                'status' => 'fail',
                'message' => 'No Item found',
            ]);
        }
    }

    public function removeCartItem(Request $request)
    {
        if ($request->ajax()) {
            $item = Item::where('id', $request->item_id)->first();

            if (isset($item)) {
                Cart::remove($item->id);
                $discountable_amount = $this->getDiscoutableAmount();
                $total = Cart::getTotal();
                $non_discountable_amount = $total - $discountable_amount;
                return response()->json([
                    'discountable_amount' => $discountable_amount,
                    'non_discountable_amount' => $non_discountable_amount,
                    'total' => $total,
                    'status' => 'success',
                    'message' => 'Item removed successfully',
                ]);
            }
            return response()->json([
                'status' => 'fail',
                'message' => 'No Item found',
            ]);
        }
    }

    public function editCartItemQuantity(Request $request)
    {
        if ($request->ajax()) {
            $itemId = $request->item_id;
            $quantity = $request->quantity;
            if ($itemId) {
                Cart::update($itemId, array(
                    'quantity' => array(
                        'relative' => false,
                        'value' => $quantity
                    ),
                ));
                $discountable_amount = $this->getDiscoutableAmount();
                $total = Cart::getTotal();
                $non_discountable_amount = $total - $discountable_amount;
                return response()->json([
                    'discountable_amount' => $discountable_amount,
                    'non_discountable_amount' => $non_discountable_amount,
                    'total' => $total,
                    'status' => 'success',
                    'message' => 'Item Quantity Updated successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'No Item found',
                ]);
            }
        }
    }

    public function clearCart(Request $request)
    {
        if ($request->ajax()) {
            Cart::clear();
            $discountable_amount = $this->getDiscoutableAmount();
            $total = Cart::getTotal();
            $non_discountable_amount = $total - $discountable_amount;
            return response()->json([
                'status' => 'success',
                'discountable_amount' => $discountable_amount,
                'non_discountable_amount' => $non_discountable_amount,
                'total' => $total,
                'message' => 'Cart Cleared Successfully',
            ]);
        }
    }
    public function getDiscoutableAmount()
    {
        $discountable_amount = 0;
        $items = Cart::getContent();
        foreach ($items as $item) {
            $discountable_amount += $item->quantity* $item->attributes->coupon_discount_percentage * $item->price / 100;
        }
        return $discountable_amount;
    }
}
