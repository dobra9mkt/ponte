<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Frente;

class ExcluirFrente
{
    /**
     * Vai para a lixeira com as fases e aprovações (RN-82).
     */
    public function __invoke(Frente $frente): void
    {
        $frente->delete();

        Atividade::registrar('frente.excluida', $frente, ['frente' => $frente->nome]);
    }
}
