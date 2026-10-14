<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

/**
 * Tentativas do código do 2FA: por pessoa+IP e por pessoa de qualquer IP (SEG-05).
 */
class LimiteDoisFatores
{
    private const POR_PESSOA_E_IP = [5, 60];

    private const POR_PESSOA = [10, 3600];

    public function __construct(private readonly Request $request, private readonly RateLimiter $limitador) {}

    public function segundosBloqueado(User $usuario): ?int
    {
        foreach ($this->chaves($usuario) as $chave => [$maximo]) {
            if ($this->limitador->tooManyAttempts($chave, $maximo)) {
                return $this->limitador->availableIn($chave);
            }
        }

        return null;
    }

    public function falhou(User $usuario): void
    {
        foreach ($this->chaves($usuario) as $chave => [, $segundos]) {
            $this->limitador->hit($chave, $segundos);
        }
    }

    public function limpar(User $usuario): void
    {
        foreach (array_keys($this->chaves($usuario)) as $chave) {
            $this->limitador->clear($chave);
        }
    }

    /** @return array<string, array{int, int}> */
    private function chaves(User $usuario): array
    {
        return [
            '2fa|'.sha1($usuario->id.'|'.$this->request->ip()) => self::POR_PESSOA_E_IP,
            '2fa-pessoa|'.$usuario->id => self::POR_PESSOA,
        ];
    }
}
