<?php

namespace App\Models;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoFrente;
use Database\Factories\FrenteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property SituacaoFrente $situacao
 * @property Carbon|null $inicio_em
 * @property Carbon|null $prazo_final
 * @property Carbon|null $publicada_em
 */
#[Fillable(['nome', 'nome_cliente', 'revisoes_incluidas', 'inicio_em', 'prazo_final', 'depende_de_frente_id'])]
class Frente extends Model
{
    /** @use HasFactory<FrenteFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'descartado_em' => 'datetime',
            'situacao' => SituacaoFrente::class,
            'inicio_em' => 'date',
            'prazo_final' => 'date',
            'publicada_em' => 'datetime',
            'concluida_em' => 'datetime',
            'arquivavel_em' => 'date',
            'revisoes_incluidas' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (Frente $frente) => $frente->situacao ??= SituacaoFrente::Ativa);
    }

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return BelongsTo<Modelo, $this> */
    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class);
    }

    /** @return BelongsTo<Frente, $this> */
    public function dependeDe(): BelongsTo
    {
        return $this->belongsTo(Frente::class, 'depende_de_frente_id');
    }

    /** @return HasMany<Etapa, $this> */
    public function etapas(): HasMany
    {
        return $this->hasMany(Etapa::class)->orderBy('ordem');
    }

    /** @return HasMany<Etapa, $this> */
    /**
     * O cliente vê só as fases em andamento ou finalizadas (RN-16a).
     *
     * @return HasMany<Etapa, $this>
     */
    public function etapasVisiveis(): HasMany
    {
        return $this->etapas()->where('oculta', false)->where('interna', false)->where('estado', '!=', EstadoEtapa::AFazer);
    }

    public function estaAtiva(): bool
    {
        return $this->situacao === SituacaoFrente::Ativa;
    }

    public function revisoesUsadas(): int
    {
        return PedidoAjuste::query()->where('frente_id', $this->id)->where('conta_como_revisao', true)->count();
    }

    /**
     * @param  Collection<int, Etapa>  $etapas
     */
    public static function etapaAtual(Collection $etapas): ?Etapa
    {
        return $etapas->first(fn (Etapa $etapa) => ! $etapa->estado->finalizada());
    }

    public function nomeParaCliente(): string
    {
        return $this->nome_cliente ?: $this->nome;
    }
}
