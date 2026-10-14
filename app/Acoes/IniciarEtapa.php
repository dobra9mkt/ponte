<?php

namespace App\Acoes;

use App\Enums\EstadoEtapa;
use App\Models\Atividade;
use App\Models\Etapa;
use App\Servicos\Transicoes;

class IniciarEtapa
{
    public function __invoke(Etapa $etapa): void
    {
        Transicoes::aplicar($etapa, EstadoEtapa::EmProducao);
        $etapa->save();

        Atividade::registrar('etapa.iniciada', $etapa, ['etapa' => $etapa->titulo]);
    }
}
