<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\CouponCodeUnavailableException;
use Carbon\Carbon;

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

    public function checkAvailable(User $user, $orderAmount = null)
    {
        // 如果优惠码没有启用，优惠券不存在
        if (! $this->enabled) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }
        // 如果优惠券总量 小于等于 优惠券已兑换的数量，优惠券已兑换完
        if ($this->total - $this->used <= 0) {
            throw new CouponCodeUnavailableException('该优惠券已被兑换完');
        }
        // 如果设置了优惠券开始使用的时间，且该时间 大于 当前时间，还没有到优惠券可以使用的时间
        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券现在还不能使用');
        }
        // 如果设置了优惠券过期时间，且该时间 小于 当前时间，优惠券已过期
        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券已过期');
        }
        // 如果设置了最小可使用订单金额，且当前订单金额 小于 该金额，不满足使用条件
        if (! is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new CouponCodeUnavailableException('订单金额不满足该使用该优惠券的最低金额');
        }

        // 判断用户是否使用过该优惠券
        $used = Order::where('user_id', $user->id)
                        ->where('coupon_code_id', $this->id)
                        ->where(function($query) {
                            $query->where(function($query) {
                                $query->whereNull('paid_at')
                                        ->where('closed', false);
                            })->orWhere(function($query) {
                                $query->whereNotNull('paid_at')
                                        ->where('refund_status', Order::REFUND_STATUS_PENDING);
                            });
                        })
                        ->exists();
        if ($used) {
            throw new CouponCodeUnavailableException('你已经使用过这张优惠券了');
        }
    }

    /**
     * 计算优惠后金额
     *
     * @param [type] $orderAmount
     * @return void
     */
    public function getAdjustedPrice($orderAmount)
    {
        // 如果是固定金额
        if ($this->type === self::TYPE_FIXED) {
            // 为了保证系统健壮性，我们需要订单金额最少为 0.01 元
            return max(0.01, $orderAmount - $this->value);
        }
        // 如果是比例折扣
        if ($this->type === self::TYPE_PERCENT) {
            return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
        }
    }

    /**
     * 更新优惠券使用量
     *
     * @param boolean $increase
     * @return void
     */
    public function changeUsed($increase = true)
    {
        // 传入 true 代表新增用量，否则是减少用量
        if ($increase) {
            // 检查当前用量是否已经超过总量
            return $this->newQuery()
                        ->where('id', $this->id)
                        ->where('used', '<', $this->total)
                        ->increment('used');
        } else {
            return $this->decrement('used');
        }
    }
}
