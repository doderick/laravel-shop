@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-body product-info">
                <div class="row">
                    <div class="col-sm-5">
                        <img src="{{ $product->image_url }}" class="cover">
                    </div>
                    <div class="col-sm-7">
                        <div class="title">{{ $product->title }}</div>
                        <div class="price">
                            <label>价格</label><em>￥</em><span>{{ $product->price }}</span>
                        </div>
                        <div class="sales_and_reviews">
                            <div class="sold_count">
                                累计销量 <span class="count">{{ $product->sold_count }}</span>
                            </div>
                            <div class="review_count">
                                累计评价 <span class="count">{{ $product->review_count }}</span>
                            </div>
                            <div class="rating" title="评分 {{ $product->rating }}">
                                评分
                                <span class="count">
                                    {{ str_repeat('★', floor($product->rating)) }}
                                    {{ str_repeat('☆', 5 - floor($product->rating)) }}
                                </span>
                            </div>
                        </div>
                        <div class="skus">
                            <label>选择</label>
                            <div class="btn-group" data-toggle="buttons">
                                @foreach ($product->skus as $sku)
                                    <label
                                        class="btn btn-default sku-btn"
                                        data-price="{{ $sku->price }}"
                                        data-stock="{{ $sku->stock }}"
                                        data-toggle="tootip"
                                        data-placement="bottom"
                                        title="{{ $sku->description }}">
                                        <input type="radio" name="skus" value="{{ $sku->id }}" autocomplete="off"> {{ $sku->title }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="cart_amount">
                            <label>数量</label>
                            <input type="text" class="form-control input-sm" value="1"><span>件</span><span class="stock"></span>
                        </div>
                        <div class="buttons">
                            @if ($favored)
                                <button class="btn btn-danger btn-disfavor">取消收藏</button>
                            @else
                                <button class="btn btn-success btn-favor">❤ 收藏</button>
                            @endif
                            <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                        </div>
                    </div>
                </div>
                <div class="product-detail">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab">商品详情</a>
                        </li>
                        <li role="presentation">
                            <a href="#product-reviews-tab" aria-controls="product-review-tab" role="tab" data-toggle="tab">用户评价</a>
                        </li>
                    </ul>
                    <div class="tab-contnet">
                        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
                            {!! $product->description !!}
                        </div>
                        <div role="tabpanel" class="tab-pane" id="product-review-tab"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scriptsAfterJs')
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
        {{-- 监听 SKU 按钮的点击事件 --}}
        $('.sku-btn').click(function () {
            {{-- 根据 SKU 写入价格及库存 --}}
            $('.product-info .price span').text($(this).data('price'));
            $('.product-info .stock').text('库存：' + $(this).data('stock') + '件');
        });
        {{-- 监听收藏按钮的点击事件 --}}
        $('.btn-favor').click(function () {
            {{-- 发起一个 post ajax 请求，请求 url 通过后端的 route() 函数完成 --}}
            axios.post('{{ route('products.favor', ['product' => $product->id]) }}')
            .then(function () { {{-- 请求成功时执行 --}}
                swal('操作成功', '', 'success')
                .then(function () {
                    location.reload();
                });
            }, function(error) { {{-- 请求失败时执行 --}}
                {{-- 如果返回码是 401 表示用户没有登录 --}}
                if (error.response && error.response.status === 401) {
                    swal('请先登录', '', 'error')
                    .then(function() {
                        location.href = '{{ route('login') }}';
                    })
                {{-- 如果有其它 msg，则将 msg 提示给用户 --}}
                } else if (error.response && error.response.data.msg) {
                    swal(error.response.data.msg, '', 'error');
                {{-- 其它情况则是系统错误 --}}
                } else {
                    swal('系统错误', '', 'error');
                }
            });
        });
        {{-- 监听取消收藏按钮的点击时间 --}}
        $('.btn-disfavor').click(function () {
            axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
            .then(function () {
                swal('操作成功', '', 'success')
                .then(function () {
                    location.reload();
                });
            });
        });
        {{-- 监听加入购物车按钮 --}}
        $('.btn-add-to-cart').click(function () {
            // 请求加入购物车接口
            axios.post('{{ route('cart.add') }}', {
                sku_id: $('label.active input[name=skus]').val(),
                amount: $('.cart_amount input').val(),
            })
            .then(function () {
                swal('加入购物车成功', '', 'success')
                .then(function () {
                    location.href = '{{ route('cart.index') }}';
                });
            }, function (error) {
                if (error.response.status === 401) {
                    swal('请先登录', '', 'error')
                    .then(function() {
                        location.href = '{{ route('login') }}';
                    })
                } else if (error.response.status === 422) {
                    var html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error+'<br>';
                            })
                        });
                        html += '</div>';
                        swal({
                            content: $(html)[0],
                            icon: 'error',
                        })
                } else {
                    swal('系统错误', '', 'error');
                }
            })
        });
    });
</script>
@stop