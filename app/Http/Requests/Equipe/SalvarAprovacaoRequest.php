<?php

namespace App\Http\Requests\Equipe;

use App\Rules\ArquivoPermitido;
use Illuminate\Foundation\Http\FormRequest;

class SalvarAprovacaoRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nota' => [$this->boolean('mostrar') ? 'required' : 'nullable', 'string', 'max:5000'],
            'texto_aprovar' => ['nullable', 'string', 'max:60'],
            'links' => ['array', 'max:10'],
            'links.*.rotulo' => ['required', 'string', 'max:100'],
            'links.*.url' => ['required', 'url:https', 'max:2000'],
            'arquivos' => ['array', 'max:10'],
            'arquivos.*' => ['file', 'max:51200', new ArquivoPermitido(equipe: true)],
            'remover' => ['array'],
            'remover.*' => ['integer'],
            'mostrar' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['nota' => 'o que revisar', 'texto_aprovar' => 'botão de aprovação', 'links.*.url' => 'link', 'arquivos.*' => 'anexo'];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['links.*.url.url' => 'Use um link completo, começando com https://.'];
    }

    /**
     * Linhas de link vazias são ignoradas; sem nome, o link leva o endereço do site.
     */
    protected function prepareForValidation(): void
    {
        $links = collect((array) $this->input('links', []))
            ->filter(fn ($link) => is_array($link) && filled($link['url'] ?? null))
            ->map(fn (array $link) => [
                'rotulo' => trim((string) ($link['rotulo'] ?? '')) ?: (string) (parse_url(trim((string) $link['url']), PHP_URL_HOST) ?: $link['url']),
                'url' => trim((string) $link['url']),
            ])
            ->values()
            ->all();

        $this->merge(['links' => $links]);
    }

    /** @return array<string, mixed> */
    public function dadosDoCard(): array
    {
        return $this->safe()->only(['nota', 'links', 'texto_aprovar', 'remover']);
    }
}
