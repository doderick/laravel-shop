<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;

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

    /**
     * 展示商品详情页
     *
     * @param Product $product
     * @param Request $request
     * @return void
     */
    public function show(Product $product, Request $request)
    {
        // 判断商品是否一斤上架，如果没有上架则抛出异常
        if (! $product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        // 用户未登录时返回的是 null，已登录时返回的是对应的用户对象
        if ($user = $request->user()) {
            // 从当前用户已收藏的商品中搜索 id 为当前商品 id 的商品
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        // 加载评论
        $reviews = OrderItem::query()
                    ->with(['order.user', 'productSku'])    // 预加载，预防 N+1
                    ->where('product_id', $product->id)     // 选定商品品
                    ->whereNotNull('reviewed_at')           // 筛选出已评价的
                    ->orderBy('reviewed_at', 'desc')        //  按评价时间倒序排列
                    ->limit(10)                             //  取出 10 条
                    ->get();

        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews,
            ]);
    }

    /**
     * 用户收藏商品的动作
     *
     * @param Product $product
     * @param Request $request
     * @return void
     */
    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return;
        }

        $user->favoriteProducts()->attach($product);

        return;
    }

    /**
     * 用户取消收藏商品的动作
     *
     * @param Product $product
     * @param Request $request
     * @return void
     */
    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return;
    }

    /**
     * 展示收藏商品列表
     *
     * @param Request $request
     * @return void
     */
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
