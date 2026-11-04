<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Http\Request;

class DispositivoConfiavel
{
    private const COOKIE = 'ponte_dispositivo';

    private const DIAS = 30;

    public function __construct(private readonly Request $request, private readonly QueueingFactory $cookies) {}

    public function lembrar(User $usuario): void
    {
        $expira = now()->addDays(self::DIAS)->getTimestamp();
        $valor = implode('|', [$usuario->id, $expira, $this->assinatura($usuario, $expira)]);

        $this->cookies->queue($this->cookies->make(
            self::COOKIE,
            $valor,
            self::DIAS * 24 * 60,
            '/'.config('ponte.caminho_equipe'),
        ));
    }

    public function reconhece(User $usuario): bool
    {
        [$id, $expira, $assinatura] = array_pad(explode('|', (string) $this->request->cookie(self::COOKIE), 3), 3, '');

        if ($id !== (string) $usuario->id || (int) $expira <= now()->getTimestamp()) {
            return false;
        }

        return hash_equals($this->assinatura($usuario, (int) $expira), $assinatura);
    }

    /** Senha e segredo do 2FA entram na assinatura: trocar qualquer um deles invalida todos os aparelhos. */
    private function assinatura(User $usuario, int $expira): string
    {
        return hash_hmac(
            'sha256',
            implode('|', [$usuario->id, $expira, $usuario->password, $usuario->two_factor_secret]),
            config()->string('app.key'),
        );
    }
}
