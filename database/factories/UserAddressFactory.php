<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserAddress::class, function (Faker $faker) {
    // 定义省、市、区的取值
    $addresses = [
        ['上海市', '市辖区', '闸北区'],
        ['江苏省', '常州市', '武进区'],
        ['广东省', '广州市', '越秀区'],
        ['陕西省', '西安市', '未央区'],
        ['田纳西州', '孟菲斯市', '奥利夫布兰奇'],
    ];
    // 随机取出一个地址
    $address = $faker->randomElement($addresses);

    return [
        'province'      => $address[0],
        'city'          => $address[1],
        'district'      => $address[2],
        'address'       => sprintf('第%d街道第%d号', $faker->randomNumber(2), $faker->randomNumber(3)),
        'zip_code'      => $faker->postcode,
        'contact_name'  => $faker->name,
        'contact_phone' => $faker->phoneNumber,
    ];
});
