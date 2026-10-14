<?php

namespace App\Models;

use App\Enums\SituacaoCard;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

/**
 * Card de aprovação: uma rodada mostrada ao cliente (RN-70 a RN-76).
 *
 * @property SituacaoCard $situacao
 * @property list<array{rotulo: string, url: string}> $links
 * @property CarbonInterface|null $responder_ate
 * @property CarbonInterface|null $publicada_em
 */
#[Fillable(['nota', 'links', 'texto_aprovar'])]
class Entrega extends Model
{
    use SoftDeletes;

    private const ALTERAVEIS_DEPOIS_DE_PUBLICADA = [
        'situacao', 'responder_ate', 'lembrete_enviado_em', 'alerta_vencido_em', 'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'situacao' => SituacaoCard::class,
            'versao' => 'integer',
            'links' => 'array',
            'gasta_revisao' => 'boolean',
            'publicada_em' => 'datetime',
            'responder_ate' => 'date',
            'lembrete_enviado_em' => 'datetime',
            'alerta_vencido_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Entrega $card) {
            $alterados = array_diff(array_keys($card->getDirty()), self::ALTERAVEIS_DEPOIS_DE_PUBLICADA);

            if ($card->getOriginal('publicada_em') !== null && $alterados !== []) {
                throw new LogicException('Card publicado não pode ser alterado.');
            }
        });
        static::deleting(function (Entrega $card) {
            if ($card->publicada_em !== null) {
                throw new LogicException('Card publicado não pode ser apagado.');
            }
        });
    }

    /** @return BelongsTo<Etapa, $this> */
    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class);
    }

    /** @return HasMany<Arquivo, $this> */
    public function arquivos(): HasMany
    {
        return $this->hasMany(Arquivo::class)->orderBy('id');
    }

    /** @return HasOne<Aprovacao, $this> */
    public function aprovacao(): HasOne
    {
        return $this->hasOne(Aprovacao::class);
    }

    /** @return HasOne<PedidoAjuste, $this> */
    public function pedido(): HasOne
    {
        return $this->hasOne(PedidoAjuste::class)->latestOfMany();
    }

    /** @return HasMany<PedidoAjuste, $this> */
    public function pedidos(): HasMany
    {
        return $this->hasMany(PedidoAjuste::class)->orderBy('id');
    }

    /** @return HasMany<RascunhoAjuste, $this> */
    public function rascunhos(): HasMany
    {
        return $this->hasMany(RascunhoAjuste::class);
    }

    public function rodada(int $total): string
    {
        return $this->ehVersaoFinal($total) ? __('fluxo.versao_final') : $this->versao.'ª revisão';
    }

    public function rotuloParaCliente(int $total): string
    {
        return $this->ehVersaoFinal($total)
            ? __('fluxo.versao_final')
            : __('fluxo.revisao_de', ['numero' => $this->versao, 'total' => $total]);
    }

    public function rotuloSituacao(int $total): string
    {
        return $this->situacao === SituacaoCard::Aprovado && $this->ehVersaoFinal($total) ? __('fluxo.versao_final_autorizado') : $this->situacao->rotulo();
    }

    public function textoDoBotaoAprovar(int $total): string
    {
        return $this->ehVersaoFinal($total) ? ($this->texto_aprovar ?: __('fluxo.versao_final_botao')) : __('entregas.aprovar');
    }

    /** Resposta aos ajustes da última revisão: o cliente só aprova (RN-71). */
    public function ehVersaoFinal(int $total): bool
    {
        return $total > 0 && $this->versao > $total;
    }

    public function ehUltimaRevisao(int $total): bool
    {
        return $total > 0 && $this->versao === $total;
    }

    public function estaVencida(): bool
    {
        return $this->situacao === SituacaoCard::AguardandoCliente && $this->responder_ate !== null && $this->responder_ate->lt(today());
    }
}
