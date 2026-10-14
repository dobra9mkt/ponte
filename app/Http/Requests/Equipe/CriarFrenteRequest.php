<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CriarFrenteRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'modelo_id' => ['nullable', 'integer', Rule::exists('modelos', 'id')->whereNull('deleted_at')],
            'nome' => ['required_without:modelo_id', 'nullable', 'string', 'max:100'],
            'inicio_em' => ['nullable', 'date'],
            'prazo_final' => ['nullable', 'date', 'after_or_equal:inicio_em'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['modelo_id' => 'serviço', 'nome' => 'nome do serviço', 'inicio_em' => 'início', 'prazo_final' => 'término'];
    }
}
