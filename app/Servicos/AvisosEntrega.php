<?php

namespace App\Servicos;

use App\Mail\AvisoEntrega;
use App\Models\Entrega;
use Illuminate\Support\Facades\Mail;

class AvisosEntrega
{
    public function avisarCliente(Entrega $card, string $evento): void
    {
        $frente = $card->etapa->frente;

        $this->enviar([$frente->cliente->emailDeAvisos()], $evento, route('entregas.ver', $card), $card, $frente->nomeParaCliente());
    }

    public function avisarEquipe(Entrega $card, string $evento): void
    {
        $frente = $card->etapa->frente;
        $url = route('equipe.frentes.ver', [$frente->cliente_id, $frente]).'#fase-'.$card->etapa_id;

        $this->enviar(config('ponte.emails_equipe', []), $evento, $url, $card, $frente->nome);
    }

    /** @param array<string> $destinos */
    private function enviar(array $destinos, string $evento, string $url, Entrega $card, string $nomeDaFrente): void
    {
        $frente = $card->etapa->frente;
        $dados = [
            'etapa' => $card->etapa->titulo,
            'frente' => $nomeDaFrente,
            'empresa' => $frente->cliente->empresa,
            'rodada' => $card->rodada($frente->revisoes_incluidas),
            'prazo' => (string) $card->responder_ate?->format('d/m/Y'),
        ];

        foreach ($destinos as $email) {
            // Fila database na mesma transação; o worker só enxerga após o commit.
            Mail::to($email)->queue((new AvisoEntrega($evento, $url, $dados))->beforeCommit());
        }
    }
}
