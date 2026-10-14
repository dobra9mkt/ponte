<?php

namespace Database\Factories;

use App\Models\Modelo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Modelo>
 */
class ModeloFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => $nome = fake()->unique()->words(2, true),
            'slug' => Str::slug($nome),
        ];
    }
}
