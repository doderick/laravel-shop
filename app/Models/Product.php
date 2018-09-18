<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // 设置可填充字段
    protected $fillable = [
        'title',
        'description',
        'image',
        'on_sale',
        'rating',
        'sold_count',
        'review_count',
        'price',
    ];

    // 声明需要进行的类型转换
    protected $casts = [
        'on_sale' => 'boolean',
    ];

    /**
     * 处理商品和 SKU 之间的关联
     * 一个商品可以有多个 SKU
     *
     * @return void
     */
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
}
