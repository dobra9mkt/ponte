<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;

class SalvarModeloRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'nome_cliente' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['nome' => 'nome do serviço', 'nome_cliente' => 'nome para o cliente'];
    }
}
