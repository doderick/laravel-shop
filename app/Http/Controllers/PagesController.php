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
}
