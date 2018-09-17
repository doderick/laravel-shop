<?php

namespace App\Http\Controllers;

use Mail;
use Cache;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\EmailVerificationNotification;

class EmailVerificationController extends Controller
{
    /**
     * 验证用户邮箱
     *
     * @param Request $request
     * @return void
     */
    public function verify(Request $request)
    {
        // 从 url 中获取 'email' 和 'token' 两个参数
        $email = $request->input('email');
        $token = $request->input('token');
        // 如果有一个为空说明不是一个合法的验证链接，直接抛出异常
        if (! $email || ! $token) {
            throw new Exception('验证链接不正确！');
        }
        // 从缓存中读取数据，将 从 url 中的获取的 'token' 与缓存中的值对比
        // 如果缓存不存在或者返回的值与 url 中的不一致，则抛出异常
        if ($token != Cache::get('email_verification_' . $email)) {
            throw new Exception('验证链接不正确或已过期！');
        }

        // 根据邮箱从数据库中取出对应的用户
        // 通常能通过 token 校验的情况下不可能出现用户不存在
        // 为了代码的健壮行，仍需做这个判断
        if (! $user = User::where('email', $email)->first()) {
            throw new Exception('用户不存在！');
        }
        // 将制定的 key 从缓存中删除
        Cache::forget('email_verification_' . $email);
        // 将对应用户的 'emial_verified' 字段改为 true
        $user->update(['email_verified' => true]);

        // 告知用户邮箱验证通过
        return view('pages.success', ['msg' => '邮箱验证成功！']);
    }

    public function send(Request $request)
    {
        $user = $request->user();
        // 判断用户是否已经激活
        if ($user->email_verified) {
            throw new Exception('您已经验证过邮箱了！');
        }
        // 使用 notify 方法来发送通知
        $user->notify(new EmailVerificationNotification());

        return view('pages.success', ['msg' => '邮件发送成功！']);
    }
}
