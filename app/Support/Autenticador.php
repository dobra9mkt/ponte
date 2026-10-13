<?php

namespace App\Support;

use App\Enums\Papel;
use App\Models\Atividade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Autenticador
{
    public function __construct(private readonly Request $request, private readonly LimiteLogin $limite) {}

    public function conferir(string $email, string $senha, Papel $papel): User
    {
        $email = Str::lower(trim($email));

        if ($segundos = $this->limite->segundosBloqueado($email)) {
            throw ValidationException::withMessages([
                'email' => __('ponte.login.bloqueado', ['segundos' => $segundos]),
            ]);
        }

        $usuario = User::query()->where('email', $email)->where('papel', $papel)->first();
        $hash = $usuario->password ?? '$2y$12$'.str_repeat('a', 53);
        $senhaConfere = Hash::check($senha, $hash);

        if (! $usuario || ! $senhaConfere || ! $usuario->podeEntrar()) {
            $this->limite->falhou($email);
            Atividade::registrar('login.falhou', dados: ['area' => $papel->value], usuario: null);

            throw ValidationException::withMessages(['email' => __('ponte.login.erro')]);
        }

        $this->limite->limpar($email);

        return $usuario;
    }

    public function entrar(User $usuario, bool $lembrar = false): void
    {
        $guarda = Auth::guard('web');
        $guarda->setRememberDuration(config()->integer('ponte.lembrar_de_mim_dias') * 24 * 60);
        $guarda->login($usuario, $lembrar);
        $this->request->session()->regenerate();

        $usuario->forceFill(['ultimo_acesso_em' => now()])->save();
        Atividade::registrar('entrada', usuario: $usuario);
    }

    public function sair(): void
    {
        Auth::guard('web')->logout();
        $this->request->session()->invalidate();
        $this->request->session()->regenerateToken();
    }
}
