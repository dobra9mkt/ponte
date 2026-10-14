<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Frente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Frente>
 */
class FrenteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'nome' => fake()->randomElement(['Criação de sites', 'Tráfego pago', 'Sistemas sob medida']),
            'revisoes_incluidas' => 3,
            'inicio_em' => '2026-11-03',
            'prazo_final' => '2026-12-18',
            'publicada_em' => now(),
        ];
    }
}
