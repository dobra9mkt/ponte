<?php

namespace App\Acoes;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCard;
use App\Models\Atividade;
use App\Models\Entrega;
use App\Models\PedidoAjuste;
use App\Servicos\ArquivosEntrega;
use App\Servicos\AvisosEntrega;
use App\Servicos\RespostaEntrega;
use App\Servicos\Transicoes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PedirAjuste
{
    public function __construct(private RespostaEntrega $resposta, private ArquivosEntrega $arquivos, private AvisosEntrega $avisos) {}

    /** @param array<string, mixed> $dados
     * @param  list<UploadedFile>  $imagens
     */
    public function __invoke(Entrega $entrega, array $dados, array $imagens): void
    {
        $arquivos = $this->arquivos->preparar($imagens);
        try {
            DB::transaction(function () use ($entrega, $dados, $arquivos) {
                $etapa = $this->resposta->travar($entrega);
                if ($entrega->ehVersaoFinal($etapa->frente->revisoes_incluidas)) {
                    throw ValidationException::withMessages(['entrega' => __('fluxo.versao_final_sem_ajustes')]);
                }
                $usadas = $etapa->frente->revisoesUsadas();
                $extra = $entrega->gasta_revisao && $usadas >= $etapa->frente->revisoes_incluidas;
                if ((int) $dados['revisoes_esperadas'] !== $usadas || $dados['modo'] !== ($extra ? 'extra' : 'revisao')) {
                    throw ValidationException::withMessages(['entrega' => __('entregas.saldo_mudou')]);
                }
                $pedido = new PedidoAjuste;
                $conta = $entrega->gasta_revisao && ! $extra;
                $pedido->forceFill([
                    'entrega_id' => $entrega->id, 'frente_id' => $etapa->frente_id, 'enviado_por' => auth()->id(),
                    'texto' => $dados['texto'], 'tipo' => $extra ? 'extra' : 'revisao',
                    'conta_como_revisao' => $conta, 'numero_revisao' => $conta ? $usadas + 1 : null,
                    'situacao_extra' => $extra ? 'aguardando_orcamento' : null,
                    'ip' => request()->ip(), 'navegador' => mb_substr((string) request()->userAgent(), 0, 255),
                ])->save();
                $this->arquivos->registrar($pedido, $arquivos);
                Transicoes::aplicar($etapa, $extra ? EstadoEtapa::AjusteExtraSolicitado : EstadoEtapa::AjustesEmAndamento);
                $etapa->save();
                $entrega->forceFill(['situacao' => SituacaoCard::AjustesSolicitados])->save();
                $entrega->rascunhos()->delete();
                Atividade::registrar($extra ? 'ajuste.extra_solicitado' : 'ajuste.enviado', $etapa, ['etapa' => $entrega->titulo, 'versao' => $entrega->versao]);
                $entrega->setRelation('etapa', $etapa);
                $this->avisos->avisarEquipe($entrega, 'ajuste');
            });
        } catch (\Throwable $erro) {
            $this->arquivos->descartar($arquivos);
            throw $erro;
        }
    }
}
