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

    /**
     * Um pacote de ajustes por rodada; na versão final o cliente só autoriza (RN-21, RN-71, RN-76).
     *
     * @param  list<UploadedFile>  $imagens
     */
    public function __invoke(Entrega $entrega, string $texto, array $imagens): void
    {
        $arquivos = $this->arquivos->preparar($imagens);
        try {
            DB::transaction(function () use ($entrega, $texto, $arquivos) {
                $etapa = $this->resposta->travar($entrega);
                if ($entrega->ehVersaoFinal($etapa->frente->revisoes_incluidas)) {
                    throw ValidationException::withMessages(['entrega' => __('fluxo.versao_final_sem_ajustes')]);
                }
                $pedido = new PedidoAjuste;
                $pedido->forceFill([
                    'entrega_id' => $entrega->id, 'frente_id' => $etapa->frente_id, 'enviado_por' => auth()->id(),
                    'texto' => $texto, 'tipo' => 'revisao',
                    'ip' => request()->ip(), 'navegador' => mb_substr((string) request()->userAgent(), 0, 255),
                ])->save();
                $this->arquivos->registrar($pedido, $arquivos);
                Transicoes::aplicar($etapa, EstadoEtapa::AjustesEmAndamento);
                $etapa->save();
                $entrega->forceFill(['situacao' => SituacaoCard::AjustesSolicitados])->save();
                $entrega->rascunhos()->delete();
                Atividade::registrar('ajuste.enviado', $etapa, ['etapa' => $entrega->titulo, 'versao' => $entrega->versao]);
                $entrega->setRelation('etapa', $etapa);
                $this->avisos->avisarEquipe($entrega, 'ajuste');
            });
        } catch (\Throwable $erro) {
            $this->arquivos->descartar($arquivos);
            throw $erro;
        }
    }
}
