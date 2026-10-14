<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrocarSenhaRequest;
use App\Models\Atividade;
use App\Servicos\RespostaDoContrato;
use App\Support\Sessoes;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ContaController extends Controller
{
    public function show(Request $request): View
    {
        return view('cliente.conta', ['usuario' => $request->user(), 'cliente' => $request->user()->cliente]);
    }

    public function contrato(Request $request, Gate $gate, RespostaDoContrato $contrato): Response
    {
        $cliente = $request->user()->cliente;
        abort_if($cliente === null, 404);
        $gate->authorize('baixarContrato', $cliente);

        return $contrato($cliente);
    }

    public function senha(TrocarSenhaRequest $request, Sessoes $sessoes): RedirectResponse
    {
        $usuario = $request->user();
        $usuario->forceFill(['password' => $request->validated('password')])->save();
        $sessoes->encerrarOutras($usuario);
        Atividade::registrar('senha.trocada');

        return back()->with('sucesso', __('ponte.senha.trocada'));
    }
}
