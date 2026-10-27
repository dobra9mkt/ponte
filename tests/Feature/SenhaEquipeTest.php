<?php

use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\User;
use App\Notifications\RedefinirSenha;
use App\Notifications\SenhaRedefinida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

function linkRecebido(User $usuario): RedefinirSenha
{
    $recebido = null;
    Notification::assertSentTo($usuario, RedefinirSenha::class, function (RedefinirSenha $aviso) use (&$recebido) {
        $recebido = $aviso;

        return true;
    });

    return $recebido;
}

function dadosNovaSenha(string $token, string $email, string $senha = 'senha-nova-da-equipe'): array
{
    return ['token' => $token, 'email' => $email, 'password' => $senha, 'password_confirmation' => $senha];
}

it('RN-05a: a tela de entrar da equipe tem "Esqueci minha senha"', function () {
    $this->get(rotaEquipe('entrar'))->assertOk()->assertSee(rotaEquipe('senha.pedir'), false);
    $this->get(rotaEquipe('senha.pedir'))->assertOk();
});

it('SEG-06: o pedido da equipe responde igual, exista ou não o e-mail', function () {
    $equipe = User::factory()->equipe()->create();

    $this->post(rotaEquipe('senha.pedir'), ['email' => $equipe->email])->assertSessionHas('sucesso', __('ponte.senha.email_enviado'));
    $this->post(rotaEquipe('senha.pedir'), ['email' => 'ninguem@exemplo.com'])->assertSessionHas('sucesso', __('ponte.senha.email_enviado'));

    Notification::assertSentTo($equipe, RedefinirSenha::class);
    Notification::assertCount(1);
});

it('SEG-09a: cliente, equipe sem 2FA e equipe desativada não recebem link pela tela da equipe', function () {
    $cliente = Cliente::factory()->comLogin()->create();
    $sem2fa = User::factory()->equipe(com2fa: false)->create();
    $desativada = User::factory()->equipe()->create(['ativo' => false]);

    foreach ([$cliente->email, $sem2fa->email, $desativada->email] as $email) {
        $this->post(rotaEquipe('senha.pedir'), ['email' => $email])->assertSessionHas('sucesso', __('ponte.senha.email_enviado'));
    }

    Notification::assertNothingSent();
});

it('SEG-09: o link da equipe leva ao caminho da equipe e o do cliente continua no caminho do cliente', function () {
    $equipe = User::factory()->equipe()->create();
    $cliente = Cliente::factory()->comLogin()->create();

    $this->post(rotaEquipe('senha.pedir'), ['email' => $equipe->email]);
    $this->post(route('senha.pedir'), ['email' => $cliente->email]);

    $linkEquipe = linkRecebido($equipe);
    $linkCliente = linkRecebido($cliente->usuario);

    expect($linkEquipe->toMail($equipe)->viewData['url'])
        ->toBe(rotaEquipe('senha.redefinir', ['token' => $linkEquipe->token, 'email' => $equipe->email]))
        ->and($linkCliente->toMail($cliente->usuario)->viewData['url'])
        ->toBe(route('senha.redefinir', ['token' => $linkCliente->token, 'email' => $cliente->email]));
});

it('SEG-09a: a senha nova da equipe ainda exige o 2FA, encerra as sessões e avisa por e-mail', function () {
    $equipe = User::factory()->equipe()->create();
    $lembrarAntigo = $equipe->remember_token;
    DB::table('sessions')->insert(['id' => 'sessao-antiga', 'user_id' => $equipe->id, 'payload' => '', 'last_activity' => time()]);

    $this->post(rotaEquipe('senha.pedir'), ['email' => $equipe->email]);
    $token = linkRecebido($equipe)->token;

    $this->get(rotaEquipe('senha.redefinir', ['token' => $token, 'email' => $equipe->email]))->assertOk();
    $this->post(rotaEquipe('senha.salvar'), dadosNovaSenha($token, $equipe->email))
        ->assertRedirect(rotaEquipe('entrar'))
        ->assertSessionHas('sucesso', __('ponte.senha.redefinida'));

    $equipe->refresh();
    expect($equipe->hasEnabledTwoFactorAuthentication())->toBeTrue()
        ->and($equipe->remember_token)->not->toBe($lembrarAntigo)
        ->and(DB::table('sessions')->where('user_id', $equipe->id)->exists())->toBeFalse()
        ->and(Atividade::query()->where('acao', 'senha.redefinida')->where('user_id', $equipe->id)->exists())->toBeTrue();
    Notification::assertSentTo($equipe, SenhaRedefinida::class);

    $this->post(rotaEquipe('entrar'), ['email' => $equipe->email, 'password' => 'senha-nova-da-equipe'])->assertRedirect(rotaEquipe('2fa'));
    $this->assertGuest();
});

it('o link da equipe redefine a senha uma única vez', function () {
    $equipe = User::factory()->equipe()->create();
    $this->post(rotaEquipe('senha.pedir'), ['email' => $equipe->email]);
    $token = linkRecebido($equipe)->token;

    $this->post(rotaEquipe('senha.salvar'), dadosNovaSenha($token, $equipe->email))->assertRedirect(rotaEquipe('entrar'));
    $this->post(rotaEquipe('senha.salvar'), dadosNovaSenha($token, $equipe->email, 'outra-senha-bem-longa'))
        ->assertSessionHasErrors(['email' => __('ponte.senha.link_invalido')]);
});

it('SEG-09: link da equipe não vale na tela do cliente e link do cliente não vale na tela da equipe', function () {
    $equipe = User::factory()->equipe()->create();
    $cliente = Cliente::factory()->comLogin()->create();
    $this->post(rotaEquipe('senha.pedir'), ['email' => $equipe->email]);
    $this->post(route('senha.pedir'), ['email' => $cliente->email]);
    $senhaEquipe = $equipe->password;
    $senhaCliente = $cliente->usuario->password;

    $this->post(route('senha.salvar'), dadosNovaSenha(linkRecebido($equipe)->token, $equipe->email))
        ->assertSessionHasErrors(['email' => __('ponte.senha.link_invalido')]);
    $this->post(rotaEquipe('senha.salvar'), dadosNovaSenha(linkRecebido($cliente->usuario)->token, $cliente->email))
        ->assertSessionHasErrors(['email' => __('ponte.senha.link_invalido')]);

    expect($equipe->fresh()->password)->toBe($senhaEquipe)
        ->and($cliente->usuario->fresh()->password)->toBe($senhaCliente);
});

it('SEG-09a: equipe que perdeu o 2FA depois de pedir o link não consegue usá-lo', function () {
    $equipe = User::factory()->equipe()->create();
    $this->post(rotaEquipe('senha.pedir'), ['email' => $equipe->email]);
    $token = linkRecebido($equipe)->token;
    $equipe->forceFill(['two_factor_secret' => null, 'two_factor_confirmed_at' => null])->save();

    $this->post(rotaEquipe('senha.salvar'), dadosNovaSenha($token, $equipe->email))
        ->assertSessionHasErrors(['email' => __('ponte.senha.link_invalido')]);
});

it('SEG-01: cliente logado recebe 404 nas telas de senha da equipe', function () {
    actingAsCliente();

    $this->get(rotaEquipe('senha.pedir'))->assertNotFound();
    $this->post(rotaEquipe('senha.pedir'), ['email' => 'a@exemplo.com'])->assertNotFound();
    $this->get(rotaEquipe('senha.redefinir', ['token' => 'x']))->assertNotFound();
    $this->post(rotaEquipe('senha.salvar'))->assertNotFound();
});

it('equipe já logada vai para o painel ao abrir as telas de senha', function () {
    actingAsEquipe();

    $this->get(rotaEquipe('senha.pedir'))->assertRedirect(rotaEquipe('painel'));
});

it('a troca de senha do cliente não manda o aviso da equipe', function () {
    $cliente = Cliente::factory()->comLogin()->create();
    $this->post(route('senha.pedir'), ['email' => $cliente->email]);

    $this->post(route('senha.salvar'), dadosNovaSenha(linkRecebido($cliente->usuario)->token, $cliente->email))->assertRedirect(route('entrar'));

    Notification::assertNotSentTo($cliente->usuario, SenhaRedefinida::class);
});
