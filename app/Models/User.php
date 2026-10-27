<?php

namespace App\Models;

use App\Enums\Papel;
use App\Notifications\RedefinirSenha;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property Papel $papel
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'papel' => Papel::class,
            'ativo' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'ultimo_acesso_em' => 'datetime',
            'boas_vindas_vistas_em' => 'datetime',
        ];
    }

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return HasMany<Convite, $this> */
    public function convites(): HasMany
    {
        return $this->hasMany(Convite::class);
    }

    public function ehEquipe(): bool
    {
        return $this->papel === Papel::Equipe;
    }

    public function ehCliente(): bool
    {
        return $this->papel === Papel::Cliente;
    }

    public function podeEntrar(): bool
    {
        if (! $this->ativo || $this->password === null) {
            return false;
        }

        return $this->ehEquipe() || $this->cliente?->estaAtivo() === true;
    }

    /** Não é um e-mail válido, então ninguém entra nem recebe nada por ele (RN-82a). */
    public static function emailDescartado(int $id, string $email): string
    {
        return mb_substr('descartado-'.$id.':'.$email, 0, 255);
    }

    public function podeRedefinirSenha(): bool
    {
        return $this->podeEntrar() && ($this->ehCliente() || $this->hasEnabledTwoFactorAuthentication());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RedefinirSenha($token));
    }
}
