@extends('layouts.app')
@section('title', ($address->id ? '修改' : '新增') . '收货地址')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="text-center">
                    {{ $address->id ? '修改' : '新增' }}收货地址
                </h2>
            </div>
            <div class="panel-body">
                {{-- 输出后端报错开始 --}}
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <h4>有错误发生：</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- 输出后端报错结束 --}}
                {{-- inline-template 代表通过内联方式引入组件 --}}
                <user-addresses-create-and-edit inline-template>
                    @if ($address->id)
                        <form action="{{ route('user_addresses.update', ['user_address' => $address->id]) }}" method="post" class="form-horizontal" role="form">
                            {{ method_field('PUT') }}
                    @else
                        <form action="{{ route('user_addresses.store') }}" method="post" class="form-horizontal" role="form">
                    @endif
                        {{ csrf_field() }}
                        <select-district :init-value="{{ json_encode([$address->province, $address->city, $address->district]) }}" @change="onDistrictChanged" inline-template>
                            <div class="form-group">
                                <label class="control-label col-sm-2">省市区</label>
                                <div class="col-sm-3">
                                    <select class="form-control" v-model="provinceId">
                                        <option value="">选择省</option>
                                        <option v-for="(name, id) in provinces" :value="id">@{{ name }}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-control" v-model="cityId">
                                        <option value="">选择市</option>
                                        <option v-for="(name, id) in cities" :value="id">@{{ name }}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-control" v-model="districtId">
                                        <option value="">选择区</option>
                                        <option v-for="(name, id) in districts" :value="id">@{{ name }}</option>
                                    </select>
                                </div>
                            </div>
                        </select-district>
                        {{-- 插入 3 个隐藏字段 --}}
                        {{-- 通过 v-model 与 user-address-create-and-edit 组件里的值关联起来 --}}
                        {{-- 当组件中的值变化时，这里的值也会跟着变化 --}}
                        <input type="hidden" name="province" v-model="province">
                        <input type="hidden" name="city" v-model="city">
                        <input type="hidden" name="district" v-model="district">
                        <div class="form-group">
                            <label class="control-label col-sm-2">详细地址</label>
                            <div class="col-sm-9">
                                <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $address->address) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">邮编</label>
                            <div class="col-sm-9">
                                <input type="text" name="zip_code" id="zip_code" class="form-control" value="{{ old('zip_code', $address->zip_code) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">姓名</label>
                            <div class="col-sm-9">
                                <input type="text" name="contact_name" id="contact_name" class="form-control" value="{{ old('contact_name', $address->contact_name) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">电话</label>
                            <div class="col-sm-9">
                                <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ old('contact_phone', $address->contact_phone) }}">
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div>
                    </form>
                </user-addresses-create-and-edit>
            </div>
        </div>
    </div>
</div>
@stop