<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    // 设置可填充字段
    protected $fillable = [
        'amount',
        'price',
        'rating',
        'review',
        'review_at',
    ];

    // 声明需要转换为日期的属性
    protected $dates = [
        'reviewed_at',
    ];

    // 禁用时间戳更新
    public $timestamps = false;

    /**
     * 处理订单项目和产品之间的关联
     * 一个订单项目属于一个产品
     *
     * @return void
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 处理订单项目和产品 SKU 之间的关联
     * 一个订单项目属于一个产品 SKU
     *
     * @return void
     */
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    /**
     * 处理订单项目与订单之间的关联
     * 一个订单项目属于一个订单
     *
     * @return void
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
