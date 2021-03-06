<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'email_verified',
    ];

    /**
     * 需要进行类型转换的参数
     *
     * @var array
     */
    protected $casts = [
        'email_verified' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 处理用户和收货地址间的管理
     * 一个用户可以拥有多个收货地址
     *
     * @return void
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * 处理用户和收藏的商品之间的关联
     * 一个用户可以有多个收藏的商品，同时一个商品可以有多个收藏该商品的用户
     * 同时更新时间戳
     * 取出时按添加时间倒序排列
     *
     * @return void
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products')
                    ->withTimestamps()
                    ->orderBy('user_favorite_products.created_at', 'desc');
    }

    /**
     * 处理用户与购物车模型之间的关联
     * 一个用户可以拥有多个购物车模型
     *
     * @return void
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，当模型创建时触发
        static::creating(function($model) {
            // 如果模型的 avatar 字段为空，则使用默认头像
            if (! $model->avatar) {
                $model->avatar = config('app.url') . '/images/avatar_0.png';
            }
        });
    }
}
