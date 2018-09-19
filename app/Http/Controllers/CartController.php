<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;

class CartController extends Controller
{
    /**
     * 将商品添加到购物车
     *
     * @param AddCartRequest $request
     * @return void
     */
    public function add(AddCartRequest $request)
    {
        $user   = $request->user();
        $skuId  = $request->input('sku_id');
        $amount = $request->input('amount');

        // 从数据库中查询该商品是否已经在购物车中
        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // 如果存在则直接增加购物车中商品的数量
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {
            // 如果不存在，则在购物车中增加一条新记录
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return [];
    }
}
