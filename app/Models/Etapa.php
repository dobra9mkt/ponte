<?php

namespace App\Models;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCard;
use App\Enums\TipoEtapa;
use Carbon\CarbonInterface;
use Database\Factories\EtapaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property TipoEtapa $tipo
 * @property EstadoEtapa $estado
 * @property CarbonInterface|null $entrada_em
 * @property CarbonInterface|null $termino_em
 * @property bool $interna
 * @property bool $oculta
 */
#[Fillable(['titulo', 'responsavel', 'o_que_fazer', 'entrada_em', 'termino_em'])]
class Etapa extends Model
{
    /** @use HasFactory<EtapaFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'descartado_em' => 'datetime',
            'tipo' => TipoEtapa::class,
            'estado' => EstadoEtapa::class,
            'oculta' => 'boolean',
            'interna' => 'boolean',
            'entrada_em' => 'date',
            'termino_em' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Etapa $etapa) {
            $etapa->estado ??= EstadoEtapa::AFazer;
            $etapa->tipo ??= TipoEtapa::TrabalhoEquipe;
        });
    }

    /** @return BelongsTo<Frente, $this> */
    public function frente(): BelongsTo
    {
        return $this->belongsTo(Frente::class);
    }

    /** @return HasMany<Entrega, $this> */
    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class)->orderByDesc('versao');
    }

    /** @return HasMany<Entrega, $this> */
    public function entregasPublicadas(): HasMany
    {
        return $this->entregas()->whereNotNull('publicada_em');
    }

    public function cardAberto(): ?Entrega
    {
        return $this->entregas->first(fn (Entrega $card) => $card->situacao->aberto());
    }

    public function ultimoCard(): ?Entrega
    {
        return $this->entregas->first();
    }

    public function podeReceberCard(): bool
    {
        return $this->estado !== EstadoEtapa::Concluida
            && $this->frente->estaAtiva()
            && $this->cardAberto() === null
            && ! $this->revisoesEncerradas();
    }

    /** Não existe 4ª revisão: depois da última, só a versão final em resposta aos ajustes (RN-71). */
    public function revisoesEncerradas(): bool
    {
        $incluidas = $this->frente->revisoes_incluidas;
        $versao = $this->ultimoCard()->versao ?? 0;

        return $incluidas > 0 && ($versao > $incluidas || ($versao === $incluidas && ! $this->proximoEhVersaoFinal()));
    }

    public function proximoEhVersaoFinal(): bool
    {
        $ultimo = $this->ultimoCard();

        return $ultimo?->situacao === SituacaoCard::AjustesSolicitados && $ultimo->versao === $this->frente->revisoes_incluidas;
    }

    public function podeSerConcluida(): bool
    {
        return $this->estado === EstadoEtapa::EmProducao && ! $this->entregas()->where('situacao', SituacaoCard::Rascunho)->exists();
    }

    /** Voltar para "A fazer" esconde a fase do cliente, então só vale se nada dela foi mostrado a ele (RN-14a). */
    public function podeVoltarParaAFazer(): bool
    {
        return $this->estado === EstadoEtapa::EmProducao
            && ! $this->entregas()->withTrashed()->where('situacao', '!=', SituacaoCard::Rascunho)->exists();
    }
}
