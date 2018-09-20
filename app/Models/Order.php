<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // 定义退款状态常量
    const REFUND_STATUS_PENDING    = 'pending';
    const REFUND_STATUS_APPLIED    = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS    = 'success';
    const REFUND_STATUS_FAILED     = 'failed';
    // 定义对应的中文描述
    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    // 定义物流状态常量
    const SHIP_STATUS_PENDING   = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED  = 'received';
    // 定义对应的中文描述
    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    // 设置可填充字段
    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    // 声明数据类型转换
    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    // 声明需要转换为日期的属性
    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库前触发
        static::cretaing(function ($model) {
            // 如果模型的 no 字段为空
            if (! $model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (! $model->no) {
                    return false;
                }
            }
        });
    }

    /**
     * 处理订单和用户之间的关联
     * 一个订单属于一个用户
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 处理订单与订单项目之间的关联
     * 一个订单可以有多个订单项
     *
     * @return void
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * 生成订单号
     *
     * @return void
     */
    public static function findAvailableNo()
    {
        // 订单流水号前缀，日期时间
        $prefix = date('YmdHis');
        // 生成随机的 6 位数字
        // 为避免死循环，只生成 10 次
        for ($i = 0; $i < 10; $i++) {
            $no = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在，如果不存在，返回订单流水号
            if (! static::where('no', $no)->exists()) {
                return $no;
            }
        }
        // 如果 10 次循环不能获得订单流水号，记录到日志中，并返回
        \Log::warning('find order no failed');

        return false;
    }
}
