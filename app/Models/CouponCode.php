<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    // 用常量的方式定义支持的优惠券类型
    const TYPE_FIXED   = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED   => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];

    // 设置可填充字段
    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];
    // 设置数据类型装换
    protected $casts = [
        'enabled' => 'boolean',
    ];
    // 声明需要转换为日期的属性
    protected $dates = [
        'not_before',
        'not_after',
    ];

    // 追加到模型数组表单的访问器
    protected $appends = [
        'description',
    ];

    /**
     * 生成优惠码
     *
     * @param integer $length
     * @return void
     */
    public static function findAvailableCode($length = 16)
    {
        do {
            // 生成指定长度的随机字符串，并转换为大写
            // 如果生成的优惠码已存在，则继续循环，生成新的优惠码
            $code = strtoupper(Str::random($length));
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    /**
     * 优化优惠券的显示
     *
     * @return void
     */
    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = '满' . str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str . '优惠' . str_replace('.00', '', $this->value) . '%';
        }

        return $str . '减' . str_replace('.00', '', $this->value);
    }
}
