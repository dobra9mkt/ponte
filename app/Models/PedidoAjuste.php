<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PedidoAjuste extends Model
{
    protected $table = 'pedidos_ajuste';

    protected $fillable = [];

    protected function casts(): array
    {
        return ['conta_como_revisao' => 'boolean', 'correcao_nossa_em' => 'datetime'];
    }

    /** @return BelongsTo<Entrega, $this> */
    public function entrega(): BelongsTo
    {
        return $this->belongsTo(Entrega::class);
    }

    /** @return BelongsTo<Frente, $this> */
    public function frente(): BelongsTo
    {
        return $this->belongsTo(Frente::class);
    }

    /** @return HasMany<Arquivo, $this> */
    public function arquivos(): HasMany
    {
        return $this->hasMany(Arquivo::class)->orderBy('id');
    }
}
