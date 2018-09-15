<?php

/**
 * 将路由名转换为样式类名
 *
 * @return void
 */
function routeToClass()
{
    return str_replace('.', '-', Route::currentRouteName());
}