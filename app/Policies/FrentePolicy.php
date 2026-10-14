<?php

namespace App\Policies;

use App\Enums\SituacaoFrente;
use App\Models\Frente;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FrentePolicy
{
    public function view(User $usuario, Frente $frente): Response
    {
        if ($usuario->ehEquipe()) {
            return Response::allow();
        }

        return $usuario->cliente_id === $frente->cliente_id && $frente->publicada_em !== null && $frente->situacao !== SituacaoFrente::Arquivada
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function update(User $usuario, Frente $frente): bool
    {
        return $usuario->ehEquipe() && $frente->situacao !== SituacaoFrente::Arquivada;
    }

    public function delete(User $usuario, Frente $frente): bool
    {
        return $usuario->ehEquipe();
    }

    public function restore(User $usuario, Frente $frente): bool
    {
        return $usuario->ehEquipe() && $frente->trashed() && $frente->descartado_em === null && $frente->cliente !== null;
    }
}
