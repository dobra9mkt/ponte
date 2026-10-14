<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Frente;

class AlterarVisibilidadeFrente
{
    /**
     * Libera ou esconde o acompanhamento no portal, sem enviar e-mail (RN-16).
     */
    public function __invoke(Frente $frente, bool $mostrar): void
    {
        $frente->forceFill(['publicada_em' => $mostrar ? ($frente->publicada_em ?? now()) : null])->save();

        Atividade::registrar($mostrar ? 'frente.mostrada' : 'frente.ocultada', $frente, ['frente' => $frente->nome]);
    }
}
