<?php

namespace App\Http\Controllers\Cliente;

use App\Acoes\EnviarLinkSenha;
use App\Acoes\SalvarNovaSenha;
use App\Enums\Papel;
use App\Http\Controllers\Controller;
use App\Http\Requests\PedirSenhaRequest;
use App\Http\Requests\RedefinirSenhaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SenhaController extends Controller
{
    public function pedir(): View
    {
        return view('cliente.senha-pedir');
    }

    public function enviar(PedirSenhaRequest $request, EnviarLinkSenha $enviarLink): RedirectResponse
    {
        $enviarLink($request->validated('email'), Papel::Cliente);

        return back()->with('sucesso', __('ponte.senha.email_enviado'));
    }

    public function redefinir(Request $request, string $token): View
    {
        return view('cliente.senha-redefinir', [
            'token' => $token,
            'email' => (string) $request->query('email'),
        ]);
    }

    public function salvar(RedefinirSenhaRequest $request, SalvarNovaSenha $salvarSenha): RedirectResponse
    {
        if (! $salvarSenha($request->validated(), Papel::Cliente)) {
            return back()->withErrors(['email' => __('ponte.senha.link_invalido')]);
        }

        return redirect()->route('entrar')->with('sucesso', __('ponte.senha.redefinida'));
    }
}
