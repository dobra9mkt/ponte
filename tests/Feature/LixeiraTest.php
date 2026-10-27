<?php

use App\Mail\AvisoEntrega;
use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->cliente = Cliente::factory()->comLogin()->create(['empresa' => 'Bela Bartók']);
    $this->frente = servicoDoCliente($this->cliente);
});

it('RN-82: excluir cliente pede o botão de confirmação, vai para a lixeira e bloqueia o portal', function () {
    actingAsEquipe();
    $this->get(rotaEquipe('clientes.show', $this->cliente))->assertSee(__('equipe.excluir_cliente'));

    $this->delete(rotaEquipe('clientes.destroy', $this->cliente))->assertRedirect(rotaEquipe('clientes.index'));

    expect(Cliente::withTrashed()->find($this->cliente->id)->trashed())->toBeTrue()
        ->and(Frente::query()->find($this->frente->id))->not->toBeNull();
    $this->get(rotaEquipe('clientes.show', $this->cliente))->assertNotFound();
    $this->get(rotaEquipe('clientes.index'))->assertViewHas('clientes', fn ($clientes) => ! $clientes->contains('id', $this->cliente->id));
    $this->get(rotaEquipe('consulta.index'))->assertDontSee('Bela Bartók');

    $usuario = $this->cliente->usuario()->firstOrFail();
    $this->actingAs($usuario)->get(route('inicio'))->assertRedirect(route('entrar'));
    $this->assertGuest();
    $this->post(route('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $this->assertGuest();
});

it('RN-82: restaurar cliente devolve o cadastro, os serviços e o acesso', function () {
    actingAsEquipe();
    $this->delete(rotaEquipe('clientes.destroy', $this->cliente));

    $this->get(rotaEquipe('lixeira'))->assertOk()->assertSee('Bela Bartók')->assertDontSee('Criação de sites');
    $this->post(rotaEquipe('lixeira.cliente', $this->cliente))->assertRedirect(rotaEquipe('clientes.show', $this->cliente));

    expect($this->cliente->fresh()->trashed())->toBeFalse()
        ->and($this->cliente->fresh()->frentes()->count())->toBe(1);
    $this->get(rotaEquipe('lixeira'))->assertSee(__('equipe.lixeira_vazia'));
});

it('RN-82: excluir e restaurar serviço', function () {
    actingAsEquipe();

    $this->delete(rotaEquipe('frentes.destroy', [$this->cliente, $this->frente]))->assertRedirect(rotaEquipe('clientes.show', $this->cliente));
    $this->get(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]))->assertNotFound();
    $this->get(rotaEquipe('lixeira'))->assertSee('Criação de sites')->assertDontSee('Preparo das imagens');

    $this->post(rotaEquipe('lixeira.servico', $this->frente))->assertRedirect(rotaEquipe('frentes.ver', [$this->cliente, $this->frente]));
    expect($this->frente->fresh()->trashed())->toBeFalse()
        ->and($this->frente->etapas()->count())->toBe(6);
});

it('RN-82: fase restaurada volta ao mesmo lugar', function () {
    actingAsEquipe();
    $copy = $this->frente->etapas()->where('titulo', 'Copy')->sole();

    $this->delete(rotaEquipe('etapas.destroy', $copy));
    $this->get(rotaEquipe('lixeira'))->assertSee('Copy')->assertSee('Bela Bartók · Criação de sites');
    $this->post(rotaEquipe('lixeira.fase', $copy))->assertRedirect();

    expect($this->frente->etapas()->pluck('titulo')->take(3)->all())->toBe(['Pesquisa', 'Copy', 'Preparo das imagens']);
});

it('RN-82: item de cliente excluído não se restaura sozinho', function () {
    actingAsEquipe();
    $fase = $this->frente->etapas()->firstOrFail();
    $this->delete(rotaEquipe('etapas.destroy', $fase));
    $this->delete(rotaEquipe('clientes.destroy', $this->cliente));

    $this->post(rotaEquipe('lixeira.fase', $fase))->assertForbidden();
    expect(Etapa::withTrashed()->find($fase->id)->trashed())->toBeTrue();
});

it('RN-83: aprovação de serviço excluído não gera lembrete nem alerta', function () {
    Storage::fake('privado');
    Mail::fake();
    config(['ponte.emails_equipe' => ['equipe@dobra9.com.br']]);
    $this->travelTo('2026-11-13 10:00');
    cardMostrado($this->frente->etapas()->where('titulo', 'Aprovação do cliente')->sole());
    $this->delete(rotaEquipe('frentes.destroy', [$this->cliente, $this->frente]));
    Mail::fake();

    $this->travelTo('2026-11-25 08:00');
    $this->artisan('ponte:verificar-prazos')->assertSuccessful();

    Mail::assertNotQueued(AvisoEntrega::class);
    $this->get(rotaEquipe('painel'))->assertDontSee('Aprovação do cliente');
});

it('restaurar item que não está na lixeira é recusado', function () {
    actingAsEquipe(User::factory()->equipe()->create());

    $this->post(rotaEquipe('lixeira.cliente', $this->cliente))->assertForbidden();
});

it('RN-82a: excluir definitivamente tira da lixeira, não restaura e mantém os dados no banco', function () {
    actingAsEquipe();
    $this->delete(rotaEquipe('frentes.destroy', [$this->cliente, $this->frente]))->assertRedirect();

    $this->delete(rotaEquipe('lixeira.servico.descartar', $this->frente))->assertRedirect(rotaEquipe('lixeira'));

    $this->get(rotaEquipe('lixeira'))->assertOk()->assertDontSee($this->frente->nome);
    $this->post(rotaEquipe('lixeira.servico', $this->frente))->assertForbidden();
    expect(Frente::withTrashed()->find($this->frente->id))->not->toBeNull()->descartado_em->not->toBeNull();
});

it('RN-82a: esvaziar lixeira descarta tudo; item fora da lixeira não é descartado', function () {
    actingAsEquipe();
    $fase = $this->frente->etapas()->firstOrFail();
    $this->delete(rotaEquipe('etapas.destroy', $fase))->assertRedirect();
    $this->delete(rotaEquipe('lixeira.cliente.descartar', $this->cliente))->assertForbidden();

    $this->delete(rotaEquipe('lixeira.esvaziar'))->assertRedirect(rotaEquipe('lixeira'));

    $this->get(rotaEquipe('lixeira'))->assertSee(__('equipe.lixeira_vazia'));
    expect(Etapa::withTrashed()->find($fase->id)->descartado_em)->not->toBeNull()
        ->and($this->cliente->fresh()->descartado_em)->toBeNull();
});

it('RN-82a: cliente não usa as rotas de descarte da lixeira', function () {
    $this->frente->delete();
    actingAsCliente($this->cliente);

    $this->delete(rotaEquipe('lixeira.servico.descartar', $this->frente))->assertNotFound();
    $this->delete(rotaEquipe('lixeira.esvaziar'))->assertNotFound();
});

it('RN-82a: excluir cliente definitivamente libera o e-mail para um novo cadastro', function () {
    actingAsEquipe();
    $usuario = $this->cliente->usuario()->firstOrFail();
    $email = $usuario->email;
    $this->delete(rotaEquipe('clientes.destroy', $this->cliente));

    $this->post(rotaEquipe('clientes.store'), ['empresa' => 'Outra', 'contato_nome' => 'Ana', 'email' => $email])->assertSessionHasErrors('email');

    $this->delete(rotaEquipe('lixeira.cliente.descartar', $this->cliente))->assertRedirect(rotaEquipe('lixeira'));
    expect($usuario->fresh()->email)->toBe('descartado-'.$usuario->id.':'.$email);

    $this->post(rotaEquipe('clientes.store'), ['empresa' => 'Outra', 'contato_nome' => 'Ana', 'email' => $email])->assertSessionHasNoErrors();
    expect(User::query()->where('email', $email)->sole()->cliente_id)->not->toBe($this->cliente->id);
    auth()->logout();
    $this->post(route('entrar'), ['email' => $usuario->fresh()->email, 'password' => UserFactory::SENHA]);
    $this->assertGuest();
});

it('RN-82a: esvaziar a lixeira também libera o e-mail do cliente', function () {
    actingAsEquipe();
    $email = $this->cliente->usuario()->firstOrFail()->email;
    $this->delete(rotaEquipe('clientes.destroy', $this->cliente));

    $this->delete(rotaEquipe('lixeira.esvaziar'))->assertRedirect();

    expect(User::query()->where('email', $email)->exists())->toBeFalse();
});

it('RN-82a: a publicação libera o e-mail de clientes descartados antes da regra', function () {
    $usuario = $this->cliente->usuario()->firstOrFail();
    $email = $usuario->email;
    $this->cliente->delete();
    $this->cliente->forceFill(['descartado_em' => now()])->save();
    $ativo = Cliente::factory()->comLogin()->create()->usuario()->firstOrFail();

    (require database_path('migrations/2026_10_27_000001_liberar_email_de_clientes_descartados.php'))->up();

    expect($usuario->fresh()->email)->toBe('descartado-'.$usuario->id.':'.$email)
        ->and($ativo->fresh()->email)->toBe($ativo->email);
});
