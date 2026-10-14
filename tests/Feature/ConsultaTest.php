<?php

use App\Models\Cliente;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

it('RN-81: a aba Clientes mostra serviços, fases, situação e histórico de aprovações', function () {
    Storage::fake('privado');
    Mail::fake();
    $cliente = Cliente::factory()->create(['empresa' => 'Bela Bartók']);
    $frente = servicoDoCliente($cliente);
    cardMostrado($frente->etapas()->where('titulo', 'Aprovação do cliente')->sole());

    $this->get(rotaEquipe('consulta.index'))->assertOk()->assertSee('Bela Bartók')->assertSee('Criação de sites');
    $this->get(rotaEquipe('consulta.ver', $cliente))->assertOk()
        ->assertSee('Preparo das imagens')
        ->assertSee(__('equipe.historico_aprovacoes'))
        ->assertSee('1ª revisão')
        ->assertSee('Aguardando cliente');
});

it('RN-81: a aba Clientes é somente visualização', function () {
    $cliente = Cliente::factory()->create();
    servicoDoCliente($cliente);
    actingAsEquipe();

    $pagina = $this->get(rotaEquipe('consulta.ver', $cliente))->assertOk()->getContent();
    $principal = substr($pagina, strpos($pagina, '<main'));

    expect($principal)->not->toContain('<form')->not->toContain('<input')->not->toContain('<button');
});

it('rascunhos não entram no histórico de aprovações', function () {
    Storage::fake('privado');
    $cliente = Cliente::factory()->create();
    $frente = servicoDoCliente($cliente);
    cardEmRascunho($frente->etapas()->firstOrFail(), ['nota' => 'Ainda em preparo']);

    $this->get(rotaEquipe('consulta.ver', $cliente))->assertOk()->assertDontSee('Ainda em preparo')->assertDontSee('1ª revisão');
});
