<?php

namespace App\Acoes;

use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;

/**
 * Devolve o item excluído ao lugar de antes (RN-82).
 */
class RestaurarDaLixeira
{
    public function cliente(Cliente $cliente): void
    {
        $cliente->restore();

        Atividade::registrar('cliente.restaurado', $cliente, ['cliente' => $cliente->empresa]);
    }

    public function frente(Frente $frente): void
    {
        $frente->restore();

        Atividade::registrar('frente.restaurada', $frente, ['frente' => $frente->nome]);
    }

    public function etapa(Etapa $etapa): void
    {
        $etapa->restore();

        Atividade::registrar('etapa.restaurada', $etapa, ['etapa' => $etapa->titulo]);
    }
}
