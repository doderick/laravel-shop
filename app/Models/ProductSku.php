<?php

namespace App\Models;

use App\Exceptions\InternalException;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    // 设置可填充字段
    protected $fillable = [
        'title',
        'description',
        'price',
        'stock',
    ];

    /**
     * 处理 SKU 与商品之间的管理
     * 一个 SKU 属于一个商品
     *
     * @return void
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 减库存
     *
     * @param [type] $amount
     * @return void
     */
    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不能小于 0');
        }

        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    /**
     * 加库存
     *
     * @param [type] $amount
     * @return void
     */
    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不能小于 0');
        }

        $this->increment('stock', $amount);
    }
}
