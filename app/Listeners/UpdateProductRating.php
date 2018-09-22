<?php

namespace App\Listeners;

// use DB;
use App\Models\OrderItem;
use App\Events\OrderReviewed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateProductRating implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderReviewed  $event
     * @return void
     */
    public function handle(OrderReviewed $event)
    {
        $items = $event->getOrder()->items()->with(['product'])->get();
        foreach ($items as $item) {
            $result = OrderItem::query()
                                    ->where('product_id', $item->product_id)
                                    ->whereHas('order', function ($query) {
                                        $query->where('ship_status', \App\Models\Order::SHIP_STATUS_RECEIVED);
                                    })
                                    ->first([
                                        \DB::raw('count(*) as review_count'),
                                        \DB::raw('avg(rating) as rating')
                                    ]);
            // 更新商品的评价和评价数
            $item->product->update([
                'rating'       => $result->rating,
                'review_count' => $result->review_count,
            ]);
        }
    }
}
