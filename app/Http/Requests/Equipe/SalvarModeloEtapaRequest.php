<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;

class SalvarModeloEtapaRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:150'],
            'responsavel' => ['nullable', 'string', 'max:100'],
            'o_que_fazer' => ['nullable', 'string', 'max:2000'],
            'duracao_dias_uteis' => ['nullable', 'integer', 'min:1', 'max:250'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'titulo' => 'nome da fase',
            'responsavel' => 'responsável',
            'o_que_fazer' => 'o que fazer',
            'duracao_dias_uteis' => 'duração',
        ];
    }
}
