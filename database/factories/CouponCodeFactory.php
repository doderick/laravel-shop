<?php

use Faker\Generator as Faker;

$factory->define(App\Models\CouponCode::class, function (Faker $faker) {
    // 随机获得一个优惠券类型
    $type  = $faker->randomElement(array_keys(App\Models\CouponCode::$typeMap));
    // 根据取得的类型生成对应的折扣
    $value = $type === App\Models\CouponCode::TYPE_FIXED ? random_int(1, 200) : random_int(1, 50);

    // 如果是固定金额，则最低订单金额必须要笔优惠金额高 0.01 元
    // 如果是百分比折扣，则有 50% 的概率不需要最低订单金额
    if ($type === App\Models\CouponCode::TYPE_FIXED) {
        $minAmount = $value + 0.01;
    } else {
        if (random_int(0, 100) < 50) {
            $minAmount = 0;
        } else {
            $minAmount = random_int(100, 1000);
        }
    }

    return [
        'name'       => implode(' ', $faker->words),
        'code'       => App\Models\CouponCode::findAvailableCode(),
        'type'       => $type,
        'value'      => $value,
        'total'      => 1000,
        'used'       => 0,
        'min_amount' => $minAmount,
        'not_before' => null,
        'not_after'  => null,
        'enabled'    => true,
    ];
});
