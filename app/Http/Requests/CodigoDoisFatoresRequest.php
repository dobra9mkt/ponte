<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CodigoDoisFatoresRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'codigo' => ['nullable', 'required_without:codigo_recuperacao', 'string', 'max:10'],
            'codigo_recuperacao' => ['nullable', 'string', 'max:64'],
        ];
    }
}
