<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 用户订单授权策略的 own 方法
     * 判断当前登录用户是否为订单的所有者
     *
     * @param User $user
     * @param UserAddress $address
     * @return void
     */
    public function own(User $user, Order $order)
    {
        return $order->user_id == $user->id;
    }
}
