<?php

namespace App\Acoes;

use App\Enums\Papel;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class EnviarLinkSenha
{
    public function __invoke(string $email, Papel $papel): void
    {
        $email = Str::lower($email);
        $usuario = User::query()->where('email', $email)->where('papel', $papel)->first();

        if ($usuario?->podeRedefinirSenha()) {
            Password::sendResetLink(['email' => $email]);
        }
    }
}
