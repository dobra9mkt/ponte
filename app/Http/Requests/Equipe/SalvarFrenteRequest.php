<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;

class SalvarFrenteRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'nome_cliente' => ['nullable', 'string', 'max:100'],
            'inicio_em' => ['nullable', 'date'],
            'prazo_final' => ['nullable', 'date', 'after_or_equal:inicio_em'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['nome' => 'nome do serviço', 'nome_cliente' => 'nome para o cliente', 'inicio_em' => 'início', 'prazo_final' => 'término'];
    }
}
