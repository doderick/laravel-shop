<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductSku;
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

    /**
     * 显示购物车
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        // ’productSku.product‘ 加载多层级的关联关系，预加载防止 N+1
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
        // 获取用户的收货地址，并传递到前端视图中
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', [
            'cartItems' => $cartItems,
            'addresses' => $addresses,
            ]);
    }

    /**
     * 从购物车中移除商品
     *
     * @param ProductSku $sku
     * @param Request $request
     * @return void
     */
    public function remove(ProductSku $sku, Request $request)
    {
        $request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();

        return [];
    }
}
