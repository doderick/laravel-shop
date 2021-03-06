@extends('layouts.app')
@section('title', '商品评价')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                商品评价
                <a href="{{ route('orders.index') }}" class="pull-right">返回订单列表</a>
            </div>
            <div class="panel-body">
                <form action="{{ route('orders.review.store', [$order->id]) }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>商品名称</td>
                                <td>评分</td>
                                <td>评价</td>
                            </tr>
                            @foreach ($order->items as $index => $item)
                                <tr>
                                    <td class="product-info">
                                        <div class="preview">
                                            <a href="{{ route('products.show', [$item->product_id]) }}" target="_blank">
                                                <img src="{{ $item->product->image_url }}">
                                            </a>
                                        </div>
                                        <div>
                                            <span class="product-title">
                                                <a href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                            </span>
                                            <span class="sku-title">{{ $item->productSku->title }}</span>
                                        </div>
                                        <input type="hidden" name="reviews[{{$index}}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="vertical-middle">
                                        {{-- 如果订单已评价则展示评分 --}}
                                        @if ($order->reviewed)
                                            <span class="rating-star-yes">{{ str_repeat('★', $item->rating) }}</span><span class="rating-star-no">{{ str_repeat('★', 5 - $item->rating) }}</span>
                                        @else
                                            <ul class="rate-area">
                                                <input type="radio" name="reviews[{{$index}}][rating]" id="5-star-{{$index}}" value="5" checked><label for="5-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" id="4-star-{{$index}}" value="4"><label for="4-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" id="3-star-{{$index}}" value="3"><label for="3-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" id="2-star-{{$index}}" value="2"><label for="2-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" id="1-star-{{$index}}" value="1"><label for="1-star-{{$index}}"></label>
                                            </ul>
                                        @endif
                                    </td>
                                    <td class="{{ $errors->has('reviews.'.$index.'.review') ? 'has-error' : '' }}">
                                        @if ($order->reviewed)
                                            {{ $item->review }}
                                        @else
                                            <textarea name="reviews[{{$index}}][review]" class="form-control"></textarea>
                                            @if ($errors->has('reviews.'.$index.'.review'))
                                                @foreach ($errors->get('reviews.'.$index.'.review') as $msg)
                                                    <span class="help-block">{{ $msg }}</span>
                                                @endforeach
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-center">
                                    @if (! $order->reviewed)
                                        <button type="submit" class="btn btn-primary center-block">提交</button>
                                    @else
                                        <a href="{{ route('orders.show', [$order->id]) }}" class="btn btn-primary">查看订单</a>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
@stop