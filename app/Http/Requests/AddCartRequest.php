<?php

namespace App\Http\Requests;

use App\Models\ProductSku;

class AddCartRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (! $sku = ProductSku::find($value)) {
                        $fail('该商品不存在');
                        return;
                    }
                    if (! $sku->product->on_sale) {
                        $fail('该商品未上架');
                        return;
                    }
                    if ($sku->stock === 0) {
                        $fail('该商品已售罄');
                        return;
                    }
                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        $fail('该商品库存不足');
                        return;
                    }
                }
            ],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * 自定义参数的显示名
     *
     * @return void
     */
    public function attributes()
    {
        return [
            'amount' => '商品数量'
        ];
    }

    /**
     * 自定义验证消息提示
     *
     * @return void
     */
    public function messages()
    {
        return [
            'sku_id.required' => '请选择商品'
        ];
    }
}
