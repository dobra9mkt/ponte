<?php

namespace App\Policies;

use App\Models\Modelo;
use App\Models\User;

class ModeloPolicy
{
    public function viewAny(User $usuario): bool
    {
        return $usuario->ehEquipe();
    }

    public function create(User $usuario): bool
    {
        return $usuario->ehEquipe();
    }

    public function update(User $usuario, Modelo $modelo): bool
    {
        return $usuario->ehEquipe();
    }

    public function delete(User $usuario, Modelo $modelo): bool
    {
        return $usuario->ehEquipe();
    }
}
