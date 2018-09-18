<?php

namespace App\Admin\Controllers;

use App\Models\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UsersController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            // 页面标题
            $content->header('用户列表');

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        // 根据回调函数，在页面上用表格是的形式展示用户记录
        return Admin::grid(User::class, function (Grid $grid) {

            // 创建一个列名为 ID 的列，内容是用户的 id 字段，并且可以在前端页面点击排序
            $grid->id('ID')->sortable();

            // 根据 users 表中的字段，创建列
            $grid->name('用户名');
            $grid->email('邮箱');
            // 将 email_verified 字段进行优雅的输出
            $grid->email_verified('已验证邮箱')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->created_at('注册时间');

            // 不在页面显示「新建」 按钮
            $grid->disableCreateButton();

            $grid->actions(function ($actions) {
                // 不在每一行后面展示「查看」按钮
                $actions->disableView();
                // 不在每一行后面展示「删除」按钮
                $actions->disableDelete();
                // 不在每一行后面展示「编辑」按钮
                $actions->disableEdit();
            });
            $grid->tools(function ($tools) {
                // 禁用「批量删除」按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }
}