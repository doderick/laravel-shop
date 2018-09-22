<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Events\OrderPaid;

class PaymentController extends Controller
{
    /**
     * 使用支付宝进行支付
     *
     * @param Order $order
     * @param Request $request
     * @return void
     */
    public function payByAlipay(Order $order, Request $request)
    {
        // 判断订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断订单状态是否为已支付或者已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        // 调用支付宝的网页支付
        return app('alipay')->web([
            // 订单编号，需保证在商户端不重复
            'out_trade_no' => $order->no,
            // 订单金额，单位元，支持小数点后两位
            'total_amount' => $order->total_amount,
            // 订单标题
            'subject'      => '支付 Laravel 商城 的订单：' . $order->no,
        ]);
    }

    /**
     * 支付宝前端回调
     *
     * @return void
     */
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
       try {
           app('alipay')->verify();
       } catch (\Excrption $e) {
           return view('pages.error', ['msg' => '数据不正确']);
       }

       return view('pages.success', ['msg' => '付款成功']);
    }

    /**
     * 支付宝服务器回调
     *
     * @return void
     */
    public function alipayNotify()
    {
        // 校验输入参数
        $data = app('alipay')->verify();
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 判断订单是否存在，没卵用
        if (! $order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => 'alipay',
            'payment_no'     => $data->trade_no,
        ]);

        $this->afterPaid($order);

        return app('alipay')->success();
    }

    /**
     * 触发 OrderPaid 事件
     *
     * @param Order $order
     * @return void
     */
    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
