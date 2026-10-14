<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Frente;

class SalvarFrente
{
    /**
     * As datas do serviço são independentes das datas das fases (RN-13).
     *
     * @param  array<string, mixed>  $dados
     */
    public function __invoke(Frente $frente, array $dados): void
    {
        $frente->fill($dados)->save();

        Atividade::registrar('frente.alterada', $frente, ['frente' => $frente->nome]);
    }
}
