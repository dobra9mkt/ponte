<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalvarRascunhoRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['texto' => ['nullable', 'string', 'max:10000'], 'revisao' => ['required', 'integer', 'min:0']];
    }
}
