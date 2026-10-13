<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $expira_em
 * @property Carbon|null $usado_em
 */
class Convite extends Model
{
    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'expira_em' => 'datetime',
            'usado_em' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function hashDoToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public static function validoPeloToken(string $token): ?self
    {
        if (strlen($token) !== 64) {
            return null;
        }

        return static::query()
            ->where('token_hash', static::hashDoToken($token))
            ->whereNull('usado_em')
            ->where('expira_em', '>', now())
            ->whereHas('usuario', fn (Builder $q) => $q->where('ativo', true))
            ->first();
    }
}
