<?php

use App\Acoes\CriarFrente;
use App\Models\Cliente;
use App\Models\Entrega;
use App\Models\Etapa;
use App\Models\Frente;
use App\Models\Modelo;
use App\Models\User;
use Carbon\CarbonImmutable;
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

function servicoDoCliente(Cliente $cliente, string $slug = 'criacao-de-sites', ?string $inicio = null): Frente
{
    $modelo = Modelo::query()->where('slug', $slug)->with('etapas')->firstOrFail();

    return app(CriarFrente::class)($cliente, $modelo, $modelo->nome, $inicio ? CarbonImmutable::parse($inicio) : null);
}

function cardEmRascunho(Etapa $etapa, array $dados = []): Entrega
{
    actingAsEquipe();
    test()->post(rotaEquipe('aprovacoes.store', $etapa))->assertSessionHasNoErrors();
    $card = $etapa->entregas()->firstOrFail();
    test()->put(rotaEquipe('aprovacoes.update', $card), $dados + ['nota' => 'Confira o menu e as cores.'])->assertSessionHasNoErrors();

    return $card->fresh();
}

function cardMostrado(Etapa $etapa, array $dados = []): Entrega
{
    $card = cardEmRascunho($etapa, $dados);
    test()->put(rotaEquipe('aprovacoes.update', $card), $dados + ['nota' => 'Confira o menu e as cores.', 'mostrar' => '1'])->assertSessionHasNoErrors();

    return $card->fresh();
}
