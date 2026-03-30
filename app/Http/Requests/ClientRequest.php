<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prefix = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name'     => [$prefix, 'string', 'max:255'],
            'lastname' => [$prefix, 'string', 'max:255'],
            'email'    => [$prefix, 'string', 'email', 'max:255'],
        ];
    }
}
