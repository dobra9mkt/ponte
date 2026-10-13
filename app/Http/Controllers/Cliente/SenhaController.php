<?php

namespace App\Http\Controllers\Cliente;

use App\Enums\Papel;
use App\Http\Controllers\Controller;
use App\Http\Requests\PedirSenhaRequest;
use App\Http\Requests\RedefinirSenhaRequest;
use App\Models\Atividade;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SenhaController extends Controller
{
    public function pedir(): View
    {
        return view('cliente.senha-pedir');
    }

    public function enviar(PedirSenhaRequest $request): RedirectResponse
    {
        $email = Str::lower($request->validated('email'));
        $usuario = User::query()->where('email', $email)->where('papel', Papel::Cliente)->first();

        if ($usuario?->podeEntrar()) {
            Password::sendResetLink(['email' => $email]);
        }

        return back()->with('sucesso', __('ponte.senha.email_enviado'));
    }

    public function redefinir(Request $request, string $token): View
    {
        return view('cliente.senha-redefinir', [
            'token' => $token,
            'email' => (string) $request->query('email'),
        ]);
    }

    public function salvar(RedefinirSenhaRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        $dados['email'] = Str::lower($dados['email']);
        $usuario = User::query()->where('email', $dados['email'])->where('papel', Papel::Cliente)->first();

        if (! $usuario?->podeEntrar()) {
            return back()->withErrors(['email' => __('ponte.senha.link_invalido')]);
        }

        $status = Password::reset($dados, function (User $usuario, string $senha) {
            $usuario->forceFill(['password' => $senha, 'remember_token' => Str::random(60)])->save();
            DB::table('sessions')->where('user_id', $usuario->id)->delete();
            Atividade::registrar('senha.redefinida', usuario: $usuario);
        });

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __('ponte.senha.link_invalido')]);
        }

        return redirect()->route('entrar')->with('sucesso', __('ponte.senha.redefinida'));
    }
}
