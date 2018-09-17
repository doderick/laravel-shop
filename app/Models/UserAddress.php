<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    // 设置可填充字段
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip_code',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];
    protected $dates = ['last_used_at'];

    /**
     * 处理收货地址与用户之间的关联
     * 一个收货地址属于一个用户
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 访问器
     * 可以获得完整地址
     *
     * @return void
     */
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
