<?php

namespace App\Acoes;

use App\Models\Entrega;
use App\Models\RascunhoAjuste;
use App\Servicos\RespostaEntrega;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalvarRascunhoAjuste
{
    public function __construct(private RespostaEntrega $resposta) {}

    public function __invoke(Entrega $entrega, ?string $texto, int $revisao): RascunhoAjuste
    {
        return DB::transaction(function () use ($entrega, $texto, $revisao) {
            $this->resposta->travar($entrega);
            $rascunho = $entrega->rascunhos()->where('user_id', auth()->id())->first() ?? new RascunhoAjuste;
            if ((int) $rascunho->revisao !== $revisao) {
                throw ValidationException::withMessages(['rascunho' => __('entregas.rascunho_conflito')]);
            }
            $rascunho->forceFill([
                'entrega_id' => $entrega->id, 'user_id' => auth()->id(),
                'texto' => $texto, 'revisao' => $revisao + 1,
            ])->save();

            return $rascunho;
        });
    }
}
