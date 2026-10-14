<?php

namespace App\Acoes;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCard;
use App\Models\Aprovacao;
use App\Models\Atividade;
use App\Models\Entrega;
use App\Servicos\ArquivosEntrega;
use App\Servicos\AvisosEntrega;
use App\Servicos\RespostaEntrega;
use App\Servicos\Transicoes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AprovarEntrega
{
    public function __construct(private RespostaEntrega $resposta, private ArquivosEntrega $arquivos, private AvisosEntrega $avisos) {}

    public function __invoke(Entrega $entrega, bool $conferi): void
    {
        DB::transaction(function () use ($entrega, $conferi) {
            $etapa = $this->resposta->travar($entrega);
            if ($entrega->confirmacao && ! $conferi) {
                throw ValidationException::withMessages(['conferi' => __('entregas.confirme_teste')]);
            }
            $this->arquivos->conferir($entrega);
            $aprovacao = new Aprovacao;
            $aprovacao->forceFill([
                'entrega_id' => $entrega->id, 'user_id' => auth()->id(), 'aprovado_em' => now(),
                'hash_conteudo' => $entrega->hash_conteudo, 'confirmacao_cumprida' => $conferi,
                'ip' => request()->ip(), 'navegador' => mb_substr((string) request()->userAgent(), 0, 255),
            ])->save();
            Transicoes::aplicar($etapa, EstadoEtapa::EmProducao);
            $etapa->save();
            $entrega->forceFill(['situacao' => SituacaoCard::Aprovado])->save();
            $entrega->rascunhos()->delete();
            Atividade::registrar('entrega.aprovada', $etapa, ['etapa' => $entrega->titulo, 'versao' => $entrega->versao]);
            $entrega->setRelation('etapa', $etapa);
            $this->avisos->avisarEquipe($entrega, $entrega->ehVersaoFinal($etapa->frente->revisoes_incluidas) ? 'autorizada' : 'aprovada');
        });
    }
}
