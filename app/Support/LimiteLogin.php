<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LimiteLogin
{
    private const POR_EMAIL_E_IP = 5;

    private const POR_IP = 20;

    public function __construct(private readonly Request $request) {}

    public function segundosBloqueado(string $identificador): ?int
    {
        foreach ($this->chaves($identificador) as $chave => $maximo) {
            if (RateLimiter::tooManyAttempts($chave, $maximo)) {
                return RateLimiter::availableIn($chave);
            }
        }

        return null;
    }

    public function falhou(string $identificador): void
    {
        foreach (array_keys($this->chaves($identificador)) as $chave) {
            RateLimiter::hit($chave, 60);
        }
    }

    public function limpar(string $identificador): void
    {
        RateLimiter::clear($this->chaveEmailIp($identificador));
    }

    /** @return array<string, int> */
    private function chaves(string $identificador): array
    {
        return [
            $this->chaveEmailIp($identificador) => self::POR_EMAIL_E_IP,
            'login-ip|'.$this->request->ip() => self::POR_IP,
        ];
    }

    private function chaveEmailIp(string $identificador): string
    {
        return 'login|'.sha1(Str::lower($identificador).'|'.$this->request->ip());
    }
}
