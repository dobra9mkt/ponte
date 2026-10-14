<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ArquivoPermitido implements ValidationRule
{
    public const TIPOS = [
        'application/pdf' => 'pdf', 'image/png' => 'png', 'image/jpeg' => 'jpg',
        'image/webp' => 'webp', 'image/svg+xml' => 'svg',
        'application/zip' => 'zip', 'application/x-zip-compressed' => 'zip',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'video/mp4' => 'mp4',
    ];

    public function __construct(private bool $equipe = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail(__('entregas.arquivo_invalido'));

            return;
        }
        $nome = $value->getClientOriginalName();
        $tipoAceito = $this->equipe || in_array($value->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true);
        if (! $tipoAceito || str_contains($nome, '..') || preg_match('/[\/\\\\\x00-\x1f]/', $nome)) {
            $fail(__('entregas.arquivo_invalido'));
        }
    }
}
