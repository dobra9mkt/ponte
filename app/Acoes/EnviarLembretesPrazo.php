<?php

namespace App\Acoes;

use App\Enums\SituacaoCard;
use App\Models\Atividade;
use App\Models\Entrega;
use App\Servicos\AvisosEntrega;
use App\Servicos\DiasUteis;

class EnviarLembretesPrazo
{
    public function __construct(private DiasUteis $diasUteis, private AvisosEntrega $avisos) {}

    /**
     * Lembrete ao cliente 1 dia útil antes do vencimento, uma vez por card (RN-74).
     */
    public function __invoke(): int
    {
        $cards = Entrega::query()
            ->with('etapa.frente.cliente')
            ->where('situacao', SituacaoCard::AguardandoCliente)
            ->whereHas('etapa.frente.cliente')
            ->whereNull('lembrete_enviado_em')
            ->whereDate('responder_ate', '>=', today())
            ->get()
            ->filter(fn (Entrega $card) => $card->responder_ate !== null
                && $this->diasUteis->somar($card->responder_ate, -1)->lte(today()));

        foreach ($cards as $card) {
            $card->forceFill(['lembrete_enviado_em' => now()])->save();
            $this->avisos->avisarCliente($card, 'lembrete');
            Atividade::registrar('card.lembrete_enviado', $card->etapa, ['etapa' => $card->etapa->titulo, 'rodada' => $card->versao]);
        }

        return $cards->count();
    }
}
