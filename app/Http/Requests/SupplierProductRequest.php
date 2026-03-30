<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prefix = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'supplier_id' => [$prefix, 'integer', 'exists:suppliers,id'],
            'product_id'  => [$prefix, 'integer', 'exists:products,id'],
        ];
    }
}
