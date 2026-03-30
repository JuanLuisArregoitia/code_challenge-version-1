<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prefix = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'order_id'   => [$prefix, 'integer', 'exists:orders,id'],
            'product_id' => [$prefix, 'integer', 'exists:products,id'],
            'quantity'   => [$prefix, 'integer', 'min:1'],
            'price'      => [$prefix, 'numeric', 'min:0'],
        ];
    }
}
