<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        $this->order = $order;

        // 设置延迟的时间， delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 判断相应的订单是否已支付，
        // 如果已支付，则不需要关闭订单，直接退出即可
        if ($this->order->paid_at) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function () {
            // 将订单的 closed 字段标记为 true， 表示关闭订单
            $this->order->update(['closed' => true]);
            // 循环遍历订单中的商品 SKU， 将订单中的数量加回到 SKU 的库存中
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }
            // 如果使用了优惠券，则减少优惠券使用量
            if ($this->order->couponCode) {
                $this->order->couponCode->changeUsed(false);
            }
        });
    }
}
