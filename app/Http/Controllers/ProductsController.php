<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * 显示商品列表
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        // 创建查询构造器
        $bulider = Product::where('on_sale', true);
        // 判断是否提交了 search 参数，如果有就赋值给 $search
        if ($search = $request->input('search', '')) {
            $like = '%' . $search . '%';
            // 模糊搜索商品标题，商品详情，SKU 标题，SKU 描述
            $bulider->where(function ($query) use($like) {
                $query->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('skus', function ($query) use($like) {
                            $query->where('title', 'like', $like)
                                    ->orWhere('description', 'like', $like);
                        });
            });
        }

        // 判断是否提交了 order 参数，如果有就赋值给 $order
        if ($order = $request->input('order', '')) {
            // 判断是否以 _asc 或者 _desc 结果
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 判断字符串的开头是否为以下 3 个字符串之一，如果是说明参数合法
                if (in_array($m[1], ['price', 'sold_count', 'review_count'])) {
                    // 根据传入的排序值来构造排序参数
                    $bulider->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $bulider->paginate(16);

        return view('products.index', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }
}
