<?php

namespace App\Acoes;

use App\Enums\SituacaoCard;
use App\Models\Atividade;
use App\Models\Entrega;
use App\Servicos\AvisosEntrega;

class AlertarPrazosVencidos
{
    public function __construct(private AvisosEntrega $avisos) {}

    /**
     * Alerta à equipe quando o prazo vence sem resposta, uma vez por card (RN-75).
     */
    public function __invoke(): int
    {
        $cards = Entrega::query()
            ->with('etapa.frente.cliente')
            ->where('situacao', SituacaoCard::AguardandoCliente)
            ->whereHas('etapa.frente.cliente')
            ->whereNull('alerta_vencido_em')
            ->whereDate('responder_ate', '<', today())
            ->get();

        foreach ($cards as $card) {
            $card->forceFill(['alerta_vencido_em' => now()])->save();
            $this->avisos->avisarEquipe($card, 'vencido');
            Atividade::registrar('card.prazo_vencido', $card->etapa, ['etapa' => $card->etapa->titulo, 'rodada' => $card->versao]);
        }

        return $cards->count();
    }
}
