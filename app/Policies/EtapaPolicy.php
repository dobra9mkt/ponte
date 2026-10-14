<?php

namespace App\Policies;

use App\Enums\EstadoEtapa;
use App\Models\Etapa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EtapaPolicy
{
    public function __construct(private FrentePolicy $frentes) {}

    public function view(User $usuario, Etapa $etapa): Response
    {
        if ($usuario->ehEquipe()) {
            return Response::allow();
        }

        $frente = $etapa->frente;

        return ! $etapa->interna && ! $etapa->oculta && $etapa->estado !== EstadoEtapa::AFazer
            && $frente !== null && $this->frentes->view($usuario, $frente)->allowed()
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function update(User $usuario, Etapa $etapa): bool
    {
        return $this->frentes->update($usuario, $etapa->frente);
    }

    public function delete(User $usuario, Etapa $etapa): bool
    {
        return $this->update($usuario, $etapa);
    }

    public function restore(User $usuario, Etapa $etapa): bool
    {
        return $usuario->ehEquipe() && $etapa->trashed() && $etapa->descartado_em === null && $etapa->frente?->cliente !== null;
    }

    public function iniciar(User $usuario, Etapa $etapa): bool
    {
        return $this->update($usuario, $etapa) && $etapa->estado === EstadoEtapa::AFazer;
    }

    public function concluir(User $usuario, Etapa $etapa): bool
    {
        return $this->update($usuario, $etapa) && $etapa->podeSerConcluida();
    }

    public function adicionarAprovacao(User $usuario, Etapa $etapa): bool
    {
        return $this->update($usuario, $etapa) && $etapa->podeReceberCard();
    }
}
