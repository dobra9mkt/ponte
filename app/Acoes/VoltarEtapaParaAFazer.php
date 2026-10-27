<?php

namespace App\Acoes;

use App\Enums\EstadoEtapa;
use App\Models\Atividade;
use App\Models\Etapa;
use App\Servicos\Transicoes;

class VoltarEtapaParaAFazer
{
    public function __invoke(Etapa $etapa): void
    {
        Transicoes::aplicar($etapa, EstadoEtapa::AFazer);
        $etapa->save();

        Atividade::registrar('etapa.voltou_a_fazer', $etapa, ['etapa' => $etapa->titulo]);
    }
}
