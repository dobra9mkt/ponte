<?php

namespace Database\Factories;

use App\Enums\EstadoEtapa;
use App\Models\Etapa;
use App\Models\Frente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Etapa>
 */
class EtapaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'frente_id' => Frente::factory(),
            'ordem' => 1,
            'titulo' => fake()->sentence(3),
            'estado' => EstadoEtapa::AFazer,
            'responsavel' => 'Rose',
            'entrada_em' => '2026-11-03',
            'termino_em' => '2026-11-10',
        ];
    }

    public function estado(EstadoEtapa $estado): static
    {
        return $this->state(fn () => ['estado' => $estado]);
    }

    public function oculta(): static
    {
        return $this->state(fn () => ['oculta' => true]);
    }
}
