<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressPolicy
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
     * 用户收货地址授权策略的 own 方法
     * 判断当前登录用户是否为收货地址的所有者
     *
     * @param User $user
     * @param UserAddress $address
     * @return void
     */
    public function own(User $user, UserAddress $address)
    {
        return $address->user_id == $user->id;
    }
}
