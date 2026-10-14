<?php

namespace App\Acoes;

use App\Enums\SituacaoCard;
use App\Models\Atividade;
use App\Models\Entrega;
use App\Models\Etapa;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdicionarAprovacao
{
    /**
     * Card em rascunho da próxima rodada; uma nova rodada já vem com os links da anterior (RN-70, RN-71, RN-76).
     */
    public function __invoke(Etapa $etapa): Entrega
    {
        return DB::transaction(function () use ($etapa) {
            $etapa = Etapa::query()->with(['frente', 'entregas'])->lockForUpdate()->findOrFail($etapa->id);

            if (! $etapa->podeReceberCard()) {
                throw ValidationException::withMessages(['card' => __('equipe.card_desatualizado')]);
            }

            $anterior = $etapa->ultimoCard();
            $card = new Entrega;
            $card->fill(['links' => $anterior?->situacao === SituacaoCard::AjustesSolicitados ? $anterior->links : []]);
            $card->forceFill([
                'etapa_id' => $etapa->id,
                'versao' => (int) ($anterior->versao ?? 0) + 1,
                'titulo' => $etapa->titulo,
                'situacao' => SituacaoCard::Rascunho,
                'gasta_revisao' => false,
                'criada_por' => auth()->id(),
            ])->save();

            Atividade::registrar('card.criado', $etapa, ['etapa' => $etapa->titulo, 'rodada' => $card->versao]);

            return $card;
        });
    }
}
