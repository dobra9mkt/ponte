<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;

class AlterarPrazoRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['responder_ate' => ['required', 'date', 'after_or_equal:today']];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['responder_ate' => 'prazo'];
    }
}
