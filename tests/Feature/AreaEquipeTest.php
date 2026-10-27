<?php

use App\Models\Cliente;
use App\Models\Etapa;
use App\Models\Frente;
use App\Models\Modelo;
use App\Models\User;
use Database\Factories\UserFactory;
use PragmaRX\Google2FA\Google2FA;

dataset('rotasDaEquipe', fn () => [
    'painel' => fn () => ['get', rotaEquipe('painel')],
    'clientes' => fn () => ['get', rotaEquipe('clientes.index')],
    'novo cliente' => fn () => ['get', rotaEquipe('clientes.create')],
    'ficha' => fn () => ['get', rotaEquipe('clientes.show', Cliente::factory()->create())],
    'contrato' => fn () => ['get', rotaEquipe('clientes.contrato', Cliente::factory()->create())],
    'enviar acesso' => fn () => ['post', rotaEquipe('clientes.enviar-acesso', Cliente::factory()->create())],
    'criar serviço' => fn () => ['post', rotaEquipe('frentes.store', Cliente::factory()->create())],
    'serviço' => fn () => ['get', rotaEquipe('frentes.ver', [($f = Frente::factory()->create())->cliente_id, $f])],
    'nova fase' => fn () => ['post', rotaEquipe('etapas.store', [($f = Frente::factory()->create())->cliente_id, $f])],
    'ordem das fases' => fn () => ['put', rotaEquipe('frentes.ordem', [($f = Frente::factory()->create())->cliente_id, $f])],
    'editar fase' => fn () => ['put', rotaEquipe('etapas.update', Etapa::factory()->create())],
    'remover fase' => fn () => ['delete', rotaEquipe('etapas.destroy', Etapa::factory()->create())],
    'iniciar fase' => fn () => ['post', rotaEquipe('etapas.iniciar', Etapa::factory()->create())],
    'reabrir fase' => fn () => ['post', rotaEquipe('etapas.reabrir', Etapa::factory()->create())],
    'voltar fase para a fazer' => fn () => ['post', rotaEquipe('etapas.voltar-a-fazer', Etapa::factory()->create())],
    'adicionar aprovação' => fn () => ['post', rotaEquipe('aprovacoes.store', Etapa::factory()->create())],
    'consulta' => fn () => ['get', rotaEquipe('consulta.index')],
    'consulta do cliente' => fn () => ['get', rotaEquipe('consulta.ver', Cliente::factory()->create())],
    'serviços padrão' => fn () => ['get', rotaEquipe('servicos.index')],
    'serviço padrão' => fn () => ['get', rotaEquipe('servicos.ver', Modelo::query()->firstOrFail())],
    'novo serviço padrão' => fn () => ['post', rotaEquipe('servicos.store')],
    'excluir cliente' => fn () => ['delete', rotaEquipe('clientes.destroy', Cliente::factory()->create())],
    'excluir serviço' => fn () => ['delete', rotaEquipe('frentes.destroy', [($f = Frente::factory()->create())->cliente_id, $f])],
    'lixeira' => fn () => ['get', rotaEquipe('lixeira')],
    'restaurar cliente' => fn () => ['post', rotaEquipe('lixeira.cliente', tap(Cliente::factory()->create())->delete())],
    'conta' => fn () => ['get', rotaEquipe('conta')],
    'configurar 2fa' => fn () => ['get', rotaEquipe('2fa.configurar')],
    'inexistente' => fn () => ['get', url(config('ponte.caminho_equipe').'/qualquer-coisa')],
]);

it('SEG-01: visitante recebe 404 em qualquer rota da equipe', function (array $rota) {
    [$metodo, $url] = $rota;

    $this->{$metodo}($url)->assertNotFound();
})->with('rotasDaEquipe');

it('SEG-01: cliente logado recebe 404 em qualquer rota da equipe, inclusive na tela de entrar', function (array $rota) {
    actingAsCliente();
    [$metodo, $url] = $rota;

    $this->{$metodo}($url)->assertNotFound();
    $this->get(rotaEquipe('entrar'))->assertNotFound();
})->with('rotasDaEquipe');

it('SEG-01: caminhos óbvios de administração não existem', function (string $caminho) {
    $this->get($caminho)->assertNotFound();
})->with(['/equipe', '/admin', '/painel', '/login', '/dashboard', '/.env', '/storage/app.php']);

it('SEG-02: equipe sem 2FA só acessa a configuração do 2FA', function () {
    actingAsEquipe(User::factory()->equipe(com2fa: false)->create());

    $this->get(rotaEquipe('painel'))->assertRedirect(rotaEquipe('2fa.configurar'));
    $this->get(rotaEquipe('clientes.index'))->assertRedirect(rotaEquipe('2fa.configurar'));
    $this->get(rotaEquipe('conta'))->assertRedirect(rotaEquipe('2fa.configurar'));
    $this->get(rotaEquipe('2fa.configurar'))->assertOk()->assertSee('data:image/svg+xml;base64', false);
});

it('SEG-02: confirmar o código ativa o 2FA e mostra os códigos de recuperação', function () {
    $usuario = actingAsEquipe(User::factory()->equipe(com2fa: false)->create());
    $this->get(rotaEquipe('2fa.configurar'));

    $segredo = decrypt($usuario->fresh()->two_factor_secret);
    $codigo = (new Google2FA)->getCurrentOtp($segredo);

    $this->post(rotaEquipe('2fa.configurar'), ['codigo' => $codigo])->assertRedirect(rotaEquipe('conta'));
    expect($usuario->fresh()->hasEnabledTwoFactorAuthentication())->toBeTrue();

    $this->get(rotaEquipe('conta'))->assertOk()->assertSee($usuario->fresh()->recoveryCodes()[0]);
});

it('SEG-02: login da equipe pede o código do aplicativo antes de liberar o painel', function () {
    $usuario = User::factory()->equipe()->create();

    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA])
        ->assertRedirect(rotaEquipe('2fa'));
    $this->assertGuest();

    $this->post(rotaEquipe('2fa'), ['codigo' => '000000'])->assertSessionHasErrors('codigo');
    $this->assertGuest();

    $codigo = (new Google2FA)->getCurrentOtp(UserFactory::SEGREDO_2FA);
    $this->post(rotaEquipe('2fa'), ['codigo' => $codigo])->assertRedirect(rotaEquipe('painel'));
    $this->assertAuthenticatedAs($usuario);
});

it('SEG-02: código de recuperação funciona uma única vez', function () {
    $usuario = User::factory()->equipe()->create();
    $codigo = UserFactory::CODIGOS_RECUPERACAO[0];

    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $this->post(rotaEquipe('2fa'), ['codigo_recuperacao' => $codigo])->assertRedirect(rotaEquipe('painel'));
    expect($usuario->fresh()->recoveryCodes())->not->toContain($codigo);

    $this->post(route('sair'));
    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $this->post(rotaEquipe('2fa'), ['codigo_recuperacao' => $codigo])->assertSessionHasErrors('codigo');
    $this->assertGuest();
});

it('SEG-02: a etapa do código expira e não pode ser pulada', function () {
    $this->get(rotaEquipe('2fa'))->assertRedirect(rotaEquipe('entrar'));

    $usuario = User::factory()->equipe()->create();
    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);

    $this->travel(6)->minutes();
    $codigo = (new Google2FA)->getCurrentOtp(UserFactory::SEGREDO_2FA);
    $this->post(rotaEquipe('2fa'), ['codigo' => $codigo])->assertRedirect(rotaEquipe('entrar'));
    $this->assertGuest();
});

it('SEG-09: login de cliente no caminho da equipe falha com erro genérico', function () {
    $cliente = Cliente::factory()->comLogin()->create();

    $this->post(rotaEquipe('entrar'), ['email' => $cliente->email, 'password' => UserFactory::SENHA])
        ->assertSessionHasErrors(['email' => __('ponte.login.erro')]);
    $this->assertGuest();
});

it('SEG-09: equipe não entra pela tela do cliente', function () {
    $usuario = User::factory()->equipe()->create();

    $this->post(route('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA])
        ->assertSessionHasErrors(['email' => __('ponte.login.erro')]);
    $this->assertGuest();
});
