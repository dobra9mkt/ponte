<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;

class OrdenarFasesRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'fases' => ['required', 'array', 'min:1', 'max:200'],
            'fases.*' => ['required', 'integer', 'distinct'],
        ];
    }

    /** @return list<int> */
    public function fases(): array
    {
        return array_map(intval(...), array_values($this->validated('fases')));
    }
}
