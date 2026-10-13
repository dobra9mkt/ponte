<?php

namespace Database\Factories;

use App\Enums\Papel;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public const SENHA = 'senha-de-teste-123';

    public const SEGREDO_2FA = 'JBSWY3DPEHPK3PXP';

    public const CODIGOS_RECUPERACAO = ['aaaaaaaaaa-1111111111', 'bbbbbbbbbb-2222222222'];

    protected static ?string $senhaHash;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$senhaHash ??= Hash::make(self::SENHA),
            'papel' => Papel::Cliente,
            'ativo' => true,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'ultimo_acesso_em' => null,
            'boas_vindas_vistas_em' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function equipe(bool $com2fa = true): static
    {
        return $this->state(fn () => array_merge(
            ['papel' => Papel::Equipe, 'cliente_id' => null],
            $com2fa ? [
                'two_factor_secret' => encrypt(self::SEGREDO_2FA),
                'two_factor_recovery_codes' => encrypt(json_encode(self::CODIGOS_RECUPERACAO)),
                'two_factor_confirmed_at' => now(),
            ] : [],
        ));
    }

    public function cliente(?Cliente $cliente = null): static
    {
        return $this->state(fn () => [
            'papel' => Papel::Cliente,
            'cliente_id' => $cliente->id ?? Cliente::factory(),
            'boas_vindas_vistas_em' => now(),
        ]);
    }

    public function semSenha(): static
    {
        return $this->state(fn () => ['password' => null]);
    }
}
