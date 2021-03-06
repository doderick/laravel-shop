@extends('layouts.app')
@section('title', '订单详情')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>订单详情</h4>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>商品信息</th>
                            <th class="text-center">单价</th>
                            <th class="text-center">数量</th>
                            <th class="text-right item-amount">小计</th>
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
                            <td class="vertical-middle text-center sku-price">￥{{ $item->price }}</td>
                            <td class="vertical-middle text-center sku-amount">{{ $item->amount }}</td>
                            <td class="vertical-middle text-right item-amount">￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                    <tr><td colspan="4"></td></tr>
                </table>
                <div class="order-bottom">
                    <div class="order-info">
                        <div class="line">
                            <div class="line-label">
                                收货地址：
                            </div>
                            <div class="line-value">
                                {{ join(' ', $order->address) }}
                            </div>
                        </div>
                        <div class="line">
                            <div class="line-label">
                                订单备注：
                            </div>
                            <div class="line-value">
                                {{ $order->remark ?: '-' }}
                            </div>
                        </div>
                        <div class="line">
                            <div class="line-label">
                                订单编号：
                            </div>
                            <div class="line-value">
                                {{ $order->no }}
                            </div>
                        </div>
                        {{-- 输出物流状态 --}}
                        <div class="line">
                            <div class="line-label">
                                物流状态：
                            </div>
                            <div class="line-value">
                                {{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}
                            </div>
                        </div>
                        {{-- 如果有物流信息，则展示 --}}
                        @if ($order->ship_data)
                            <div class="line">
                                <div class="line-label">物流信息：</div>
                                <div class="line-value">
                                    {{ $order->ship_data['express_company'] }}
                                    {{ $order->ship_data['express_no'] }}
                                </div>
                            </div>
                        @endif
                        {{-- 订单已支付，且退款状态不是未退款时展示退款信息 --}}
                        @if ($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
                            <div class="line">
                                <div class="line-label">退款状态：</div>
                                <div class="line-value">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">退款理由：</div>
                                <div class="line-value">{{ $order->extra['refund_reason'] }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="order-summary text-right">
                        {{-- 显示优惠信息开始 --}}
                        @if ($order->couponCode)
                            <div class="text-primary">
                                <span>优惠信息：</span>
                                <div class="value">{{ $order->couponCode->description }}</div>
                            </div>
                        @endif
                        {{-- 显示优惠信息结束 --}}
                        <div class="total-amount">
                            <span>订单总价：</span>
                            <div class="value">￥{{ $order->total_amount }}</div>
                        </div>
                        <div>
                            <span>订单状态：</span>
                            <div class="value">
                                @if ($order->paid_at)
                                    @if ($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                        已支付
                                    @else
                                        {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                    @endif
                                @elseif ($order->closed)
                                    已关闭
                                @else
                                    未支付
                                @endif
                            </div>
                        </div>
                        {{-- 展示拒绝退款理由 --}}
                        @if (isset($order->extra['refund_disagree_reason']))
                            <div>
                                <span>拒绝退款理由：</span>
                                <div class="value">{{ $order->extra['refund_disagree_reason'] }}</div>
                            </div>
                        @endif
                        {{-- 支付按钮开始 --}}
                        @if (! $order->paid_at && ! $order->closed)
                            <div class="payment-buttons">
                                <a href="{{ route('payment.alipay', ['order' => $order->id]) }}" class="btn btn-primary btn-sm">支付宝支付</a>
                            </div>
                        @endif
                        {{-- 支付按钮结束 --}}
                        {{-- 如果订单已发货则展示收货按钮 --}}
                        @if ($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
                            <div class="receive-button">
                                <form action="{{ route('orders.received', [$order->id]) }}" method="post">
                                    {{ csrf_field() }}
                                    <button type="button" id="btn-receive" class="btn btn-sm btn-success">确认收货</button>
                                </form>
                            </div>
                        @endif
                        {{-- 订单已支付，且退款状态是未退款时展示申请退款按钮 --}}
                        @if ($order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                            <div class="refund-button">
                                <button id="btn-apply-refund" class="btn btn-sm btn-danger">申请退款</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scriptsAfterJs')
<script>
    $(document).ready(function() {
        {{-- 监听收货按钮的点击事件 --}}
        $('#btn-receive').click(function() {
            swal({
                title: '确认已经收到商品了？',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
                buttons: ['取消','确认收货'],
            })
            .then(function(ret) {
                if (! ret) {
                    return;
                }
                {{-- ajax 提交确认操作 --}}
                axios.post('{{ route('orders.received', [$order->id]) }}')
                .then(function() {
                    location.reload();
                });
            });
        });
        {{-- 监听退款按钮点击事件 --}}
        $('#btn-apply-refund').click(function() {
            swal({
                text: '请输入退款理由',
                content: 'input',
            }).then(function(input) {
                if (! input) {
                    swal ('退款理由不可空', '', 'error');
                    return;
                }
                // 请求退款接口
                axios.post('{{ route('orders.apply_refund', [$order->id]) }}', {reason: input})
                .then(function() {
                    swal('申请退款成功', '', 'success')
                    .then(function() {
                        location.reload();
                    });
                });
            });
        });
    });
</script>
@stop