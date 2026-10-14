<?php

namespace App\Http\Requests;

use App\Rules\ArquivoPermitido;
use Illuminate\Foundation\Http\FormRequest;

class PedirAjusteRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'texto' => ['required', 'string', 'max:10000'],
            'imagens' => ['array', 'max:5'],
            'imagens.*' => ['file', 'max:51200', new ArquivoPermitido],
        ];
    }
}
