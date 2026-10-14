<?php

namespace App\Http\Controllers\Equipe;

use App\Models\Etapa;
use Illuminate\Http\RedirectResponse;

trait VoltaParaFase
{
    private function voltarParaFase(Etapa $etapa, ?string $mensagem = null): RedirectResponse
    {
        $resposta = redirect()->to(
            route('equipe.frentes.ver', [$etapa->frente->cliente_id, $etapa->frente_id]).'#fase-'.$etapa->id
        );

        return $mensagem ? $resposta->with('sucesso', $mensagem) : $resposta;
    }
}
