<?php

namespace App\Policies;

use App\Models\ModeloEtapa;
use App\Models\User;

class ModeloEtapaPolicy
{
    public function update(User $usuario, ModeloEtapa $etapa): bool
    {
        return $usuario->ehEquipe();
    }

    public function delete(User $usuario, ModeloEtapa $etapa): bool
    {
        return $usuario->ehEquipe();
    }
}
