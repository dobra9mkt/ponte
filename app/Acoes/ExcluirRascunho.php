<?php

namespace App\Acoes;

use App\Enums\SituacaoCard;
use App\Models\Atividade;
use App\Models\Entrega;
use Illuminate\Validation\ValidationException;

class ExcluirRascunho
{
    public function __invoke(Entrega $card): void
    {
        if ($card->situacao !== SituacaoCard::Rascunho) {
            throw ValidationException::withMessages(['card' => __('equipe.card_desatualizado')]);
        }

        $card->delete();

        Atividade::registrar('card.rascunho_excluido', $card->etapa, ['etapa' => $card->etapa->titulo, 'rodada' => $card->versao]);
    }
}
