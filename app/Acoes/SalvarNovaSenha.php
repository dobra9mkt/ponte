<?php

namespace App\Acoes;

use App\Enums\Papel;
use App\Models\Atividade;
use App\Models\User;
use App\Notifications\SenhaRedefinida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SalvarNovaSenha
{
    /**
     * @param  array{token: string, email: string, password: string}  $dados
     */
    public function __invoke(array $dados, Papel $papel): bool
    {
        $dados['email'] = Str::lower($dados['email']);
        $usuario = User::query()->where('email', $dados['email'])->where('papel', $papel)->first();

        if (! $usuario?->podeRedefinirSenha()) {
            return false;
        }

        $status = Password::reset($dados, function (User $usuario, string $senha) {
            $usuario->forceFill(['password' => $senha, 'remember_token' => Str::random(60)])->save();
            DB::table('sessions')->where('user_id', $usuario->id)->delete();
            Atividade::registrar('senha.redefinida', usuario: $usuario);
        });

        if ($status !== Password::PASSWORD_RESET) {
            return false;
        }

        if ($usuario->ehEquipe()) {
            $usuario->notify(new SenhaRedefinida);
        }

        return true;
    }
}
