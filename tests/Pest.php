<?php

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        Http::preventStrayRequests();
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);
    })
    ->in('Feature');

function rotaEquipe(string $nome, mixed $parametros = []): string
{
    return route('equipe.'.$nome, $parametros);
}

function actingAsEquipe(?User $usuario = null): User
{
    $usuario ??= User::factory()->equipe()->create();
    test()->actingAs($usuario);

    return $usuario;
}

function actingAsCliente(?Cliente $cliente = null): User
{
    $cliente ??= Cliente::factory()->comLogin()->create();
    $usuario = $cliente->usuario()->firstOrFail();
    test()->actingAs($usuario);

    return $usuario;
}
