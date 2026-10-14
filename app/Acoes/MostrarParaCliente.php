<?php

namespace App\Acoes;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCard;
use App\Models\Arquivo;
use App\Models\Atividade;
use App\Models\Entrega;
use App\Models\Etapa;
use App\Models\Frente;
use App\Servicos\AvisosEntrega;
use App\Servicos\DiasUteis;
use App\Servicos\Transicoes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MostrarParaCliente
{
    public function __construct(private DiasUteis $diasUteis, private AvisosEntrega $avisos) {}

    /**
     * Publica o card: conteúdo bloqueado, prazo em dias úteis, fase aguardando o cliente e aviso por e-mail (RN-72, RN-73).
     */
    public function __invoke(Entrega $card): void
    {
        DB::transaction(function () use ($card) {
            $frente = Frente::query()->with('cliente')->lockForUpdate()->findOrFail($card->etapa->frente_id);
            $etapa = Etapa::query()->lockForUpdate()->findOrFail($card->etapa_id);
            $card = Entrega::query()->with('arquivos')->lockForUpdate()->findOrFail($card->id);

            if ($card->situacao !== SituacaoCard::Rascunho || ! $frente->estaAtiva()) {
                throw ValidationException::withMessages(['card' => __('equipe.card_desatualizado')]);
            }
            if (blank($card->nota)) {
                throw ValidationException::withMessages(['nota' => __('equipe.card_precisa_texto')]);
            }

            $card->forceFill([
                'situacao' => SituacaoCard::AguardandoCliente,
                'hash_conteudo' => $this->hash($card),
                'publicada_por' => auth()->id(),
                'publicada_em' => now(),
                'responder_ate' => $this->diasUteis->somar(today(), config()->integer('ponte.prazo_resposta_dias_uteis')),
            ])->save();

            Transicoes::aplicar($etapa, EstadoEtapa::AguardandoCliente);
            $etapa->forceFill(['interna' => false, 'oculta' => false])->save();
            $frente->forceFill(['publicada_em' => $frente->publicada_em ?? now()])->save();

            $etapa->setRelation('frente', $frente);
            $card->setRelation('etapa', $etapa);
            Atividade::registrar('card.mostrado', $etapa, ['etapa' => $etapa->titulo, 'rodada' => $card->versao]);
            $this->avisos->avisarCliente($card, 'publicada');
        });
    }

    private function hash(Entrega $card): string
    {
        return hash('sha256', json_encode([
            'etapa_id' => $card->etapa_id,
            'versao' => $card->versao,
            'nota' => $card->nota,
            'links' => $card->links,
            'texto_aprovar' => $card->texto_aprovar,
            'arquivos' => $card->arquivos->map(fn (Arquivo $a) => ['nome' => $a->nome_original, 'sha256' => $a->sha256])->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }
}
