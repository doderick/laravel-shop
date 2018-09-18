<?php

namespace App\Models;

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
}
