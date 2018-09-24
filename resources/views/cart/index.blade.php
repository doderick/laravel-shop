@extends('layouts.app')
@section('title', '我的购物车')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">我的购物车</div>
            <div class="panel-body">
                @if (count($cartItems) > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>商品信息</th>
                                <th class="text-center" style="width: 120px;">单价</th>
                                <th class="text-center" style="width: 140px;">数量</th>
                                <th class="text-center" style="width: 120px;">操作</th>
                            </tr>
                        </thead>
                        <tbody class="product_list">
                            @foreach ($cartItems as $item)
                                <tr data-id="{{ $item->productSku->id }}">
                                    <td>
                                        <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
                                    </td>
                                    <td class="product_info">
                                        <div class="preview">
                                            <a href="{{ route('products.show', [$item->productSku->product_id]) }}" target="_blank">
                                                <img src="{{ $item->productSku->product->image_url }}">
                                            </a>
                                        </div>
                                        <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
                                            <span class="product_title">
                                                <a href="{{ route('products.show', [$item->productSku->product_id]) }}" target="_blank">{{ $item->productSku->product->title }}</a>
                                            </span>
                                            <span class="sku_title">{{ $item->productSku->title }}</span>
                                            @if(!$item->productSku->product->on_sale)
                                                <span class="warning">该商品已下架</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="price">￥{{ $item->productSku->price }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="text" class="form-control input-sm amount text-center"
                                                @if (! $item->productSku->product->on_sale) disabled
                                                @endif name="amount" value="{{ $item->amount }}">
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-danger btn-remove">移除</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- 输入地址开始 --}}
                    <div>
                        <form class="form-horizontal" role="form" id="order-form">
                            <div class="form-group">
                                <label class="control-label col-sm-3">选择收货地址</label>
                                <div class="col-sm-9 col-md-7">
                                    <select name="address" class="form-control">
                                        @if (! count($addresses))
                                            <option value="">你还没添加过收货地址</option>
                                        @else
                                            @foreach ($addresses as $address)
                                                <option value="{{ $address->id }}">
                                                    {{ $address->full_address }}
                                                    {{ $address->contact_name }}
                                                    {{ $address->contact_phone }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">备注</label>
                                <div class="col-sm-9 col-md-7">
                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            {{-- 优惠码开始 --}}
                                <div class="form-group">
                                    <label class="control-label col-sm-3">优惠码</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="coupon_code" id="" class="form-control">
                                        <span id="coupon_desc" class="help-block"></span>
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="button" id="btn-check-coupon" class="btn btn-success">检查</button>
                                        <button type="button" id="btn-cancel-coupon" class="btn btn-danger" style="display: none;">取消</button>
                                    </div>
                                </div>
                            {{-- 优惠码结束 --}}
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-3">
                                    <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    {{-- 输入地址结束 --}}
                @else
                    <h4>购物车空空如也</h4>
                    <a href="{{ route('root') }}" role="button" class="btn btn-primary">去逛逛</a>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('scriptsAfterJs')
<script>
    $(document).ready(function() {
        {{-- 监听 移除 按钮事件 --}}
        $('.btn-remove').click(function() {
            var id = $(this).closest('tr').data('id');
            swal({
                title: '确认要将该商品删除？',
                icon: 'warning',
                buttons: ['取消', '确认'],
                dangerMode: true,
            })
            .then(function(willDelete) {
                if (! willDelete) return;
                axios.delete('/cart/' + id)
                .then(function() {
                    location.reload();
                });
            });
        });
        {{-- 监听 全选/取消 单选框的变更事件 --}}
        $('#select-all').change(function() {
            {{-- 获取单选框的选中状态 --}}
            {{-- prop() 方法可以知道标签中是否包含某个属性，当单选框被勾选时，对应的标签就会新增一个 checked 的属性 --}}
            var checked = $(this).prop('checked');
            {{-- 获取所有 name=select 并且不带有 disabled 属性的勾选框 --}}
            {{-- 对于已经下架的商品我们不希望对应的勾选框会被选中，因此我们需要加上 :not([disabled]) 这个条件 --}}
            $('input[name=select][type=checkbox]:not([disabled])').each(function() {
                {{-- 将其勾选状态设为与目标单选框一致 --}}
                $(this).prop('checked', checked);
            });
        });
        {{-- 监听 提交订单 按钮的点击事件 --}}
        $('.btn-create-order').click(function() {
            {{-- 构建请求参数，将用户选择的地址的 id 和备注内容写入请求参数 --}}
            var req = {
                address_id: $('#order-form').find('select[name=address]').val(),
                items: [],
                remark: $('#order-form').find('textarea[name=remark]').val(),
                {{-- 提交优惠码信息 --}}
                coupon_code: $('input[name=coupon_code]').val(),
            };
            {{-- 遍历购物车中的商品 SKU --}}
            $('table tr[data-id]').each(function() {
                {{-- 获得当前行的复选框 --}}
                var $checkbox = $(this).find('input[name=select][type=checkbox]');
                {{-- 如果复选框被禁用或者没有选中则跳过 --}}
                if ($checkbox.prop('disabled') || !$checkbox.prop('checked')) {
                    return;
                }
                {{-- 获取当前行中数量输入框 --}}
                var $input = $(this).find('input[name=amount]');
                {{-- 如果用户将数量设为 0 或者不是一个数字，则也跳过 --}}
                if ($input.val() == 0 || isNaN($input.val())) {
                    return;
                }
                {{-- 把 SKU id 和数量存入请求参数数组中 --}}
                req.items.push({
                    sku_id: $(this).data('id'),
                    amount: $input.val(),
                });
            });
            {{-- 执行提交 --}}
            axios.post('{{ route('orders.store') }}', req)
                .then(function(response) {
                    swal('订单提交成功', '', 'success')
                    .then(() => {
                        location.href = '/orders/' + response.data.id;
                    });
                }, function(error) {
                    if (error.response.status === 422) {
                        {{-- http 状态码为 422 代表用户输入校验失败 --}}
                        var html = '<div>';
                        _.each(error.response.data.errors, function(errors) {
                            _.each(errors, function(error) {
                                html += error + '<br>';
                            });
                        });
                        html += '</div>';
                        swal({
                            content: $(html)[0],
                            icon: 'error',
                        });
                    } else {
                        {{-- 其它情况则是系统出错 --}}
                        swal('系统错误', '', 'error');
                    }
                });
        });
        {{-- 监听 优惠券检查 按钮的点击事件 --}}
        $('#btn-check-coupon').click(function() {
            {{-- 获取用户输入的优惠码 --}}
            var code = $('input[name=coupon_code]').val();
            {{-- 如果没有输入则弹框提示 --}}
            if (! code) {
                swal('请输入优惠码', '', 'warning');
                return;
            }
            {{-- 调用检查接口 --}}
            axios.get('/coupon_codes/' + encodeURIComponent(code))
                .then(function(response) {
                    $('#coupon_desc').text(response.data.description);
                    $('input[name=coupon_code]').prop('readonly', true);
                    $('#btn-cancel-coupon').show();
                    $('#btn-check-coupon').hide();
                }, function(error) {
                    {{-- 如果返回码是 404，说明优惠券不存在 --}}
                    if (error.response.status === 404) {
                        swal('优惠码不存在', '', 'error');
                    {{-- 如果返回码是 403，说明有其他条件不满足 --}}
                    } else if (error.response.status === 403) {
                        swal(error.response.data.msg, '', 'error');
                    {{-- 其它错误 --}}
                    } else {
                        swal('系统内部错误', '', 'error');
                    }
                })
        });
        {{-- 监听 优惠券取消 按钮的点击事件 --}}
        $('#btn-cancel-coupon').click(function() {
            $('#coupon_desc').text('');
            $('input[name=coupon_code]').prop('readonly', false);
            $('#btn-cancel-coupon').hide();
            $('#btn-check-coupon').show();
        });
    });
</script>
@stop