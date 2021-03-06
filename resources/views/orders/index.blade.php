@extends('layouts.app')
@section('title', '订单列表')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">订单列表</div>
            <div class="panel-body">
                @if (count($orders) > 0)
                    <ul class="list-group">
                        @foreach ($orders as $order)
                            <li class="list-group-item">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        订单号：{{ $order->no }}
                                        <span class="pull-right">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>商品信息</th>
                                                    <th class="text-center" style="width: 100px;">单价</th>
                                                    <th class="text-center" style="width: 100px;">数量</th>
                                                    <th class="text-center" style="width: 140px;">订单总价</th>
                                                    <th class="text-center" style="width: 140px;">状态</th>
                                                    <th class="text-center" style="width: 140px;">操作</th>
                                                </tr>
                                            </thead>
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
                                                                <a href="{{ route('products.show', [$item->product_id]) }}" target="_blank">
                                                                    {{ $item->product->title }}
                                                                </a>
                                                            </span>
                                                            <span class="sku-title">{{ $item->productSku->title }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="sku-price text-center">￥{{ $item->price }}</td>
                                                    <td class="sku-amount text-center">{{  $item->amount }}</td>
                                                    @if ($index === 0)
                                                        <td rowspan="{{ count($order->items) }}" class="text-center total-amount">￥{{ $order->total_amount }}</td>
                                                        <td rowspan="{{ count($order->items) }}" class="text-center">
                                                            @if ($order->paid_at)
                                                                @if ($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                                                    已支付
                                                                @else
                                                                    {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                                                @endif
                                                            @elseif ($order->closed)
                                                                已关闭
                                                            @else
                                                                未支付<br>
                                                                请于 {{ $order->created_at->addSeconds(config('app.order_ttl'))->format('H:i') }} 前完成支付<br>
                                                                否则订单将自动关闭
                                                            @endif
                                                            | {{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}
                                                        </td>
                                                        <td rowspan="{{ count($order->items) }}" class="text-center">
                                                            <a href="{{ route('orders.show', ['order' => $order->id]) }}" class="btn btn-primary btn-xs">查看订单</a>
                                                            {{-- 评价入口开始 --}}
                                                            @if ($order->ship_status == \App\Models\Order::SHIP_STATUS_RECEIVED)
                                                                <a href="{{ route('orders.review.show', ['order' => $order->id]) }}" class="btn btn-success btn-xs">
                                                                    {{ $order->reviewed ? '查看评价' : '评价' }}
                                                                </a>
                                                            @endif
                                                            {{-- 评价入口结束 --}}
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="pull-right">{{ $orders->render() }}</div>
                @else
                    <h4>你还没有在本站购买过商品</h4>
                    <a href="{{ route('root') }}" role="button" class="btn btn-primary">去逛逛</a>
                @endif
            </div>
        </div>
    </div>
</div>
@stop