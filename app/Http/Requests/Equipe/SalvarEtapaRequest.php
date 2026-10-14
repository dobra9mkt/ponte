<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;

class SalvarEtapaRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:150'],
            'responsavel' => ['nullable', 'string', 'max:100'],
            'o_que_fazer' => ['nullable', 'string', 'max:2000'],
            'entrada_em' => ['nullable', 'date'],
            'termino_em' => ['nullable', 'date', 'after_or_equal:entrada_em'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'titulo' => 'nome da fase',
            'responsavel' => 'responsável',
            'o_que_fazer' => 'o que fazer',
            'entrada_em' => 'entrada',
            'termino_em' => 'término',
        ];
    }
}
