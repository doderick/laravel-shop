<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableException;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    /**
     * 展现优惠券
     *
     * @param [type] $code
     * @return void
     */
    public function show($code, Request $request)
    {
        // 判断优惠码是否存在，如果不存在抛出异常「优惠券不存在」
        if (! $record = CouponCode::where('code', $code)->first()) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        // 如果存在，调用 checkAvailable 方法检测优惠码状态
        $record->checkAvailable($request->user());

        return $record;
    }
}
