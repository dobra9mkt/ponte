<?php

namespace App\Policies;

use App\Enums\SituacaoCliente;
use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $usuario): bool
    {
        return $usuario->ehEquipe();
    }

    public function view(User $usuario, Cliente $cliente): bool
    {
        return $usuario->ehEquipe();
    }

    public function create(User $usuario): bool
    {
        return $usuario->ehEquipe();
    }

    public function update(User $usuario, Cliente $cliente): bool
    {
        return $usuario->ehEquipe() && $cliente->situacao !== SituacaoCliente::Arquivado;
    }

    public function enviarAcesso(User $usuario, Cliente $cliente): bool
    {
        return $usuario->ehEquipe() && $cliente->estaAtivo() && $cliente->usuario !== null;
    }

    public function alterarSituacao(User $usuario, Cliente $cliente): bool
    {
        return $this->update($usuario, $cliente);
    }

    public function baixarContrato(User $usuario, Cliente $cliente): bool
    {
        return $usuario->ehEquipe() && $cliente->temContrato();
    }
}
