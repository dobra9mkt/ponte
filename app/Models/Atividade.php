<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

class Atividade extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'dados' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Atividade é somente inserção.'));
        static::deleting(fn () => throw new LogicException('Atividade é somente inserção.'));
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public static function registrar(string $acao, ?Model $alvo = null, array $dados = [], ?User $usuario = null, ?Cliente $cliente = null): self
    {
        $usuario ??= auth()->user();
        $request = request();

        $frente = match (true) {
            $alvo instanceof Frente => $alvo,
            $alvo instanceof Etapa => $alvo->frente,
            default => null,
        };

        $atividade = new self;
        $atividade->forceFill([
            'user_id' => $usuario?->id,
            'cliente_id' => $cliente->id ?? $frente->cliente_id ?? ($alvo instanceof Cliente ? $alvo->id : $usuario?->cliente_id),
            'frente_id' => $frente?->id,
            'acao' => $acao,
            'alvo_type' => $alvo?->getMorphClass(),
            'alvo_id' => $alvo?->getKey(),
            'dados' => $dados ?: null,
            'ip' => $request->ip(),
            'navegador' => mb_substr((string) $request->userAgent(), 0, 255),
        ])->save();

        return $atividade;
    }

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return MorphTo<Model, $this> */
    public function alvo(): MorphTo
    {
        return $this->morphTo();
    }
}
