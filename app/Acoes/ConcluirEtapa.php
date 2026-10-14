<?php

namespace App\Acoes;

use App\Enums\EstadoEtapa;
use App\Models\Atividade;
use App\Models\Etapa;
use App\Servicos\Transicoes;

class ConcluirEtapa
{
    public function __invoke(Etapa $etapa): void
    {
        Transicoes::aplicar($etapa, EstadoEtapa::Concluida);
        $etapa->save();

        Atividade::registrar('etapa.concluida', $etapa, ['etapa' => $etapa->titulo]);
    }
}
