<?php

namespace App\Http\Requests\Equipe;

use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalvarClienteRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $cliente = $this->route('cliente');
        $usuarioId = $cliente instanceof Cliente ? $cliente->usuario?->id : null;

        return [
            'empresa' => ['required', 'string', 'max:150'],
            'contato_nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuarioId)],
            'telefone' => ['nullable', 'string', 'max:30'],
            'endereco' => ['nullable', 'string', 'max:500'],
            'observacoes' => ['nullable', 'string', 'max:5000'],
            'contrato' => ['nullable', 'file', 'mimetypes:application/pdf', 'extensions:pdf', 'max:'.config('ponte.contrato_tamanho_max_kb')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
    }
}
