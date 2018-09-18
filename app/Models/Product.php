<?php

namespace App\Models;

use Illuminate\Support\Str;
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

    /**
     * 图片绝对路径访问器
     *
     * @return 商品图片的绝对路径
     */
    public function getImageUrlAttribute()
    {
        // 先判断 image 字段是否为完整的 url
        // 如果是完整的 url 直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        // 如果不是完整的 url ，则拼接完整的 url
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
