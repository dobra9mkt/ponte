<?php

namespace App\Policies;

use App\Models\Arquivo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArquivoPolicy
{
    public function __construct(private EntregaPolicy $entregas) {}

    public function view(User $usuario, Arquivo $arquivo): Response
    {
        return $this->entregas->view($usuario, $arquivo->entregaDeOrigem());
    }
}
