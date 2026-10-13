<?php

use App\Models\Atividade;
use App\Models\Cliente;
use Database\Factories\UserFactory;

it('cliente entra com e-mail e senha e vai para o início', function () {
    $cliente = Cliente::factory()->comLogin()->create();

    $this->post(route('entrar'), ['email' => strtoupper($cliente->email), 'password' => UserFactory::SENHA])
        ->assertRedirect(route('inicio'));

    $this->assertAuthenticatedAs($cliente->usuario);
    expect(Atividade::query()->where('acao', 'entrada')->count())->toBe(1);
    $this->get(route('inicio'))->assertOk()->assertSee($cliente->empresa);
});

it('visitante é levado para entrar', function () {
    $this->get(route('inicio'))->assertRedirect(route('entrar'));
    $this->get(route('conta'))->assertRedirect(route('entrar'));
});

it('SEG-06: mesma mensagem para senha errada e e-mail inexistente', function () {
    $cliente = Cliente::factory()->comLogin()->create();

    $existente = $this->post(route('entrar'), ['email' => $cliente->email, 'password' => 'errada-errada']);
    $inexistente = $this->post(route('entrar'), ['email' => 'ninguem@exemplo.com', 'password' => 'errada-errada']);

    $existente->assertSessionHasErrors(['email' => __('ponte.login.erro')]);
    $inexistente->assertSessionHasErrors(['email' => __('ponte.login.erro')]);
    $this->assertGuest();
});

it('SEG-05: a 6ª tentativa em 1 minuto é bloqueada, mesmo com a senha certa', function () {
    $cliente = Cliente::factory()->comLogin()->create();

    foreach (range(1, 5) as $tentativa) {
        $this->post(route('entrar'), ['email' => $cliente->email, 'password' => 'errada-'.$tentativa]);
    }

    $this->post(route('entrar'), ['email' => $cliente->email, 'password' => UserFactory::SENHA])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
    expect(session('errors')->first('email'))->toContain('Muitas tentativas');

    $this->travel(61)->seconds();
    $this->post(route('entrar'), ['email' => $cliente->email, 'password' => UserFactory::SENHA])->assertRedirect(route('inicio'));
});

it('SEG-05: mais de 20 tentativas por IP são bloqueadas, mesmo trocando o e-mail', function () {
    foreach (range(1, 20) as $tentativa) {
        $this->post(route('entrar'), ['email' => "pessoa{$tentativa}@exemplo.com", 'password' => 'errada']);
    }

    $cliente = Cliente::factory()->comLogin()->create();
    $this->post(route('entrar'), ['email' => $cliente->email, 'password' => UserFactory::SENHA])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('SEG-07: o ID da sessão muda depois do login', function () {
    $cliente = Cliente::factory()->comLogin()->create();
    $this->get(route('entrar'));
    $antes = session()->getId();

    $this->post(route('entrar'), ['email' => $cliente->email, 'password' => UserFactory::SENHA]);

    expect(session()->getId())->not->toBe($antes);
});

it('RN-04: "lembrar de mim" cria o cookie de 30 dias', function () {
    $cliente = Cliente::factory()->comLogin()->create();

    $resposta = $this->post(route('entrar'), ['email' => $cliente->email, 'password' => UserFactory::SENHA, 'lembrar' => '1']);

    $cookie = collect($resposta->headers->getCookies())->first(fn ($c) => str_starts_with($c->getName(), 'remember_web_'));
    expect($cookie)->not->toBeNull();
    expect($cookie->getExpiresTime())->toBeLessThanOrEqual(now()->addDays(30)->addMinute()->getTimestamp())
        ->toBeGreaterThan(now()->addDays(29)->getTimestamp());
});

it('RN-08: cliente desativado não entra', function () {
    $cliente = Cliente::factory()->comLogin()->create(['situacao' => 'desativado']);

    $this->post(route('entrar'), ['email' => $cliente->email, 'password' => UserFactory::SENHA])
        ->assertSessionHasErrors(['email' => __('ponte.login.erro')]);
    $this->assertGuest();
});

it('RN-08: cliente desativado com a sessão aberta é desconectado', function () {
    $usuario = actingAsCliente();
    $usuario->cliente->forceFill(['situacao' => 'desativado'])->save();

    $this->get(route('inicio'))->assertRedirect(route('entrar'));
    $this->assertGuest();
});

it('cliente ainda sem senha não entra', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();

    $this->post(route('entrar'), ['email' => $cliente->email, 'password' => ''])->assertSessionHasErrors('password');
    $this->assertGuest();
});

it('C3: boas-vindas aparecem só no primeiro acesso', function () {
    $usuario = actingAsCliente();
    $usuario->forceFill(['boas_vindas_vistas_em' => null])->save();

    $this->get(route('inicio'))->assertRedirect(route('boas-vindas'));
    $this->get(route('boas-vindas'))->assertOk()->assertSee('3 revisões por serviço');
    $this->post(route('boas-vindas.concluir'))->assertRedirect(route('inicio'));
    $this->get(route('inicio'))->assertOk();
});

it('equipe logada que abre a área do cliente vai para o painel', function () {
    actingAsEquipe();

    $this->get(route('inicio'))->assertRedirect(rotaEquipe('painel'));
});

it('sair encerra a sessão', function () {
    actingAsCliente();

    $this->post(route('sair'))->assertRedirect(route('entrar'));
    $this->assertGuest();
});
