<?php

namespace App\Policies;

use App\Enums\SituacaoCard;
use App\Models\Entrega;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EntregaPolicy
{
    public function __construct(private EtapaPolicy $etapas) {}

    public function view(User $usuario, Entrega $entrega): Response
    {
        $etapa = $entrega->etapa;

        if ($etapa === null || (! $usuario->ehEquipe() && $entrega->publicada_em === null)) {
            return Response::denyAsNotFound();
        }

        return $this->etapas->view($usuario, $etapa);
    }

    public function responder(User $usuario, Entrega $entrega): Response
    {
        return $usuario->ehCliente() && $this->view($usuario, $entrega)->allowed()
            ? Response::allow() : Response::denyAsNotFound();
    }

    public function editar(User $usuario, Entrega $entrega): bool
    {
        return $this->etapas->update($usuario, $entrega->etapa) && $entrega->situacao === SituacaoCard::Rascunho;
    }

    public function alterarPrazo(User $usuario, Entrega $entrega): bool
    {
        return $this->etapas->update($usuario, $entrega->etapa) && $entrega->situacao === SituacaoCard::AguardandoCliente;
    }
}
