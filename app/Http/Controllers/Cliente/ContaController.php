<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmarSenhaRequest;
use App\Http\Requests\TrocarSenhaRequest;
use App\Models\Atividade;
use App\Support\Sessoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContaController extends Controller
{
    public function show(Request $request): View
    {
        return view('cliente.conta', ['usuario' => $request->user()]);
    }

    public function senha(TrocarSenhaRequest $request, Sessoes $sessoes): RedirectResponse
    {
        $usuario = $request->user();
        $usuario->forceFill(['password' => $request->validated('password')])->save();
        $sessoes->encerrarOutras($usuario);
        Atividade::registrar('senha.trocada');

        return back()->with('sucesso', __('ponte.senha.trocada'));
    }

    public function sairDeTodos(ConfirmarSenhaRequest $request, Sessoes $sessoes): RedirectResponse
    {
        $sessoes->encerrarOutras($request->user());
        Atividade::registrar('sessoes.encerradas');

        return back()->with('sucesso', __('ponte.conta.sair_todos_feito'));
    }
}
