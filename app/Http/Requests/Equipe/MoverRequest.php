<?php

namespace App\Http\Requests\Equipe;

use Illuminate\Foundation\Http\FormRequest;

class MoverRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['direcao' => ['required', 'in:subir,descer']];
    }
}
