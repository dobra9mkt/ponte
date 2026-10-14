<?php

namespace App\Acoes;

use App\Enums\SituacaoCard;
use App\Models\Atividade;
use App\Models\Entrega;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

class AlterarPrazo
{
    /**
     * O lembrete e o alerta voltam a valer para a nova data (RN-73).
     */
    public function __invoke(Entrega $card, CarbonInterface $prazo): void
    {
        if ($card->situacao !== SituacaoCard::AguardandoCliente) {
            throw ValidationException::withMessages(['card' => __('equipe.card_desatualizado')]);
        }

        $anterior = $card->responder_ate?->format('d/m/Y');
        $card->forceFill(['responder_ate' => $prazo, 'lembrete_enviado_em' => null, 'alerta_vencido_em' => null])->save();

        Atividade::registrar('card.prazo_alterado', $card->etapa, [
            'etapa' => $card->etapa->titulo, 'de' => $anterior, 'para' => $prazo->format('d/m/Y'),
        ]);
    }
}
