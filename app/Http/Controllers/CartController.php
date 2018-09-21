<?php

namespace App\Http\Controllers;

use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Http\Requests\AddCartRequest;

class CartController extends Controller
{
    protected $cartService;

    /**
     * 注入 CartService 类
     *
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * 显示购物车
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        // 调用 cartService 中的方法
        $cartItems = $this->cartService->get();
        // 获取用户的收货地址，并传递到前端视图中
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', [
            'cartItems' => $cartItems,
            'addresses' => $addresses,
            ]);
    }

     /**
     * 将商品添加到购物车
     *
     * @param AddCartRequest $request
     * @return void
     */
    public function add(AddCartRequest $request)
    {
       $this->cartService->add($request->input('sku_id'), $request->input('amount'));

        return [];
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
        $this->cartService->remove($sku->id);

        return [];
    }
}
