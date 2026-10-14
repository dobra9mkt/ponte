<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['titulo', 'responsavel', 'o_que_fazer', 'duracao_dias_uteis'])]
class ModeloEtapa extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return ['duracao_dias_uteis' => 'integer'];
    }

    /** @return BelongsTo<Modelo, $this> */
    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class);
    }
}
