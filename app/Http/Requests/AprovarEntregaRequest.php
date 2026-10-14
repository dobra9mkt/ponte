<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AprovarEntregaRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['confirmar' => ['accepted'], 'conferi' => ['sometimes', 'accepted']];
    }
}
