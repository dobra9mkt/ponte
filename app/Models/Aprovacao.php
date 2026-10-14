<?php

namespace App\Models;

use App\Models\Concerns\SomenteInsercao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aprovacao extends Model
{
    use SomenteInsercao;

    public const UPDATED_AT = null;

    protected $table = 'aprovacoes';

    protected $fillable = [];

    protected function casts(): array
    {
        return ['aprovado_em' => 'datetime', 'confirmacao_cumprida' => 'boolean'];
    }

    /** @return BelongsTo<Entrega, $this> */
    public function entrega(): BelongsTo
    {
        return $this->belongsTo(Entrega::class);
    }

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
