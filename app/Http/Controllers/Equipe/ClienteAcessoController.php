<?php

namespace App\Http\Controllers\Equipe;

use App\Acoes\AlterarSituacaoCliente;
use App\Acoes\EnviarAcesso;
use App\Enums\SituacaoCliente;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Servicos\RespostaDoContrato;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ClienteAcessoController extends Controller
{
    public function enviar(Cliente $cliente, EnviarAcesso $enviar): RedirectResponse
    {
        $enviar($cliente);

        return back()->with('sucesso', __('ponte.clientes.acesso_enviado', ['email' => $cliente->email]));
    }

    public function desativar(Cliente $cliente, AlterarSituacaoCliente $alterar): RedirectResponse
    {
        $alterar($cliente, SituacaoCliente::Desativado);

        return back()->with('sucesso', __('ponte.clientes.desativado'));
    }

    public function reativar(Cliente $cliente, AlterarSituacaoCliente $alterar): RedirectResponse
    {
        $alterar($cliente, SituacaoCliente::Ativo);

        return back()->with('sucesso', __('ponte.clientes.reativado'));
    }

    public function contrato(Cliente $cliente, RespostaDoContrato $contrato): Response
    {
        return $contrato($cliente);
    }
}
