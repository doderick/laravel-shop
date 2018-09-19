<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // 设置可填充字段
    protected $fillable = ['amount'];

    // 禁用时间戳
    public $timestamps = false;

    /**
     * 处理购物车与用户之间的关联
     * 一个购物车模型属于一个用户
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 处理购物车与商品 SKU 之间的关联
     * 一个购物车模型属于一个商品 SKU
     *
     * @return void
     */
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
