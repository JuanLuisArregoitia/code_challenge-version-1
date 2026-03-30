<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prefix = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'order_number' => [$prefix, 'string', 'max:255'],
            'status_id'    => [$prefix, 'integer', 'min:0', 'max:255'],
            'client_id'    => [$prefix, 'integer', 'exists:clients,id'],
        ];
    }
}
