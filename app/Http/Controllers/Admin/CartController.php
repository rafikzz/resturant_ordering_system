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

                return response()->json([
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

    public function addCartItem(Request $request)
    {
        if ($request->ajax()) {
            $item = Item::where('id', $request->item_id)->first();

            if (isset($item)) {
                Cart::add(array(
                    'id' => $item->id, // uinique row ID
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => '1'
                ));
                $cartItems = Cart::getContent();
                $total = Cart::getTotal();
                return response()->json([
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
                $total = Cart::getTotal();


                return response()->json([
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
                $total = Cart::getTotal();
                return response()->json([
                    'total' => $total,
                    'status' => 'success',
                    'message' => 'Item Quantity Updated successfully.',
                ]);
            }else{
                return response()->json([
                    'status' => 'fail',
                    'message' => 'No Item found',
                ]);
            }
        }
    }
}
