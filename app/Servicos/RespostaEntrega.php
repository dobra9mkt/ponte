<?php

namespace App\Servicos;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCard;
use App\Models\Entrega;
use App\Models\Etapa;
use App\Models\Frente;
use Illuminate\Validation\ValidationException;

class RespostaEntrega
{
    // Sempre na mesma ordem: frente → etapa. Usar dentro de uma transação.
    public function travar(Entrega $entrega): Etapa
    {
        $frente = Frente::query()->lockForUpdate()->findOrFail($entrega->etapa->frente_id);
        $etapa = Etapa::query()->lockForUpdate()->findOrFail($entrega->etapa_id);
        $etapa->setRelation('frente', $frente);

        if (! $frente->estaAtiva() || $etapa->oculta || ! $frente->publicada_em) {
            abort(404);
        }
        $entrega->refresh();

        if (! $this->podeResponder($entrega, $etapa)) {
            throw ValidationException::withMessages(['entrega' => __('entregas.desatualizada')]);
        }

        return $etapa;
    }

    public function podeResponder(Entrega $entrega, Etapa $etapa): bool
    {
        return $etapa->frente->estaAtiva()
            && $etapa->estado === EstadoEtapa::AguardandoCliente
            && $entrega->situacao === SituacaoCard::AguardandoCliente;
    }
}
