<?php

use App\Models\Cliente;
use App\Models\User;
use App\Notifications\RedefinirSenha;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

it('SEG-06: "esqueci a senha" responde igual para e-mail existente e inexistente', function () {
    $cliente = Cliente::factory()->comLogin()->create();

    $this->post(route('senha.pedir'), ['email' => $cliente->email])->assertSessionHas('sucesso', __('ponte.senha.email_enviado'));
    $this->post(route('senha.pedir'), ['email' => 'ninguem@exemplo.com'])->assertSessionHas('sucesso', __('ponte.senha.email_enviado'));

    Notification::assertSentTo($cliente->usuario, RedefinirSenha::class);
    Notification::assertCount(1);
});

it('equipe e cliente sem senha criada não recebem link de redefinição', function () {
    $equipe = User::factory()->equipe()->create();
    $semSenha = Cliente::factory()->comLogin(senhaCriada: false)->create();

    $this->post(route('senha.pedir'), ['email' => $equipe->email]);
    $this->post(route('senha.pedir'), ['email' => $semSenha->email]);

    Notification::assertNothingSent();
});

it('o link redefine a senha uma única vez', function () {
    $cliente = Cliente::factory()->comLogin()->create();
    $this->post(route('senha.pedir'), ['email' => $cliente->email]);

    $token = null;
    Notification::assertSentTo($cliente->usuario, RedefinirSenha::class, function (RedefinirSenha $aviso) use (&$token) {
        $token = $aviso->token;

        return true;
    });

    $dados = ['token' => $token, 'email' => $cliente->email, 'password' => 'senha-nova-bem-longa', 'password_confirmation' => 'senha-nova-bem-longa'];

    $this->post(route('senha.salvar'), $dados)->assertRedirect(route('entrar'));
    $this->post(route('entrar'), ['email' => $cliente->email, 'password' => 'senha-nova-bem-longa'])->assertRedirect(route('inicio'));
    $this->post(route('sair'));

    $this->post(route('senha.salvar'), [...$dados, 'password' => 'mais-uma-senha-longa', 'password_confirmation' => 'mais-uma-senha-longa'])
        ->assertSessionHasErrors(['email' => __('ponte.senha.link_invalido')]);
});
