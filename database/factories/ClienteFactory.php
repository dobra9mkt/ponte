<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empresa' => fake()->company(),
            'contato_nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'telefone' => fake()->phoneNumber(),
            'endereco' => fake()->address(),
            'observacoes' => fake()->sentence(),
        ];
    }

    public function comLogin(bool $senhaCriada = true): static
    {
        return $this->afterCreating(function (Cliente $cliente) use ($senhaCriada) {
            $fabrica = User::factory()->cliente($cliente)->state(['email' => $cliente->email, 'name' => $cliente->contato_nome]);

            ($senhaCriada ? $fabrica : $fabrica->semSenha())->create();
        });
    }
}
