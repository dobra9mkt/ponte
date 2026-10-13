<?php

use App\Mail\ConviteAcesso;
use App\Models\Cliente;
use App\Models\Convite;
use Illuminate\Support\Facades\Mail;

function enviarAcessoECapturarToken(Cliente $cliente): string
{
    $token = null;
    test()->post(rotaEquipe('clientes.enviar-acesso', $cliente))->assertRedirect();

    Mail::assertQueued(ConviteAcesso::class, function (ConviteAcesso $email) use (&$token) {
        $token = $email->token;

        return true;
    });

    return $token;
}

beforeEach(function () {
    Mail::fake();
    actingAsEquipe();
});

it('RN-02: cadastrar cliente não envia e-mail', function () {
    $this->post(rotaEquipe('clientes.store'), [
        'empresa' => 'Escola de Música Bela Bartók',
        'contato_nome' => 'Maria',
        'email' => 'maria@exemplo.com',
    ])->assertRedirect();

    Mail::assertNothingQueued();
    Mail::assertNothingSent();
});

it('RN-02: "Enviar acesso" envia o convite para o e-mail do cliente', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();

    $this->post(rotaEquipe('clientes.enviar-acesso', $cliente))->assertRedirect();

    Mail::assertQueued(ConviteAcesso::class, fn (ConviteAcesso $email) => $email->hasTo($cliente->email));
});

it('SEG-08: o banco guarda só o hash do token', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();
    $token = enviarAcessoECapturarToken($cliente);

    expect(strlen($token))->toBe(64);
    $convite = Convite::sole();
    expect($convite->token_hash)->toBe(hash('sha256', $token))->not->toBe($token);
});

it('RN-03: convite cria a senha, entra e mostra as boas-vindas', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();
    $token = enviarAcessoECapturarToken($cliente);
    $this->post(route('sair'));

    $this->get(route('convite', $token))->assertOk()->assertSee($cliente->empresa);
    $this->post(route('convite', $token), ['password' => 'uma-senha-bem-longa', 'password_confirmation' => 'uma-senha-bem-longa'])
        ->assertRedirect(route('boas-vindas'));

    $this->assertAuthenticatedAs($cliente->usuario);
    expect($cliente->usuario->fresh()->password)->not->toBeNull();
});

it('SEG-08: convite só funciona uma vez', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();
    $token = enviarAcessoECapturarToken($cliente);
    $this->post(route('sair'));

    $this->post(route('convite', $token), ['password' => 'uma-senha-bem-longa', 'password_confirmation' => 'uma-senha-bem-longa']);
    $this->post(route('sair'));

    $this->get(route('convite', $token))->assertOk()->assertSee(__('ponte.convite.invalido'));
    $this->post(route('convite', $token), ['password' => 'outra-senha-bem-longa', 'password_confirmation' => 'outra-senha-bem-longa'])
        ->assertSee(__('ponte.convite.invalido'));
    $this->assertGuest();
});

it('RN-03: convite vence em 72 horas', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();
    $token = enviarAcessoECapturarToken($cliente);
    $this->post(route('sair'));

    $this->travel(71)->hours();
    $this->get(route('convite', $token))->assertSee($cliente->empresa);

    $this->travel(2)->hours();
    $this->get(route('convite', $token))->assertSee(__('ponte.convite.invalido'));
});

it('RN-02: reenviar invalida o link anterior', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();
    $primeiro = enviarAcessoECapturarToken($cliente);
    Mail::fake();
    $segundo = enviarAcessoECapturarToken($cliente);
    $this->post(route('sair'));

    $this->get(route('convite', $primeiro))->assertSee(__('ponte.convite.invalido'));
    $this->get(route('convite', $segundo))->assertSee($cliente->empresa);
});

it('SEG-06: token inventado tem a mesma resposta que token vencido', function () {
    $this->post(route('sair'));

    $this->get(route('convite', str_repeat('a', 64)))->assertOk()->assertSee(__('ponte.convite.invalido'));
    $this->get(route('convite', 'curto'))->assertOk()->assertSee(__('ponte.convite.invalido'));
});

it('SEG-03: senha fraca é recusada no convite', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create();
    $token = enviarAcessoECapturarToken($cliente);
    $this->post(route('sair'));

    $this->post(route('convite', $token), ['password' => 'curta', 'password_confirmation' => 'curta'])->assertSessionHasErrors('password');
    $this->assertGuest();
});

it('cliente desativado não recebe acesso', function () {
    $cliente = Cliente::factory()->comLogin(senhaCriada: false)->create(['situacao' => 'desativado']);

    $this->post(rotaEquipe('clientes.enviar-acesso', $cliente))->assertForbidden();
    Mail::assertNothingQueued();
});
