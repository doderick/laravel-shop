<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserAddress;
use App\Http\Requests\Request;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\SendReviewRequest;
use Carbon\Carbon;
use App\Events\OrderReviewed;

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

    /**
     * 用户确认收货
     *
     * @param Order $order
     * @param Request $request
     * @return void
     */
    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }
        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        // 返回订单信息
        return $order;
    }

    /**
     * 用户对订单进行评价
     *
     * @param Order $order
     * @return void
     */
    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已经支付
        // if (! $order->paid_at) {
        //     throw new InvalidRequestException('该订单未支付，不可评价');
        // }
        // 判断是否已收货
        if ($order->ship_status !== Order::SHIP_STATUS_RECEIVED) {
            throw new InvalidRequestException('还没有确认收货，不可评价');
        }
        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    /**
     * 处理用户的评价
     *
     * @param Order $order
     * @param SendReviewRequest $request
     * @return void
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已收货
        if ($order->ship_status !== Order::SHIP_STATUS_RECEIVED) {
            throw new InvalidRequestException('还没有确认收货，不可评价');
        }
        // 判断是否已评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input('reviews');
        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
            // 触发 OrderReviewed 事件
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }
}
