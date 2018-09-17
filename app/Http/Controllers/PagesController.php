<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * 展示首页
     *
     * @return void
     */
    public function root()
    {
        return view('pages.root');
    }

    /**
     * 邮件激活提醒
     *
     * @param Request $request
     * @return void
     */
    public function emailVerifyNotice(Request $request)
    {
        return view('pages.email_verify_notice');
    }
}
