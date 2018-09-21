<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserAddress;
use App\Http\Requests\Request;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;

class OrdersController extends Controller
{
    /**
     * 保存用户订单
     *
     * @param OrderRequest $request
     * @param OrderService $orderService
     * @return void
     */
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
    }

    /**
     * 显示订单列表
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $orders = Order::query()
                    ->with(['items.product', 'items.productSku'])
                    ->where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    /**
     * 显示订单详情
     *
     * @param Order $order
     * @param Request $request
     * @return void
     */
    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        // load 方法类似 with ，用于预防 N+1
        // load 是在已经查询出来的模型上调用， with 则是在 ORM查询构造器上调用
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }
}
