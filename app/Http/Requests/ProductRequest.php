<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prefix = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name'        => [$prefix, 'string', 'max:255'],
            'description' => [$prefix, 'string', 'max:1000'],
            'price'       => [$prefix, 'numeric', 'min:0'],
            'quantity'    => [$prefix, 'integer', 'min:0'],
        ];
    }
}
