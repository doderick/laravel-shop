<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class CartService
{
    /**
     * 取得购物车
     *
     * @return void
     */
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    /**
     * 向购物车添加项目
     *
     * @param [type] $skuID
     * @param [type] $amount
     * @return void
     */
    public function add($skuId, $amount)
    {
        $user = Auth::user();
        // 从数据库中查询商品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // 如果存在则直接叠加商品数量
            $item->update([
                'amount' => $item->amount + $amount,
            ]);
        } else {
            // 否则创建一个新的购物车项目
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    /**
     * 从购物车中移除项目
     *
     * @param [type] $skuIds
     * @return void
     */
    public function remove($skuIds)
    {
        // 可以传递单个 ID，也可以传递 ID 数组
        if (! is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        Auth::user()->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
    }
}