<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Jobs\CloseOrder;
use App\Models\ProductSku;
use App\Models\CouponCode;
use App\Models\UserAddress;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\CouponCodeUnavailableException;

class OrderService
{
    /**
     * 向数据库中添加订单
     *
     * @param User $user
     * @param UserAddress $address
     * @param text $remark
     * @param array $items
     * @param CouponCode $coupon
     * @return void
     */
    public function store(User $user, UserAddress $address, $remark, $items, CouponCode $coupon = null)
    {
        // 如果是否传入了优惠券，先检查优惠券是否可用
        if ($coupon) {
            // 此时还没有计算出订单总金额，先不校验
            $coupon->checkAvailable($user);
        }
        // 开启一个数据库事务
        $order = \DB::transaction(function() use ($user, $address, $remark, $items, $coupon) {
            // 更新地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                // 存入地址信息
                'address'      => [
                    'address'       => $address->full_address,
                    'zip_code'      => $address->zip_code,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                // 存入备注
                'remark'       => $remark,
                // 存入总金额
                'total_amount' => 0,
            ]);
            // 将订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            // 初始化总金额
            $totalAmount = 0;
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku =ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            // 计算使用优惠券之后的金额
            if ($coupon) {
                // 判断是否符合优惠券的使用规则
                $coupon->checkAvailable($user, $totalAmount);
                // 将订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                // 增加优惠券的使用量，需要判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new CouponCodeUnavailableException('该优惠券已被兑换完');
                }
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);
            // 将下单商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });
        // 使用 dispatch 函数设置订单超时支付关闭
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}