<?php

namespace App\Models;

use App\Enums\SituacaoCard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

class Arquivo extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Arquivo não pode ser alterado.'));
        static::deleting(function (Arquivo $arquivo) {
            if ($arquivo->entrega?->situacao !== SituacaoCard::Rascunho) {
                throw new LogicException('Só sai arquivo de card em rascunho.');
            }
        });
    }

    /** @return BelongsTo<Entrega, $this> */
    public function entrega(): BelongsTo
    {
        return $this->belongsTo(Entrega::class);
    }

    /** @return BelongsTo<PedidoAjuste, $this> */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(PedidoAjuste::class, 'pedido_ajuste_id');
    }

    public function entregaDeOrigem(): Entrega
    {
        return $this->entrega ?? $this->pedido->entrega;
    }

    public function permitePrevia(): bool
    {
        return in_array($this->mime, ['application/pdf', 'image/png', 'image/jpeg', 'image/webp'], true);
    }
}
